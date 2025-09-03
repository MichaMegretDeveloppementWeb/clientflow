<?php

namespace App\Services\Dashboard;

use App\Repositories\Contracts\Dashboard\StatisticsRepositoryInterface;

class DashboardStatisticsService
{
    public function __construct(
        private readonly StatisticsRepositoryInterface $statisticsRepository
    ) {}

    /**
     * Get basic statistics for StatsGrid component
     * OPTIMIZED: Only retrieves what StatsGrid actually needs (2 queries instead of 23)
     */
    public function getBasicStatistics(): array
    {
        $userId = auth()->id();

        return $this->statisticsRepository->getBasicStatistics($userId);
    }
}
