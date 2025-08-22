<?php

namespace App\Services;

use CodeIgniter\Cache\CacheInterface;
use Config\Services;

class CacheService
{
    protected CacheInterface $cache;
    protected int $defaultTTL = 3600; // 1 hour default
    
    public function __construct()
    {
        $this->cache = Services::cache();
    }

    /**
     * Remember a value in cache or execute callback if not cached
     */
    public function remember(string $key, int $ttl, callable $callback): mixed
    {
        $cachedValue = $this->cache->get($key);
        
        if ($cachedValue !== null) {
            return $cachedValue;
        }
        
        $value = $callback();
        $this->cache->save($key, $value, $ttl);
        
        return $value;
    }

    /**
     * Cache frequently accessed data
     */
    public function cacheFrequentData(): void
    {
        // Cache user levels for role checking
        $this->remember('user_levels', 86400, function() {
            return [
                USER_LEVEL_ADMIN => 'Administrator',
                USER_LEVEL_FINANCE => 'Finance',
                USER_LEVEL_GUDANG => 'Warehouse'
            ];
        });
        
        // Cache categories for dropdown lists
        $this->remember('categories_list', 3600, function() {
            $db = \Config\Database::connect();
            return $db->table('kategori')
                     ->select('id_kategori, nama_kategori')
                     ->orderBy('nama_kategori')
                     ->get()
                     ->getResultArray();
        });
        
        // Cache couriers for dropdown lists
        $this->remember('couriers_list', 3600, function() {
            $db = \Config\Database::connect();
            return $db->table('kurir')
                     ->select('id_kurir, nama_kurir')
                     ->orderBy('nama_kurir')
                     ->get()
                     ->getResultArray();
        });
        
        // Cache customers for dropdown lists
        $this->remember('customers_list', 1800, function() {
            $db = \Config\Database::connect();
            return $db->table('pelanggan')
                     ->select('id_pelanggan, nama_pelanggan')
                     ->orderBy('nama_pelanggan')
                     ->get()
                     ->getResultArray();
        });
    }

    /**
     * Cache dashboard statistics
     */
    public function cacheDashboardStats(): array
    {
        return $this->remember('dashboard_stats', 300, function() {
            $db = \Config\Database::connect();
            
            $stats = [];
            
            // Total shipments
            $stats['total_shipments'] = $db->table('pengiriman')->countAllResults();
            
            // Shipments this month
            $stats['monthly_shipments'] = $db->table('pengiriman')
                ->where('MONTH(tanggal)', date('m'))
                ->where('YEAR(tanggal)', date('Y'))
                ->countAllResults();
            
            // Pending shipments
            $stats['pending_shipments'] = $db->table('pengiriman')
                ->where('status', 0)
                ->countAllResults();
            
            // Completed shipments
            $stats['completed_shipments'] = $db->table('pengiriman')
                ->where('status', 1)
                ->countAllResults();
            
            // Total customers
            $stats['total_customers'] = $db->table('pelanggan')->countAllResults();
            
            // Total couriers
            $stats['total_couriers'] = $db->table('kurir')->countAllResults();
            
            // Total items
            $stats['total_items'] = $db->table('barang')->countAllResults();
            
            // Recent shipments (last 7 days)
            $stats['recent_shipments'] = $db->table('pengiriman p')
                ->select('p.id_pengiriman, p.tanggal, pel.nama_pelanggan, k.nama_kurir, p.status')
                ->join('pelanggan pel', 'p.id_pelanggan = pel.id_pelanggan')
                ->join('kurir k', 'p.id_kurir = k.id_kurir')
                ->where('p.tanggal >=', date('Y-m-d', strtotime('-7 days')))
                ->orderBy('p.tanggal', 'DESC')
                ->limit(10)
                ->get()
                ->getResultArray();
            
            return $stats;
        });
    }

