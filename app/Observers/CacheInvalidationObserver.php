<?php

namespace App\Observers;

use App\Services\Cache\CacheService;
use Illuminate\Support\Facades\App;

class CacheInvalidationObserver
{
    private CacheService $cacheService;

    public function __construct()
    {
        $this->cacheService = App::make(CacheService::class);
    }

    /**
     * Handle model creation events
     */
    public function created($model): void
    {
        $this->invalidateRelevantCache($model, 'created');
    }

    /**
     * Handle model update events
     */
    public function updated($model): void
    {
        $this->invalidateRelevantCache($model, 'updated');
    }

    /**
     * Handle model deletion events
     */
    public function deleted($model): void
    {
        $this->invalidateRelevantCache($model, 'deleted');
    }

    /**
     * Invalidate relevant cache based on model type
     */
    private function invalidateRelevantCache($model, string $action): void
    {
        $modelClass = get_class($model);
        
        match ($modelClass) {
            \App\Models\Client::class => $this->handleClientCacheInvalidation($model, $action),
            \App\Models\Project::class => $this->handleProjectCacheInvalidation($model, $action),
            \App\Models\Event::class => $this->handleEventCacheInvalidation($model, $action),
            default => null,
        };
    }

    /**
     * Handle client cache invalidation
     */
    private function handleClientCacheInvalidation(\App\Models\Client $client, string $action): void
    {
        $this->cacheService->invalidateClientCache($client->id);
        
        // If client was deleted, also invalidate all related projects
        if ($action === 'deleted') {
            $client->projects->each(function ($project) {
                $this->cacheService->invalidateProjectCache($project->id);
            });
        }
        
        $this->logCacheInvalidation('Client', $client->id, $action);
    }

    /**
     * Handle project cache invalidation
     */
    private function handleProjectCacheInvalidation(\App\Models\Project $project, string $action): void
    {
        $this->cacheService->invalidateProjectCache($project->id);
        
        // If project status changed, invalidate client cache
        if ($action === 'updated' && $project->wasChanged('status')) {
            $this->cacheService->invalidateClientCache($project->client_id);
        }
        
        // If budget changed, invalidate financial caches
        if ($action === 'updated' && $project->wasChanged('budget')) {
            $this->cacheService->invalidateClientCache($project->client_id);
            $this->cacheService->invalidateDashboardCache();
        }
        
        $this->logCacheInvalidation('Project', $project->id, $action);
    }

    /**
     * Handle event cache invalidation
     */
    private function handleEventCacheInvalidation(\App\Models\Event $event, string $action): void
    {
        $this->cacheService->invalidateEventCache($event->id);
        
        // Special handling for billing events
        if ($event->event_type === 'billing') {
            // If payment status changed, invalidate financial data
            if ($action === 'updated' && $event->wasChanged('payment_status')) {
                $this->cacheService->invalidateDashboardCache();
                
                // Clear revenue chart cache
                \Illuminate\Support\Facades\Cache::forget('revenue.chart.6months');
                \Illuminate\Support\Facades\Cache::forget('revenue.chart.12months');
            }
            
            // If amount changed, invalidate all financial calculations
            if ($action === 'updated' && $event->wasChanged('amount')) {
                $this->cacheService->invalidateDashboardCache();
            }
        }
        
        // If task status changed, invalidate dashboard
        if ($event->event_type === 'step' && $action === 'updated' && $event->wasChanged('status')) {
            $this->cacheService->invalidateDashboardCache();
        }
        
        $this->logCacheInvalidation('Event', $event->id, $action);
    }

    /**
     * Log cache invalidation for debugging
     */
    private function logCacheInvalidation(string $modelType, int $modelId, string $action): void
    {
        if (config('app.debug')) {
            \Log::debug("Cache invalidated: {$modelType} {$modelId} was {$action}", [
                'model_type' => $modelType,
                'model_id' => $modelId,
                'action' => $action,
                'timestamp' => now(),
            ]);
        }
    }
}