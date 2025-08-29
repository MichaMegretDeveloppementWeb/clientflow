<?php

declare(strict_types=1);

namespace App\Services\Dashboard;

use App\Models\Client;
use App\Models\Project;
use App\Models\Event;
use App\Enums\ProjectStatus;

class DashboardHelpService
{
    public function getHelpData(): array
    {
        $userId = auth()->id();

        return [
            'onboarding_progress' => $this->getOnboardingProgress($userId),
            'has_clients' => $this->hasClients($userId),
            'has_projects' => $this->hasProjects($userId),
            'has_events' => $this->hasEvents($userId),
            'tips' => $this->getTips($userId),
            'actions' => $this->getActions(),
        ];
    }

    private function hasClients(int $userId): bool
    {
        return Client::where('user_id', $userId)->exists();
    }

    private function hasProjects(int $userId): bool
    {
        return Project::whereHas('client', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->exists();
    }

    private function hasEvents(int $userId): bool
    {
        return Event::whereHas('project.client', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->exists();
    }

    private function getOnboardingProgress(int $userId): int
    {
        $progress = 0;
        
        if ($this->hasClients($userId)) {
            $progress += 33;
        }
        
        if ($this->hasProjects($userId)) {
            $progress += 33;
        }
        
        if ($this->hasEvents($userId)) {
            $progress += 34;
        }
        
        return $progress;
    }

    private function getTips(int $userId): array
    {
        return [
            [
                'id' => 'clients',
                'icon' => 'users',
                'text' => 'Commencez par ajouter vos clients existants',
                'completed' => $this->hasClients($userId),
                'route' => 'clients.index',
            ],
            [
                'id' => 'projects',
                'icon' => 'folder-plus',
                'text' => 'Créez votre premier projet',
                'completed' => $this->hasProjects($userId),
                'route' => 'projects.index',
            ],
            [
                'id' => 'events',
                'icon' => 'calendar',
                'text' => 'Planifiez vos prochaines tâches',
                'completed' => $this->hasEvents($userId),
                'route' => 'events.index',
            ],
        ];
    }

    private function getActions(): array
    {
        return [
            [
                'id' => 'guide',
                'icon' => 'book-open',
                'text' => 'Guide de démarrage',
                'variant' => 'outline',
                'action' => 'guide',
            ],
            [
                'id' => 'demo',
                'icon' => 'play-circle',
                'text' => 'Vidéo de démo',
                'variant' => 'ghost',
                'action' => 'demo',
            ],
        ];
    }
}