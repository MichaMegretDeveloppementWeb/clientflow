<?php

namespace App\Repositories\Contracts\Dashboard;

interface QuickStatsRepositoryInterface
{
    /**
     * Get all quick stats data in optimized queries
     */
    public function getQuickStatsData(int $userId): array;
}
