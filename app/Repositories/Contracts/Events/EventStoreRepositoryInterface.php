<?php

namespace App\Repositories\Contracts\Events;

use App\Models\Event;

interface EventStoreRepositoryInterface
{
    /**
     * Create a new event
     */
    public function create(array $data): Event;

    /**
     * Check if a project belongs to the authenticated user
     */
    public function projectBelongsToUser(int $projectId): bool;
}