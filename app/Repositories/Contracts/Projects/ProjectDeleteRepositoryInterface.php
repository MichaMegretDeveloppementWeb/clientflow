<?php

namespace App\Repositories\Contracts\Projects;

use App\Models\Project;

interface ProjectDeleteRepositoryInterface
{
    /**
     * Delete project (soft delete recommended)
     */
    public function delete(Project $project): bool;
}
