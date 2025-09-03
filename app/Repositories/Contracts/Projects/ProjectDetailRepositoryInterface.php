<?php

namespace App\Repositories\Contracts\Projects;

use App\Models\Project;

interface ProjectDetailRepositoryInterface
{
    /**
     * Find project with specific relations
     */
    public function findWithRelations(int $projectId, array $relations = []): ?Project;

    /**
     * Find project with complete financial data for detail page
     */
    public function findWithFinancialStats(int $projectId): ?Project;
}
