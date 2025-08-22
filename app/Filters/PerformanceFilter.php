<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class PerformanceFilter implements FilterInterface
{
    private float $startTime;
    private int $startMemory;

    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Record start time and memory usage
        $this->startTime = microtime(true);
        $this->startMemory = memory_get_usage(true);
        
        // Set performance headers
        $response = service('response');
        
        // Enable compression if supported
        if (strpos($request->getHeaderLine('Accept-Encoding'), 'gzip') !== false) {
            $response->setHeader('Content-Encoding', 'gzip');
        }
        
        // Set cache headers for static assets
        $uri = $request->getUri()->getPath();
        if (preg_match('/\.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$/i', $uri)) {
            $response->setHeader('Cache-Control', 'public, max-age=31536000'); // 1 year
            $response->setHeader('Expires', gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
        }
        
        // Add performance hints
        $response->setHeader('X-DNS-Prefetch-Control', 'on');
        
        // Store start metrics in request for later use
        $request->startTime = $this->startTime;
        $request->startMemory = $this->startMemory;
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Calculate performance metrics
        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);
        
        $executionTime = $endTime - $this->startTime;
        $memoryUsage = $endMemory - $this->startMemory;
        $peakMemory = memory_get_peak_usage(true);
        
        // Add performance headers (only in development)
        if (ENVIRONMENT === 'development') {
            $response->setHeader('X-Execution-Time', number_format($executionTime * 1000, 2) . 'ms');
            $response->setHeader('X-Memory-Usage', $this->formatBytes($memoryUsage));
            $response->setHeader('X-Peak-Memory', $this->formatBytes($peakMemory));
            
            // Add database query count if available
            $db = \Config\Database::connect();
            if (method_exists($db, 'getQueryCount')) {
                $response->setHeader('X-Database-Queries', $db->getQueryCount());
            }
        }
        
        // Log performance metrics for slow requests
        if ($executionTime > 2.0) { // Log requests taking more than 2 seconds
            $this->logSlowRequest($request, $executionTime, $memoryUsage, $peakMemory);
        }
        
        // Add performance monitoring data to response (for AJAX requests)
        if ($request->isAJAX() && ENVIRONMENT === 'development') {
            $performanceData = [
                'execution_time' => $executionTime,
                'memory_usage' => $memoryUsage,
                'peak_memory' => $peakMemory,
                'formatted' => [
                    'execution_time' => number_format($executionTime * 1000, 2) . 'ms',
                    'memory_usage' => $this->formatBytes($memoryUsage),
                    'peak_memory' => $this->formatBytes($peakMemory)
                ]
            ];
            
            // Add to response body if it's JSON
            $contentType = $response->getHeaderLine('Content-Type');
            if (strpos($contentType, 'application/json') !== false) {
                $body = $response->getBody();
                $data = json_decode($body, true);
                
                if (is_array($data)) {
                    $data['_performance'] = $performanceData;
                    $response->setBody(json_encode($data));
                }
            }
        }
        
        // Optimize response
        $this->optimizeResponse($response);
    }

    /**
     * Log slow requests for analysis
     */
    private function logSlowRequest(RequestInterface $request, float $executionTime, int $memoryUsage, int $peakMemory): void
    {
        $logData = [
            'url' => (string) $request->getUri(),
            'method' => $request->getMethod(),
            'execution_time' => $executionTime,
            'memory_usage' => $memoryUsage,
            'peak_memory' => $peakMemory,
            'user_agent' => $request->getHeaderLine('User-Agent'),
            'ip_address' => $request->getIPAddress(),
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        log_message('warning', 'Slow Request: ' . json_encode($logData));
    }

    /**
     * Optimize response for better performance
     */
    private function optimizeResponse(ResponseInterface $response): void
    {
        // Remove unnecessary headers
        $response->removeHeader('X-Powered-By');
        $response->removeHeader('Server');
        
        // Add security and performance headers
        $response->setHeader('X-Content-Type-Options', 'nosniff');
        $response->setHeader('X-Frame-Options', 'DENY');
        $response->setHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Compress response if possible and not already compressed
        $body = $response->getBody();
        $contentEncoding = $response->getHeaderLine('Content-Encoding');
        
        if (strlen($body) > 1024 && empty($contentEncoding) && function_exists('gzencode')) {
            $acceptEncoding = $_SERVER['HTTP_ACCEPT_ENCODING'] ?? '';
            
            if (strpos($acceptEncoding, 'gzip') !== false) {
                $compressedBody = gzencode($body, 6);
                if ($compressedBody !== false && strlen($compressedBody) < strlen($body)) {
                    $response->setBody($compressedBody);
                    $response->setHeader('Content-Encoding', 'gzip');
                    $response->setHeader('Content-Length', strlen($compressedBody));
                }
            }
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}