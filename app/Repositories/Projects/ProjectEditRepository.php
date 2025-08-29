<?php

namespace App\Repositories\Projects;

use App\Models\Project;
use App\Models\Client;
use App\Repositories\Contracts\Projects\ProjectEditRepositoryInterface;

class ProjectEditRepository implements ProjectEditRepositoryInterface
{
    /**
     * Find a project for editing with security check and required relations
     */
    public function findProjectForEdit(int $projectId): ?Project
    {
        return Project::withWhereHas('client', function($q) {
            $q->where('clients.user_id', auth()->id());
        })
        ->find($projectId);
    }
}
