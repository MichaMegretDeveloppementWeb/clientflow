<?php

namespace App\Repositories\Projects;

use App\Models\Project;
use App\Repositories\Contracts\Projects\ProjectStoreRepositoryInterface;

class ProjectStoreRepository implements ProjectStoreRepositoryInterface
{
    /**
     * Create a new project
     */
    public function create(array $data): Project
    {
        return Project::create($data);
    }

    /**
     * Check if a project name already exists for a specific user and client
     */
    public function projectNameExistsForUserAndClient(string $name, int $userId, int $clientId): bool
    {
        return Project::whereHas('client', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('client_id', $clientId)
            ->where('name', $name)
            ->exists();
    }
}
