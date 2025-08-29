<?php

namespace App\Repositories\Contracts\Clients;

use App\Models\Client;

interface ClientDetailRepositoryInterface
{
    /**
     * Find client with details
     */
    public function find(int $id): Client;

    /**
     * Get client projects with optimized query
     */
    public function getClientProjects(int $clientId): \Illuminate\Database\Eloquent\Collection;

    /**
     * Get filtered client events
     */
    public function getFilteredClientEvents(int $clientId, array $filters = []): \Illuminate\Database\Eloquent\Collection;

    /**
     * Get financial statistics with optimized queries
     */
    public function getFinancialStats(int $clientId): array;
}
