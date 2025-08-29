<?php

namespace App\Repositories\Contracts\Projects;

use App\Models\Project;

interface ProjectUpdateRepositoryInterface
{
    /**
     * Update existing project
     */
    public function update(Project $project, array $data): Project;
}
