<?php

namespace App\Repositories\Contracts\Projects;

use App\Models\Project;

interface ProjectEditRepositoryInterface
{
    /**
     * Find a project for editing with security check and required relations
     */
    public function findProjectForEdit(int $projectId): ?Project;
}
