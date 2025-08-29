<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\Project;
use App\Models\Event;
use App\Observers\CacheInvalidationObserver;
use App\Services\Cache\CacheService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class CacheServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(CacheService::class, function ($app) {
            return new CacheService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register model observers for automatic cache invalidation
        Client::observe(CacheInvalidationObserver::class);
        Project::observe(CacheInvalidationObserver::class);
        Event::observe(CacheInvalidationObserver::class);

        // Schedule cache maintenance tasks
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);

            // Clean up old cache entries every hour
            $schedule->call(function () {
                $cacheService = $this->app->make(CacheService::class);
                $cacheService->cleanup();
            })->hourly()->name('cache-cleanup');

            // Warm up critical caches every 6 hours
            $schedule->command('cache:warm')
                ->everySixHours()
                ->name('cache-warm-auto')
                ->runInBackground();

            // Full cache statistics and cleanup daily at 3 AM
            $schedule->call(function () {
                $cacheService = $this->app->make(CacheService::class);
                
                // Log cache statistics
                $stats = $cacheService->getCacheStats();
                \Log::info('Daily cache statistics', $stats);
                
                // Perform deep cleanup
                $cacheService->cleanup();
            })->dailyAt('03:00')->name('cache-daily-maintenance');
        });

        // Register cache macros for easier usage
        $this->registerCacheMacros();
    }

    /**
     * Register custom cache macros
     */
    private function registerCacheMacros(): void
    {
        // Macro for remembering with tags
        \Illuminate\Support\Facades\Cache::macro('rememberWithTags', function (array $tags, string $key, int $ttl, \Closure $callback) {
            return \Illuminate\Support\Facades\Cache::tags($tags)->remember($key, $ttl, $callback);
        });

        // Macro for conditional caching
        \Illuminate\Support\Facades\Cache::macro('rememberWhen', function (bool $condition, string $key, int $ttl, \Closure $callback) {
            if ($condition) {
                return \Illuminate\Support\Facades\Cache::remember($key, $ttl, $callback);
            }
            
            return $callback();
        });

        // Macro for intelligent cache key generation
        \Illuminate\Support\Facades\Cache::macro('smartKey', function (string $prefix, array $params = []) {
            $key = $prefix;
            
            if (!empty($params)) {
                ksort($params); // Ensure consistent ordering
                $key .= '.' . md5(serialize($params));
            }
            
            return $key;
        });
    }
}