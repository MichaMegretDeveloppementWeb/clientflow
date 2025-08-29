<?php

namespace App\Console\Commands;

use App\Services\Cache\CacheService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'cache:clear-app 
                            {--type=* : Specific cache types to clear (dashboard, clients, projects, events, revenue, search)}
                            {--stats : Show cache statistics before clearing}
                            {--force : Skip confirmation}';

    /**
     * The console command description.
     */
    protected $description = 'Clear application-specific caches with granular control';

    private CacheService $cacheService;

    /**
     * Create a new command instance.
     */
    public function __construct(CacheService $cacheService)
    {
        parent::__construct();
        $this->cacheService = $cacheService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if ($this->option('stats')) {
            $this->displayCacheStats();
        }

        $types = $this->option('type');
        
        if (empty($types)) {
            return $this->clearAllCaches();
        } else {
            return $this->clearSpecificCaches($types);
        }
    }

    /**
     * Clear all application caches
     */
    private function clearAllCaches(): int
    {
        if (!$this->option('force')) {
            if (!$this->confirm('This will clear ALL application caches. Continue?')) {
                $this->info('Cache clearing cancelled.');
                return self::SUCCESS;
            }
        }

        $this->info('Clearing all application caches...');

        $tasks = [
            'Dashboard Cache' => fn() => Cache::forget('dashboard.stats'),
            'Revenue Charts' => fn() => $this->clearRevenueCache(),
            'Client Caches' => fn() => Cache::tags(['clients'])->flush(),
            'Project Caches' => fn() => Cache::tags(['projects'])->flush(),
            'Event Caches' => fn() => Cache::tags(['events'])->flush(),
            'Search Cache' => fn() => $this->clearSearchCache(),
            'Financial Reports' => fn() => $this->clearFinancialCache(),
        ];

        foreach ($tasks as $taskName => $task) {
            $this->line("  Clearing: {$taskName}");
            $task();
            $this->line("  ✓ {$taskName} cleared");
        }

        $this->info('All application caches cleared successfully!');
        return self::SUCCESS;
    }

    /**
     * Clear specific cache types
     */
    private function clearSpecificCaches(array $types): int
    {
        $validTypes = ['dashboard', 'clients', 'projects', 'events', 'revenue', 'search', 'financial'];
        $invalidTypes = array_diff($types, $validTypes);

        if (!empty($invalidTypes)) {
            $this->error('Invalid cache types: ' . implode(', ', $invalidTypes));
            $this->line('Valid types: ' . implode(', ', $validTypes));
            return self::FAILURE;
        }

        $this->info('Clearing specific cache types: ' . implode(', ', $types));

        foreach ($types as $type) {
            $this->clearCacheByType($type);
            $this->line("  ✓ {$type} cache cleared");
        }

        $this->info('Specific caches cleared successfully!');
        return self::SUCCESS;
    }

    /**
     * Clear cache by specific type
     */
    private function clearCacheByType(string $type): void
    {
        match ($type) {
            'dashboard' => $this->clearDashboardCache(),
            'clients' => Cache::tags(['clients'])->flush(),
            'projects' => Cache::tags(['projects'])->flush(),
            'events' => Cache::tags(['events'])->flush(),
            'revenue' => $this->clearRevenueCache(),
            'search' => $this->clearSearchCache(),
            'financial' => $this->clearFinancialCache(),
            default => null,
        };
    }

    /**
     * Clear dashboard-specific cache
     */
    private function clearDashboardCache(): void
    {
        $keys = [
            'dashboard.stats',
            'dashboard.urgent_tasks',
            'dashboard.upcoming_events',
            'dashboard.recent_projects',
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Clear revenue-specific cache
     */
    private function clearRevenueCache(): void
    {
        $keys = [
            'revenue.chart.6months',
            'revenue.chart.12months',
            'revenue.monthly_stats',
            'revenue.yearly_summary',
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Clear search cache
     */
    private function clearSearchCache(): void
    {
        if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
            $redis = \Illuminate\Support\Facades\Redis::connection();
            $searchKeys = $redis->keys('laravel_cache:search.*');
            
            foreach ($searchKeys as $key) {
                $redis->del($key);
            }
        }
    }

    /**
     * Clear financial reports cache
     */
    private function clearFinancialCache(): void
    {
        if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
            $redis = \Illuminate\Support\Facades\Redis::connection();
            $financialKeys = $redis->keys('laravel_cache:financial.*');
            
            foreach ($financialKeys as $key) {
                $redis->del($key);
            }
        }
    }

    /**
     * Display cache statistics
     */
    private function displayCacheStats(): void
    {
        $this->info('Current Cache Statistics:');
        $this->line('=========================');

        $stats = $this->cacheService->getCacheStats();

        $this->table(
            ['Cache Type', 'Keys Count'],
            [
                ['Dashboard', $stats['keys_by_type']['dashboard']],
                ['Clients', $stats['keys_by_type']['clients']],
                ['Projects', $stats['keys_by_type']['projects']],
                ['Events', $stats['keys_by_type']['events']],
                ['Revenue', $stats['keys_by_type']['revenue']],
                ['Search', $stats['keys_by_type']['search']],
                ['Total', $stats['total_keys']],
            ]
        );

        $this->line('Memory Usage: ' . $stats['memory_usage']);
        $this->newLine();
    }
}