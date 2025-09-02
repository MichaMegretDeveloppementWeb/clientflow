<?php

namespace App\Repositories\Events;

use App\Models\Event;
use App\Models\Project;
use App\Repositories\Contracts\Events\EventStoreRepositoryInterface;

class EventStoreRepository implements EventStoreRepositoryInterface
{
    /**
     * Create a new event
     */
    public function create(array $data): Event
    {
        return Event::create($data);
    }

    /**
     * Check if a project belongs to the authenticated user
     */
    public function projectBelongsToUser(int $projectId): bool
    {
        return Project::whereHas('client', function ($query) {
            $query->where('user_id', auth()->id());
        })->where('id', $projectId)->exists();
    }
}
