<?php

namespace App\Repositories\Contracts\Projects;

use App\Models\Project;

interface ProjectStoreRepositoryInterface
{
    /**
     * Create a new project
     */
    public function create(array $data): Project;

    /**
     * Check if a project name already exists for a specific user and client
     */
    public function projectNameExistsForUserAndClient(string $name, int $userId, int $clientId): bool;
}
