<?php

namespace App\Repositories\Contracts\Clients;

use Illuminate\Pagination\LengthAwarePaginator;

interface ClientListRepositoryInterface
{
    /**
     * Get paginated clients
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    /**
     * Get global client list statistics
     */
    public function getGlobalStatistics(): array;
}
