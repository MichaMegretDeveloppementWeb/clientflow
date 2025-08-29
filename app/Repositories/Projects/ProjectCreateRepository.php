<?php

namespace App\Repositories\Projects;

use App\Models\Client;
use App\Repositories\Contracts\Projects\ProjectCreateRepositoryInterface;

class ProjectCreateRepository implements ProjectCreateRepositoryInterface
{
    /**
     * Get available clients for project creation
     */
    public function getAvailableClients(): array
    {
        return Client::where('user_id', auth()->id())
            ->select('id', 'name', 'company')
            ->orderBy('name')
            ->get()
            ->map(function ($client) {
                return [
                    'id' => $client->id, 
                    'name' => $client->name,
                    'company' => $client->company
                ];
            })
            ->toArray();
    }
}
