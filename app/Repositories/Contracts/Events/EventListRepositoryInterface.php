<?php

namespace App\Repositories\Contracts\Events;

use Illuminate\Pagination\LengthAwarePaginator;

interface EventListRepositoryInterface
{
    /**
     * Get paginated events with filters
     */
    public function paginate(int $perPage, array $filters = []): LengthAwarePaginator;

    /**
     * Get global statistics
     */
    public function getGlobalStatistics(): array;

    /**
     * Get available clients for dropdown/filters
     */
    public function getAvailableClients(): array;
}