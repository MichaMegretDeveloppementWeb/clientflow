<?php

namespace App\Repositories\Contracts;

use App\Models\Client;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ClientRepositoryInterface
{
    /**
     * Find client by ID
     */
    public function find(int $id): ?Client;

    /**
     * Find client by ID or fail
     */
    public function findOrFail(int $id): Client;

    /**
     * Get all clients
     */
    public function all(): Collection;

    /**
     * Get paginated clients
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    /**
     * Create new client
     */
    public function create(array $data): Client;

    /**
     * Update client
     */
    public function update(Client $client, array $data): Client;

    /**
     * Delete client
     */
    public function delete(Client $client): bool;

    /**
     * Search clients
     */
    public function search(string $term): Collection;

    /**
     * Get clients with active projects
     */
    public function getWithActiveProjects(): Collection;

    /**
     * Get clients with unpaid invoices
     */
    public function getWithUnpaidInvoices(): Collection;

    /**
     * Get client with relationships
     */
    public function getWithRelations(int $id, array $relations = []): ?Client;

    /**
     * Get client statistics
     */
    public function getStatistics(Client $client): array;

    /**
     * Get global client list statistics
     */
    public function getGlobalStatistics(): array;

    /**
     * Find client with full details
     */
    public function findWithDetails(int $id): Client;

    /**
     * Check if client has projects
     */
    public function hasProjects(Client $client): bool;

    /**
     * Merge two clients
     */
    public function merge(Client $sourceClient, Client $targetClient): Client;
}