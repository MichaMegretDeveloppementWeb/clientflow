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
        // Convertir les dates string en Carbon pour la base de données
        if (isset($data['created_at'])) {
            $data['created_date'] = $data['created_at'];
            unset($data['created_at']);
        }

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