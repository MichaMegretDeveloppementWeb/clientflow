<?php

namespace App\Repositories\Events;

use App\Models\Project;
use App\Repositories\Contracts\Events\EventCreateRepositoryInterface;
use Illuminate\Support\Collection;

class EventCreateRepository implements EventCreateRepositoryInterface
{
    /**
     * Get all projects with their clients for the project selector
     */
    public function getAllProjects(): Collection
    {
        return Project::with('client')
            ->whereHas('client', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->orderBy('name')
            ->get()
            ->map(function ($project) {
                return [
                    'id' => $project->id,
                    'name' => $project->name,
                    'start_date' => $project->start_date?->format('Y-m-d'),
                    'client' => [
                        'id' => $project->client->id,
                        'name' => $project->client->name,
                    ]
                ];
            });
    }

    /**
     * Get a specific project by ID with client information
     */
    public function getProjectById(int $projectId): ?array
    {
        $project = Project::with('client')
            ->whereHas('client', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->find($projectId);

        if (!$project) {
            return null;
        }

        return [
            'id' => $project->id,
            'name' => $project->name,
            'start_date' => $project->start_date?->format('Y-m-d'),
            'client' => [
                'id' => $project->client->id,
                'name' => $project->client->name,
            ]
        ];
    }
}