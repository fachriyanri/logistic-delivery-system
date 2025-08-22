<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class SecurityFilter implements FilterInterface
{
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
        helper('security');
        
        // Clean POST data
        if ($request->getMethod() === 'post') {
            $postData = $request->getPost();
            if (!empty($postData)) {
                $cleanedData = xss_clean($postData);
                $request->setGlobal('post', $cleanedData);
            }
        }
        
        // Clean GET data
        $getData = $request->getGet();
        if (!empty($getData)) {
            $cleanedData = xss_clean($getData);
            $request->setGlobal('get', $cleanedData);
        }
        
        // Add security headers
        $response = service('response');
        
        // Prevent clickjacking
        $response->setHeader('X-Frame-Options', 'DENY');
        
        // Prevent MIME type sniffing
        $response->setHeader('X-Content-Type-Options', 'nosniff');
        
        // Enable XSS protection
        $response->setHeader('X-XSS-Protection', '1; mode=block');
        
        // Referrer policy
        $response->setHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        
        // Permissions policy
        $response->setHeader('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');
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
        // Additional security headers can be added here if needed
    }
}