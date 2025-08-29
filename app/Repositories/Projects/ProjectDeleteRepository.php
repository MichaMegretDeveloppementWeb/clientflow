<?php

namespace App\Repositories\Projects;

use App\Models\Project;
use App\Repositories\Contracts\Projects\ProjectDeleteRepositoryInterface;

class ProjectDeleteRepository implements ProjectDeleteRepositoryInterface
{
    /**
     * Delete project (soft delete recommended)
     */
    public function delete(Project $project): bool
    {
        return $project->delete();
    }
}
