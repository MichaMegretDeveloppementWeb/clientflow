<?php

namespace App\Repositories\Contracts\Projects;

use Illuminate\Pagination\LengthAwarePaginator;

interface ProjectListRepositoryInterface
{
    /**
     * Get paginated projects with filters
     */
    public function paginate(int $perPage, array $filters = []): LengthAwarePaginator;

    /**
     * Get global project statistics
     */
    public function getGlobalStatistics(): array;

    /**
     * Get available clients for dropdown/filters
     */
    public function getAvailableClients(): array;
}
