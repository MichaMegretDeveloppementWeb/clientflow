<?php

namespace App\Repositories\Dashboard;

use App\Enums\EventType;
use App\Models\Client;
use App\Models\Event;
use App\Models\Project;
use App\Repositories\Contracts\Dashboard\ActivitiesRepositoryInterface;
use Illuminate\Support\Collection;

class ActivitiesRepository implements ActivitiesRepositoryInterface
{
    /**
     * Get recent activities for dashboard (clients, projects, events created or completed)
     * OPTIMIZED: Reduced from 5 separate queries to 3 optimized queries
     */
    public function getRecentActivities(int $userId, int $limit = 15): Collection
    {
        $activities = collect();

        // Get recent clients (optimized with select)
        $clientActivities = Client::select('id', 'name', 'company', 'created_at')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(8) // Reduced individual limits for better overall performance
            ->get()
            ->map(function ($client) {
                return [
                    'id' => 'client_'.$client->id,
                    'type' => 'client',
                    'entity_type' => 'Client',
                    'name' => $client->name,
                    'company' => $client->company,
                    'status' => 'created',
                    'timestamp' => $client->created_at,
                    'link' => '/clients/'.$client->id,
                    'parent_project' => null,
                    'parent_client' => null,
                    'amount' => null,
                ];
            });

        // Get recent projects with optimized relations
        $projectActivities = Project::select('projects.id', 'projects.name', 'projects.created_at', 'clients.id as client_id', 'clients.name as client_name')
            ->join('clients', 'projects.client_id', '=', 'clients.id')
            ->where('clients.user_id', $userId)
            ->orderBy('projects.created_at', 'desc')
            ->limit(8)
            ->get()
            ->map(function ($project) {
                return [
                    'id' => 'project_'.$project->id,
                    'type' => 'project',
                    'entity_type' => 'Projet',
                    'name' => $project->name,
                    'company' => null,
                    'status' => 'created',
                    'timestamp' => $project->created_at,
                    'link' => '/projects/'.$project->id,
                    'parent_project' => null,
                    'parent_client' => [
                        'id' => $project->client_id,
                        'name' => $project->client_name,
                    ],
                    'amount' => null,
                ];
            });

        // Get recent events (created, completed, paid) in one optimized query
        $eventActivities = Event::select(
            'events.id', 'events.name', 'events.event_type', 'events.amount',
            'events.created_date', 'events.completed_at', 'events.paid_at',
            'events.status', 'events.payment_status',
            'projects.id as project_id', 'projects.name as project_name',
            'clients.id as client_id', 'clients.name as client_name'
        )
            ->join('projects', 'events.project_id', '=', 'projects.id')
            ->join('clients', 'projects.client_id', '=', 'clients.id')
            ->where('clients.user_id', $userId)
            ->where(function ($query) {
                // Events created recently OR completed recently OR paid recently
                $query->where('events.created_date', '>=', now()->subDays(30))
                    ->orWhere('events.completed_at', '>=', now()->subDays(30))
                    ->orWhere('events.paid_at', '>=', now()->subDays(30));
            })
            ->orderByDesc('events.created_date')
            ->orderByDesc('events.completed_at')
            ->orderByDesc('events.paid_at')
            ->limit(20) // Get more events since we'll create multiple activities per event
            ->get()
            ->flatMap(function ($event) {
                $eventActivities = collect();

                // Add created activity
                if ($event->created_date && $event->created_date >= now()->subDays(30)) {
                    $eventActivities->push([
                        'id' => 'event_created_'.$event->id,
                        'type' => $event->event_type === EventType::Billing->value ? 'billing' : 'step',
                        'entity_type' => $event->event_type === EventType::Billing->value ? 'Facturation' : 'Étape',
                        'name' => $event->name,
                        'company' => null,
                        'status' => 'created',
                        'timestamp' => $event->created_date,
                        'link' => '/events/'.$event->id,
                        'parent_project' => [
                            'id' => $event->project_id,
                            'name' => $event->project_name,
                        ],
                        'parent_client' => [
                            'id' => $event->client_id,
                            'name' => $event->client_name,
                        ],
                        'amount' => $event->event_type === EventType::Billing->value ? $event->amount : null,
                    ]);
                }

                // Add completed activity
                if ($event->completed_at && $event->completed_at >= now()->subDays(30)) {
                    $status = $event->event_type === EventType::Billing->value ? 'sent' : 'done';
                    $eventActivities->push([
                        'id' => 'event_completed_'.$event->id,
                        'type' => $event->event_type === EventType::Billing->value ? 'billing' : 'step',
                        'entity_type' => $event->event_type === EventType::Billing->value ? 'Facturation' : 'Étape',
                        'name' => $event->name,
                        'company' => null,
                        'status' => $status,
                        'timestamp' => $event->completed_at,
                        'link' => '/events/'.$event->id,
                        'parent_project' => [
                            'id' => $event->project_id,
                            'name' => $event->project_name,
                        ],
                        'parent_client' => [
                            'id' => $event->client_id,
                            'name' => $event->client_name,
                        ],
                        'amount' => $event->event_type === EventType::Billing->value ? $event->amount : null,
                    ]);
                }

                // Add paid activity
                if ($event->paid_at && $event->paid_at >= now()->subDays(30) && $event->event_type === EventType::Billing->value) {
                    $eventActivities->push([
                        'id' => 'event_paid_'.$event->id,
                        'type' => 'billing',
                        'entity_type' => 'Facturation',
                        'name' => $event->name,
                        'company' => null,
                        'status' => 'paid',
                        'timestamp' => $event->paid_at,
                        'link' => '/events/'.$event->id,
                        'parent_project' => [
                            'id' => $event->project_id,
                            'name' => $event->project_name,
                        ],
                        'parent_client' => [
                            'id' => $event->client_id,
                            'name' => $event->client_name,
                        ],
                        'amount' => $event->amount,
                    ]);
                }

                return $eventActivities;
            });

        // Merge all activities and sort
        return $clientActivities
            ->concat($projectActivities)
            ->concat($eventActivities)
            ->filter(function ($activity) {
                $timestamp = $activity['timestamp'];
                if (is_string($timestamp)) {
                    $timestamp = \Carbon\Carbon::parse($timestamp);
                }

                return $timestamp instanceof \Carbon\Carbon && ($timestamp->isPast() || $timestamp->isCurrentSecond());
            })
            ->sortByDesc(function ($activity) {
                $timestamp = $activity['timestamp'];
                if (is_string($timestamp)) {
                    $timestamp = \Carbon\Carbon::parse($timestamp);
                }

                return $timestamp instanceof \Carbon\Carbon ? $timestamp->getTimestamp() : 0;
            })
            ->take($limit)
            ->values();
    }
}
