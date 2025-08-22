<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;
use Config\Database;

class PerformanceService
{
    protected BaseConnection $db;
    protected CacheService $cache;
    
    public function __construct()
    {
        $this->db = Database::connect();
        $this->cache = new CacheService();
    }

    /**
     * Optimize database queries with caching
     */
    public function optimizeQuery(string $cacheKey, callable $queryCallback, int $ttl = 3600): array
    {
        return $this->cache->remember($cacheKey, $ttl, $queryCallback);
    }

    /**
     * Get optimized shipment list with pagination
     */
    public function getOptimizedShipmentList(int $page = 1, int $perPage = 20, array $filters = []): array
    {
        $cacheKey = 'shipments_list_' . md5(serialize($filters));
        
        return $this->cache->cachePaginatedResults($cacheKey, $page, $perPage, function() use ($filters, $page, $perPage) {
            $builder = $this->db->table('pengiriman p');
            $builder->select('p.*, pel.nama_pelanggan, k.nama_kurir');
            $builder->join('pelanggan pel', 'p.id_pelanggan = pel.id_pelanggan');
            $builder->join('kurir k', 'p.id_kurir = k.id_kurir');
            
            // Apply filters
            if (!empty($filters['date_from'])) {
                $builder->where('p.tanggal >=', $filters['date_from']);
            }
            if (!empty($filters['date_to'])) {
                $builder->where('p.tanggal <=', $filters['date_to']);
            }
            if (!empty($filters['status'])) {
                $builder->where('p.status', $filters['status']);
            }
            if (!empty($filters['customer'])) {
                $builder->where('p.id_pelanggan', $filters['customer']);
            }
            
            // Get total count for pagination
            $totalBuilder = clone $builder;
            $total = $totalBuilder->countAllResults(false);
            
            // Get paginated results
            $offset = ($page - 1) * $perPage;
            $results = $builder->orderBy('p.tanggal', 'DESC')
                             ->limit($perPage, $offset)
                             ->get()
                             ->getResultArray();
            
            return [
                'data' => $results,
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
                'total_pages' => ceil($total / $perPage)
            ];
        });
    }

    /**
     * Get optimized dropdown data
     */
    public function getOptimizedDropdownData(string $type): array
    {
        return match($type) {
            'categories' => $this->cache->remember('dropdown_categories', 3600, function() {
                return $this->db->table('kategori')
                               ->select('id_kategori as value, nama_kategori as label')
                               ->orderBy('nama_kategori')
                               ->get()
                               ->getResultArray();
            }),
            
            'customers' => $this->cache->remember('dropdown_customers', 1800, function() {
                return $this->db->table('pelanggan')
                               ->select('id_pelanggan as value, nama_pelanggan as label')
                               ->orderBy('nama_pelanggan')
                               ->get()
                               ->getResultArray();
            }),
            
            'couriers' => $this->cache->remember('dropdown_couriers', 3600, function() {
                return $this->db->table('kurir')
                               ->select('id_kurir as value, nama_kurir as label')
                               ->orderBy('nama_kurir')
                               ->get()
                               ->getResultArray();
            }),
            
            'items' => $this->cache->remember('dropdown_items', 1800, function() {
                return $this->db->table('barang b')
                               ->select('b.id_barang as value, CONCAT(b.nama_barang, " (", k.nama_kategori, ")") as label')
                               ->join('kategori k', 'b.id_kategori = k.id_kategori')
                               ->orderBy('b.nama_barang')
                               ->get()
                               ->getResultArray();
            }),
            
            default => []
        };
    }

    /**
     * Optimize search queries
     */
    public function optimizedSearch(string $query, string $type, int $limit = 10): array
    {
        $cacheKey = "search_{$type}_" . md5(strtolower($query));
        
        return $this->cache->remember($cacheKey, 600, function() use ($query, $type, $limit) {
            $searchTerm = "%{$query}%";
            
            return match($type) {
                'customers' => $this->db->table('pelanggan')
                                       ->select('id_pelanggan, nama_pelanggan, alamat')
                                       ->like('nama_pelanggan', $query)
                                       ->orLike('alamat', $query)
                                       ->limit($limit)
                                       ->get()
                                       ->getResultArray(),
                
                'items' => $this->db->table('barang b')
                                   ->select('b.id_barang, b.nama_barang, k.nama_kategori')
                                   ->join('kategori k', 'b.id_kategori = k.id_kategori')
                                   ->like('b.nama_barang', $query)
                                   ->limit($limit)
                                   ->get()
                                   ->getResultArray(),
                
                'shipments' => $this->db->table('pengiriman p')
                                       ->select('p.id_pengiriman, p.tanggal, pel.nama_pelanggan')
                                       ->join('pelanggan pel', 'p.id_pelanggan = pel.id_pelanggan')
                                       ->like('p.id_pengiriman', $query)
                                       ->orLike('pel.nama_pelanggan', $query)
                                       ->limit($limit)
                                       ->get()
                                       ->getResultArray(),
                
                default => []
            };
        });
    }

