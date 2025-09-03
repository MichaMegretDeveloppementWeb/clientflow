<?php

namespace App\Repositories\Contracts\Dashboard;

interface BillingRepositoryInterface
{
    /**
     * Get billing statistics data
     */
    public function getBillingStatistics(int $userId): array;
}
