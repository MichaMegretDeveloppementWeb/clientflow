<?php

declare(strict_types=1);

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HelpResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'onboarding' => [
                'progress' => $this->resource['onboarding_progress'],
                'progress_formatted' => $this->resource['onboarding_progress'] . '%',
                'is_complete' => $this->resource['onboarding_progress'] >= 100,
            ],
            'status' => [
                'has_clients' => $this->resource['has_clients'],
                'has_projects' => $this->resource['has_projects'],
                'has_events' => $this->resource['has_events'],
            ],
            'tips' => array_map(function ($tip) {
                return [
                    'id' => $tip['id'],
                    'icon' => $tip['icon'],
                    'text' => $tip['text'],
                    'completed' => $tip['completed'],
                    'route' => $tip['route'],
                ];
            }, $this->resource['tips']),
            'actions' => array_map(function ($action) {
                return [
                    'id' => $action['id'],
                    'icon' => $action['icon'],
                    'text' => $action['text'],
                    'variant' => $action['variant'],
                    'action' => $action['action'],
                ];
            }, $this->resource['actions']),
        ];
    }
}