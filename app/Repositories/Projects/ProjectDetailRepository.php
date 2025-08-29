<?php

namespace App\Repositories\Projects;

use App\Models\Project;
use App\Repositories\Contracts\Projects\ProjectDetailRepositoryInterface;

class ProjectDetailRepository implements ProjectDetailRepositoryInterface
{
    /**
     * Find project with specific relations
     */
    public function findWithRelations(int $projectId, array $relations = []): ?Project
    {
        return Project::with($relations)
            ->whereRelation('client', 'user_id', auth()->id())
            ->find($projectId);
    }
}
