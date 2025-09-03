<?php

namespace App\Services\Dashboard;

use App\Repositories\Contracts\Dashboard\ActivitiesRepositoryInterface;
use Illuminate\Support\Collection;

class DashboardActivitiesService
{
    public function __construct(
        private readonly ActivitiesRepositoryInterface $activitiesRepository
    ) {}

    /**
     * Get recent activities for dashboard
     * OPTIMIZED: Repository pattern + error handling
     */
    public function getRecentActivities(int $limit = 15): Collection
    {
        try {
            $userId = auth()->id();

            return $this->activitiesRepository->getRecentActivities($userId, $limit);
        } catch (\Exception $e) {
            // Log error for debugging but return empty collection
            \Log::error('Error fetching recent activities: '.$e->getMessage());

            return collect([]);
        }
    }
}
