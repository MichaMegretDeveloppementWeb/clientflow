<?php

namespace App\Repositories\Contracts\Dashboard;

use Carbon\Carbon;

interface RevenueChartRepositoryInterface
{
    /**
     * Get daily revenue and projected data for a period
     */
    public function getDailyRevenueAndProjected(Carbon $startDate, Carbon $endDate, int $userId): array;

    /**
     * Get weekly revenue and projected data for a period
     */
    public function getWeeklyRevenueAndProjected(Carbon $startDate, Carbon $endDate, int $userId): array;

    /**
     * Get monthly revenue and projected data for a period
     */
    public function getMonthlyRevenueAndProjected(Carbon $startDate, Carbon $endDate, int $userId): array;

    /**
     * Get the first billing event date for a user
     */
    public function getFirstBillingEventDate(int $userId): ?Carbon;

    /**
     * Get monthly revenue statistics
     */
    public function getMonthlyRevenueStats(Carbon $currentMonth, Carbon $lastMonth, Carbon $lastMonthEnd, int $userId): array;

    /**
     * Get yearly revenue summary
     */
    public function getYearlyRevenueSummary(Carbon $yearStart, int $userId): array;
}
