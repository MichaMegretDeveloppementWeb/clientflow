<?php

namespace App\Repositories\Clients;

use App\Repositories\Contracts\Clients\ClientCreateRepositoryInterface;

class ClientCreateRepository implements ClientCreateRepositoryInterface
{
    /**
     * Get skeleton data for client creation form
     */
    public function getSkeletonData(): array
    {
        return [
            'client' => [
                'name' => '',
                'company' => '',
                'email' => '',
                'phone' => '',
                'address' => '',
                'notes' => ''
            ]
        ];
    }

    /**
     * Get complete data for client creation form
     */
    public function getCompleteData(): array
    {
        return [
            'client' => [
                'name' => '',
                'company' => '',
                'email' => '',
                'phone' => '',
                'address' => '',
                'notes' => ''
            ]
        ];
    }
}