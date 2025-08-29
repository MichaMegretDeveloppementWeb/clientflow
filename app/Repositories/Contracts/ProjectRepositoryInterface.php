<?php

namespace App\Repositories\Contracts;

use App\Models\Project;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ProjectRepositoryInterface
{
    /**
     * Find project by ID
     */
    public function find(int $id): ?Project;

    /**
     * Find project by ID or fail
     */
    public function findOrFail(int $id): Project;

    /**
     * Get all projects
     */
    public function all(): Collection;

    /**
     * Get paginated projects
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    /**
     * Create new project
     */
    public function create(array $data): Project;

    /**
     * Update project
     */
    public function update(Project $project, array $data): Project;

    /**
     * Delete project
     */
    public function delete(Project $project): bool;

    /**
     * Get projects by client
     */
    public function getByClient(int $clientId): Collection;


    /**
     * Get overdue projects
     */
    public function getOverdue(): Collection;

    /**
     * Get projects with budget exceeded
     */
    public function getBudgetExceeded(): Collection;

    /**
     * Get project with relationships
     */
    public function getWithRelations(int $id, array $relations = []): ?Project;

    /**
     * Get project statistics
     */
    public function getStatistics(Project $project): array;

    public function getGlobalStatistics(): array;

    /**
     * Get recent projects
     */
    public function getRecent(int $limit = 10): Collection;
}
