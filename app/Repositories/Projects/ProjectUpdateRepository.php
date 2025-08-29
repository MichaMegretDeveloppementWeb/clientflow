<?php

namespace App\Repositories\Projects;

use App\Models\Project;
use App\Repositories\Contracts\Projects\ProjectUpdateRepositoryInterface;

class ProjectUpdateRepository implements ProjectUpdateRepositoryInterface
{
    /**
     * Update existing project
     */
    public function update(Project $project, array $data): Project
    {
        $project->update($data);
        return $project->fresh();
    }
}
