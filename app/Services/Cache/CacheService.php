<?php

namespace App\Services\Cache;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class CacheService
{
    private const DEFAULT_TTL = 3600; // 1 hour
    private const LONG_TTL = 86400; // 24 hours
    private const SHORT_TTL = 300; // 5 minutes

    /**
     * Cache dashboard statistics
     */
    public function getDashboardStats(): array
    {
        return Cache::remember('dashboard.stats', self::SHORT_TTL, function () {
            return $this->calculateDashboardStats();
        });
    }

    /**
     * Cache client financial data
     */
    public function getClientFinancials(int $clientId): array
    {
        return Cache::remember("client.{$clientId}.financials", self::DEFAULT_TTL, function () use ($clientId) {
            return $this->calculateClientFinancials($clientId);
        });
    }

    /**
     * Cache project metrics
     */
    public function getProjectMetrics(int $projectId): array
    {
        return Cache::remember("project.{$projectId}.metrics", self::DEFAULT_TTL, function () use ($projectId) {
            return $this->calculateProjectMetrics($projectId);
        });
    }

    /**
     * Cache revenue chart data
     */
    public function getRevenueChartData(int $months = 6): array
    {
        return Cache::remember("revenue.chart.{$months}months", self::DEFAULT_TTL, function () use ($months) {
            return $this->calculateRevenueChartData($months);
        });
    }

    /**
     * Cache global search results temporarily
     */
    public function getSearchResults(string $term, int $limit = 50): array
    {
        $key = 'search.' . md5($term) . ".{$limit}";
        
        return Cache::remember($key, 60, function () use ($term, $limit) { // Very short cache for search
            return $this->performGlobalSearch($term, $limit);
        });
    }

    /**
     * Cache expensive aggregations with fallback for environments without tag support
     */
    public function getClientsList(array $filters = []): array
    {
        $key = 'clients.list.' . md5(serialize($filters));
        
        // Use tags if supported, otherwise regular cache
        if ($this->supportsTags()) {
            return Cache::tags(['clients', 'projects', 'events'])
                ->remember($key, self::DEFAULT_TTL, function () use ($filters) {
                    return $this->getClientsWithFinancials($filters);
                });
        }
        
        return Cache::remember($key, self::DEFAULT_TTL, function () use ($filters) {
            return $this->getClientsWithFinancials($filters);
        });
    }

    /**
     * Cache project list with financial data
     */
    public function getProjectsList(array $filters = []): array
    {
        $key = 'projects.list.' . md5(serialize($filters));
        
        // Use tags if supported, otherwise regular cache
        if ($this->supportsTags()) {
            return Cache::tags(['projects', 'events'])
                ->remember($key, self::DEFAULT_TTL, function () use ($filters) {
                    return $this->getProjectsWithFinancials($filters);
                });
        }
        
        return Cache::remember($key, self::DEFAULT_TTL, function () use ($filters) {
            return $this->getProjectsWithFinancials($filters);
        });
    }

    /**
     * Cache financial reports
     */
    public function getFinancialReport($startDate = null, $endDate = null): array
    {
        $key = 'financial.report.' . md5($startDate . $endDate);
        
        return Cache::remember($key, self::LONG_TTL, function () use ($startDate, $endDate) {
            return $this->calculateFinancialReport($startDate, $endDate);
        });
    }

    /**
     * Invalidate cache by tags or manual key cleanup
     */
    public function invalidateClientCache(int $clientId): void
    {
        Cache::forget("client.{$clientId}.financials");
        
        if ($this->supportsTags()) {
            Cache::tags(['clients'])->flush();
        } else {
            // Manual cleanup for systems without tag support
            $this->flushCacheByPattern('clients.list.*');
        }
        
        $this->invalidateDashboardCache();
    }

    /**
     * Invalidate project cache
     */
    public function invalidateProjectCache(int $projectId): void
    {
        $project = \App\Models\Project::find($projectId);
        if ($project) {
            Cache::forget("project.{$projectId}.metrics");
            Cache::forget("client.{$project->client_id}.financials");
            
            if ($this->supportsTags()) {
                Cache::tags(['projects'])->flush();
            } else {
                // Manual cleanup for systems without tag support
                $this->flushCacheByPattern('projects.list.*');
            }
            
            $this->invalidateDashboardCache();
        }
    }

    /**
     * Invalidate event cache
     */
    public function invalidateEventCache(int $eventId): void
    {
        $event = \App\Models\Event::with('project')->find($eventId);
        if ($event) {
            $this->invalidateProjectCache($event->project_id);
            $this->invalidateClientCache($event->project->client_id);
            
            if ($this->supportsTags()) {
                Cache::tags(['events'])->flush();
            } else {
                // Events affect both clients and projects lists
                $this->flushCacheByPattern('clients.list.*');
                $this->flushCacheByPattern('projects.list.*');
            }
        }
    }

    /**
     * Invalidate dashboard cache
     */
    public function invalidateDashboardCache(): void
    {
        Cache::forget('dashboard.stats');
        Cache::forget('revenue.chart.6months');
        Cache::forget('revenue.chart.12months');
    }