    /**
     * Cache shipment reports data
     */
    public function cacheShipmentReports(string $dateFrom, string $dateTo): array
    {
        $cacheKey = "shipment_reports_{$dateFrom}_{$dateTo}";
        
        return $this->remember($cacheKey, 1800, function() use ($dateFrom, $dateTo) {
            $db = \Config\Database::connect();
            
            return $db->table('pengiriman p')
                ->select('p.*, pel.nama_pelanggan, k.nama_kurir')
                ->join('pelanggan pel', 'p.id_pelanggan = pel.id_pelanggan')
                ->join('kurir k', 'p.id_kurir = k.id_kurir')
                ->where('p.tanggal >=', $dateFrom)
                ->where('p.tanggal <=', $dateTo)
                ->orderBy('p.tanggal', 'DESC')
                ->get()
                ->getResultArray();
        });
    }

    /**
     * Cache user session data
     */
    public function cacheUserSession(string $userId, array $userData): void
    {
        $cacheKey = "user_session_{$userId}";
        $this->cache->save($cacheKey, $userData, 7200); // 2 hours
    }

    /**
     * Get cached user session data
     */
    public function getCachedUserSession(string $userId): ?array
    {
        $cacheKey = "user_session_{$userId}";
        return $this->cache->get($cacheKey);
    }

    /**
     * Invalidate cache by pattern
     */
    public function invalidatePattern(string $pattern): void
    {
        // This would depend on the cache driver implementation
        // For file cache, we'd need to scan and delete matching files
        // For Redis/Memcached, we'd use their pattern deletion features
        
        if (method_exists($this->cache, 'deleteMatching')) {
            $this->cache->deleteMatching($pattern);
        } else {
            // Fallback: clear all cache (not ideal but safe)
            log_message('warning', "Cache pattern invalidation not supported, clearing all cache for pattern: {$pattern}");
        }
    }

    /**
     * Clear specific cache keys when data changes
     */
    public function invalidateDataCache(string $type, ?string $id = null): void
    {
        switch ($type) {
            case 'categories':
                $this->cache->delete('categories_list');
                break;
                
            case 'couriers':
                $this->cache->delete('couriers_list');
                break;
                
            case 'customers':
                $this->cache->delete('customers_list');
                break;
                
            case 'shipments':
                $this->cache->delete('dashboard_stats');
                $this->invalidatePattern('shipment_reports_*');
                break;
                
            case 'dashboard':
                $this->cache->delete('dashboard_stats');
                break;
                
            case 'user_session':
                if ($id) {
                    $this->cache->delete("user_session_{$id}");
                }
                break;
        }
    }

    /**
     * Warm up cache with frequently accessed data
     */
    public function warmUpCache(): void
    {
        // Pre-load frequently accessed data
        $this->cacheFrequentData();
        $this->cacheDashboardStats();
        
        log_message('info', 'Cache warmed up successfully');
    }

    /**
     * Get cache statistics
     */
    public function getCacheStats(): array
    {
        $info = $this->cache->getCacheInfo();
        
        return [
            'cache_info' => $info,
            'is_supported' => $this->cache->isSupported(),
            'cache_directory' => WRITEPATH . 'cache/',
        ];
    }

    /**
     * Clean expired cache entries
     */
    public function cleanExpiredCache(): bool
    {
        return $this->cache->clean();
    }

    /**
     * Set default TTL
     */
    public function setDefaultTTL(int $seconds): self
    {
        $this->defaultTTL = $seconds;
        return $this;
    }

    /**
     * Cache paginated results
     */
    public function cachePaginatedResults(string $baseKey, int $page, int $perPage, callable $callback): array
    {
        $cacheKey = "{$baseKey}_page_{$page}_per_{$perPage}";
        
        return $this->remember($cacheKey, 900, $callback); // 15 minutes
    }

    /**
     * Cache search results
     */
    public function cacheSearchResults(string $query, string $type, callable $callback): array
    {
        $cacheKey = "search_{$type}_" . md5($query);
        
        return $this->remember($cacheKey, 600, $callback); // 10 minutes
    }
}