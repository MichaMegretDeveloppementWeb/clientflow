<?php

namespace App\Repositories\Contracts\Projects;

interface ProjectCreateRepositoryInterface
{
    /**
     * Get available clients for project creation
     */
    public function getAvailableClients(): array;
}
