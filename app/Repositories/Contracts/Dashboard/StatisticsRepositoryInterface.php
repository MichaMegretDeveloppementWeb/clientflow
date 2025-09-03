<?php

namespace App\Repositories\Contracts\Dashboard;

interface StatisticsRepositoryInterface
{
    /**
     * Get basic statistics for StatsGrid component
     */
    public function getBasicStatistics(int $userId): array;
}
