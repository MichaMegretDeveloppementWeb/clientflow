<?php

namespace App\Repositories\Contracts\Events;

use Illuminate\Support\Collection;

interface EventCreateRepositoryInterface
{
    /**
     * Get all projects with their clients for the project selector
     */
    public function getAllProjects(): Collection;

    /**
     * Get a specific project by ID with client information
     */
    public function getProjectById(int $projectId): ?array;
}