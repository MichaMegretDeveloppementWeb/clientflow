<?php

namespace App\Repositories\Contracts\Dashboard;

use Illuminate\Support\Collection;

interface ActivitiesRepositoryInterface
{
    /**
     * Get recent activities for dashboard
     */
    public function getRecentActivities(int $userId, int $limit = 15): Collection;
}