    /**
     * Warm up critical caches
     */
    public function warmUpCache(): void
    {
        // Warm up dashboard
        $this->getDashboardStats();
        
        // Warm up revenue charts
        $this->getRevenueChartData(6);
        $this->getRevenueChartData(12);
        
        // Warm up recent clients and projects
        $this->getClientsList(['limit' => 50]);
        $this->getProjectsList(['limit' => 50]);
        
        // Warm up top clients' financial data
        $topClients = \App\Models\Client::withOptimizedRelations()
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();
            
        foreach ($topClients as $client) {
            $this->getClientFinancials($client->id);
        }
        
        // Warm up active projects metrics
        $activeProjects = \App\Models\Project::active()
            ->limit(20)
            ->get();
            
        foreach ($activeProjects as $project) {
            $this->getProjectMetrics($project->id);
        }
    }

    /**
     * Get cache statistics
     */
    public function getCacheStats(): array
    {
        $keys = [
            'dashboard.stats',
            'revenue.chart.6months',
            'revenue.chart.12months',
        ];
        
        $stats = [
            'total_keys' => 0,
            'memory_usage' => 0,
            'hit_rate' => 0,
            'keys_by_type' => [
                'dashboard' => 0,
                'clients' => 0,
                'projects' => 0,
                'events' => 0,
                'revenue' => 0,
                'search' => 0,
            ],
        ];
        
        try {
            if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                $redis = Redis::connection();
                $allKeys = $redis->keys('laravel_cache:*');
                $stats['total_keys'] = count($allKeys);
                
                foreach ($allKeys as $key) {
                    $keyName = str_replace('laravel_cache:', '', $key);
                    
                    if (str_contains($keyName, 'dashboard')) {
                        $stats['keys_by_type']['dashboard']++;
                    } elseif (str_contains($keyName, 'client')) {
                        $stats['keys_by_type']['clients']++;
                    } elseif (str_contains($keyName, 'project')) {
                        $stats['keys_by_type']['projects']++;
                    } elseif (str_contains($keyName, 'event')) {
                        $stats['keys_by_type']['events']++;
                    } elseif (str_contains($keyName, 'revenue')) {
                        $stats['keys_by_type']['revenue']++;
                    } elseif (str_contains($keyName, 'search')) {
                        $stats['keys_by_type']['search']++;
                    }
                }
                
                $info = $redis->info('memory');
                $stats['memory_usage'] = $info['used_memory_human'] ?? 'N/A';
            }
        } catch (\Exception $e) {
            // Fallback for non-Redis cache drivers
            $stats['error'] = 'Cache statistics only available with Redis';
        }
        
        return $stats;
    }

    /**
     * Clear old cache entries
     */
    public function cleanup(): void
    {
        // Clear search cache (very short lived)
        if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
            $redis = Redis::connection();
            $searchKeys = $redis->keys('laravel_cache:search.*');
            
            foreach ($searchKeys as $key) {
                $ttl = $redis->ttl($key);
                if ($ttl < 0 || $ttl > 120) { // Remove expired or too old search cache
                    $redis->del($key);
                }
            }
        }
    }

    // Private calculation methods
    private function calculateDashboardStats(): array
    {
        return \App\QueryBuilders\OptimizedQueries::getDashboardStatistics();
    }

    private function calculateClientFinancials(int $clientId): array
    {
        $client = \App\Models\Client::withOptimizedRelations()->find($clientId);
        if (!$client) {
            return [];
        }
        
        return [
            'total_revenue' => $client->total_revenue,
            'pending_amount' => $client->pending_amount,
            'projects_count' => $client->projects_count,
            'active_projects_count' => $client->active_projects_count,
            'has_overdue_payments' => $client->has_overdue_payments,
            'last_updated' => now(),
        ];
    }

    private function calculateProjectMetrics(int $projectId): array
    {
        return \App\QueryBuilders\OptimizedQueries::getProjectPerformanceMetrics($projectId) ?: [];
    }

    private function calculateRevenueChartData(int $months): array
    {
        return \App\QueryBuilders\OptimizedQueries::getRevenueChartData($months);
    }

    private function performGlobalSearch(string $term, int $limit): array
    {
        return \App\QueryBuilders\OptimizedQueries::globalSearch($term, $limit);
    }

    private function getClientsWithFinancials(array $filters): array
    {
        return \App\QueryBuilders\OptimizedQueries::getClientsWithFinancials($filters)->toArray();
    }

    private function getProjectsWithFinancials(array $filters): array
    {
        return \App\QueryBuilders\OptimizedQueries::getProjectsWithFinancials($filters)->toArray();
    }

    private function calculateFinancialReport($startDate, $endDate): array
    {
        return \App\QueryBuilders\OptimizedQueries::getFinancialReport($startDate, $endDate);
    }

    /**
     * Check if cache store supports tags
     */
    private function supportsTags(): bool
    {
        try {
            return Cache::supportsTags();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Flush cache by pattern (fallback for stores without tag support)
     */
    private function flushCacheByPattern(string $pattern): void
    {
        try {
            // For database cache, we need to manually query and delete
            if (Cache::getStore() instanceof \Illuminate\Cache\DatabaseStore) {
                $store = Cache::getStore();
                $connection = $store->getConnection();
                
                // Use config to get table name since getTable() doesn't exist
                $table = config('cache.stores.database.table', 'cache');
                $prefix = $store->getPrefix();
                
                // Convert pattern to SQL LIKE pattern
                $likePattern = str_replace('*', '%', $prefix . $pattern);
                
                $connection->table($table)
                    ->where('key', 'like', $likePattern)
                    ->delete();
            }
        } catch (\Exception $e) {
            // If pattern flushing fails, just log the error
            // For testing, we can tolerate less efficient cache invalidation
            logger('Cache pattern flush failed: ' . $e->getMessage());
        }
    }
}