    /**
     * Batch operations for better performance
     */
    public function batchInsert(string $table, array $data): bool
    {
        if (empty($data)) {
            return false;
        }
        
        try {
            // Use batch insert for better performance
            $result = $this->db->table($table)->insertBatch($data);
            
            // Invalidate related cache
            $this->invalidateCacheForTable($table);
            
            return $result !== false;
        } catch (\Exception $e) {
            log_message('error', 'Batch insert failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Batch update operations
     */
    public function batchUpdate(string $table, array $data, string $keyField): bool
    {
        if (empty($data)) {
            return false;
        }
        
        try {
            $result = $this->db->table($table)->updateBatch($data, $keyField);
            
            // Invalidate related cache
            $this->invalidateCacheForTable($table);
            
            return $result !== false;
        } catch (\Exception $e) {
            log_message('error', 'Batch update failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get performance metrics
     */
    public function getPerformanceMetrics(): array
    {
        return [
            'memory_usage' => [
                'current' => memory_get_usage(true),
                'peak' => memory_get_peak_usage(true),
                'formatted_current' => $this->formatBytes(memory_get_usage(true)),
                'formatted_peak' => $this->formatBytes(memory_get_peak_usage(true))
            ],
            'execution_time' => microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],
            'database_queries' => $this->db->getQueryCount(),
            'cache_stats' => $this->cache->getCacheStats()
        ];
    }

    /**
     * Optimize images for web delivery
     */
    public function optimizeImage(string $imagePath, int $maxWidth = 1200, int $quality = 85): bool
    {
        if (!file_exists($imagePath)) {
            return false;
        }
        
        $imageInfo = getimagesize($imagePath);
        if (!$imageInfo) {
            return false;
        }
        
        [$width, $height, $type] = $imageInfo;
        
        // Skip if image is already small enough
        if ($width <= $maxWidth) {
            return true;
        }
        
        // Calculate new dimensions
        $newWidth = $maxWidth;
        $newHeight = intval($height * ($maxWidth / $width));
        
        // Create image resource based on type
        $sourceImage = match($type) {
            IMAGETYPE_JPEG => imagecreatefromjpeg($imagePath),
            IMAGETYPE_PNG => imagecreatefrompng($imagePath),
            IMAGETYPE_GIF => imagecreatefromgif($imagePath),
            default => null
        };
        
        if (!$sourceImage) {
            return false;
        }
        
        // Create new image
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG and GIF
        if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_GIF) {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
            imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
        }
        
        // Resize image
        imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        
        // Save optimized image
        $result = match($type) {
            IMAGETYPE_JPEG => imagejpeg($newImage, $imagePath, $quality),
            IMAGETYPE_PNG => imagepng($newImage, $imagePath, 9),
            IMAGETYPE_GIF => imagegif($newImage, $imagePath),
            default => false
        };
        
        // Clean up memory
        imagedestroy($sourceImage);
        imagedestroy($newImage);
        
        return $result;
    }

    /**
     * Preload critical resources
     */
    public function preloadCriticalResources(): void
    {
        // Preload frequently accessed data
        $this->cache->cacheFrequentData();
        
        // Preload dashboard stats
        $this->cache->cacheDashboardStats();
        
        // Preload dropdown data
        $this->getOptimizedDropdownData('categories');
        $this->getOptimizedDropdownData('customers');
        $this->getOptimizedDropdownData('couriers');
    }

    /**
     * Database query optimization suggestions
     */
    public function analyzeQueryPerformance(): array
    {
        $suggestions = [];
        
        // Check for missing indexes
        $slowQueries = $this->getSlowQueries();
        foreach ($slowQueries as $query) {
            if (strpos($query, 'WHERE') !== false && strpos($query, 'INDEX') === false) {
                $suggestions[] = "Consider adding index for query: " . substr($query, 0, 100) . "...";
            }
        }
        
        // Check table sizes
        $tableSizes = $this->getTableSizes();
        foreach ($tableSizes as $table => $size) {
            if ($size > 1000000) { // More than 1M rows
                $suggestions[] = "Table '{$table}' is large ({$size} rows). Consider partitioning or archiving old data.";
            }
        }
        
        return $suggestions;
    }

    /**
     * Invalidate cache for specific table
     */
    private function invalidateCacheForTable(string $table): void
    {
        $cacheType = match($table) {
            'kategori' => 'categories',
            'pelanggan' => 'customers',
            'kurir' => 'couriers',
            'barang' => 'items',
            'pengiriman', 'detail_pengiriman' => 'shipments',
            default => null
        };
        
        if ($cacheType) {
            $this->cache->invalidateDataCache($cacheType);
        }
    }

    /**
     * Get slow queries (mock implementation)
     */
    private function getSlowQueries(): array
    {
        // In a real implementation, this would query the MySQL slow query log
        // or use performance_schema tables
        return [];
    }

    /**
     * Get table sizes
     */
    private function getTableSizes(): array
    {
        try {
            $query = "SELECT table_name, table_rows 
                     FROM information_schema.tables 
                     WHERE table_schema = ? 
                     ORDER BY table_rows DESC";
            
            $result = $this->db->query($query, [$this->db->getDatabase()]);
            
            $sizes = [];
            foreach ($result->getResultArray() as $row) {
                $sizes[$row['table_name']] = (int) $row['table_rows'];
            }
            
            return $sizes;
        } catch (\Exception $e) {
            log_message('error', 'Failed to get table sizes: ' . $e->getMessage());
            return [];
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