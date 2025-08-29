<?php

namespace App\Repositories\Contracts\Clients;

interface ClientCreateRepositoryInterface
{
    /**
     * Get skeleton data for client creation form
     */
    public function getSkeletonData(): array;

    /**
     * Get complete data for client creation form  
     */
    public function getCompleteData(): array;
}
