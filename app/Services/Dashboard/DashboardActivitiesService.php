<?php

namespace App\Services\Dashboard;

use App\Models\Client;
use App\Models\Event;
use App\Models\Project;
use App\Enums\EventStatus;
use App\Enums\EventType;
use Illuminate\Support\Collection;

class DashboardActivitiesService
{
    /**
     * Get recent activities for dashboard (clients, projects, events created or completed)
     */
    public function getRecentActivities(int $limit = 15): Collection
    {
        $userId = auth()->id();
        $activities = collect();

        // 1. Récupérer les clients créés récemment
        $recentClients = Client::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        foreach ($recentClients as $client) {
            $activities->push([
                'id' => 'client_' . $client->id,
                'type' => 'client',
                'entity_type' => 'Client',
                'name' => $client->name,
                'company' => $client->company,
                'status' => 'created',
                'timestamp' => $client->created_at,
                'link' => '/clients/' . $client->id,
                // Pas de projet ou client parent pour un client
                'parent_project' => null,
                'parent_client' => null,
                'amount' => null,
            ]);
        }

        // 2. Récupérer les projets créés récemment
        $recentProjects = Project::with('client')
            ->whereHas('client', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        foreach ($recentProjects as $project) {
            $activities->push([
                'id' => 'project_' . $project->id,
                'type' => 'project',
                'entity_type' => 'Projet',
                'name' => $project->name,
                'company' => null,
                'status' => 'created',
                'timestamp' => $project->created_at,
                'link' => '/projects/' . $project->id,
                'parent_project' => null,
                'parent_client' => [
                    'id' => $project->client->id,
                    'name' => $project->client->name,
                ],
                'amount' => null,
            ]);
        }

        // 3. Récupérer les événements créés récemment
        $recentEventsCreated = Event::with(['project.client'])
            ->whereHas('project.client', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        foreach ($recentEventsCreated as $event) {
            $activities->push([
                'id' => 'event_created_' . $event->id,
                'type' => $event->event_type === EventType::Billing->value ? 'billing' : 'step',
                'entity_type' => $event->event_type === EventType::Billing->value ? 'Facturation' : 'Étape',
                'name' => $event->name,
                'company' => null,
                'status' => 'created',
                'timestamp' => $event->created_at,
                'link' => '/events/' . $event->id,
                'parent_project' => [
                    'id' => $event->project->id,
                    'name' => $event->project->name,
                ],
                'parent_client' => [
                    'id' => $event->project->client->id,
                    'name' => $event->project->client->name,
                ],
                'amount' => $event->event_type === EventType::Billing->value ? $event->amount : null,
            ]);
        }

        // 4. Récupérer les tâches récemment terminées (fait/envoyé)
        $recentCompleted = Event::with(['project.client'])
            ->whereHas('project.client', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('event_type', EventType::Step->value)
                        ->where('status', EventStatus::Done->value);
                })
                ->orWhere(function ($q) {
                    $q->where('event_type', EventType::Billing->value)
                        ->where('status', EventStatus::Sent->value);
                });
            })
            ->whereNotNull('completed_at')
            ->orderBy('completed_at', 'desc')
            ->limit(10)
            ->get();

        foreach ($recentCompleted as $event) {
            $status = $event->event_type === EventType::Billing->value ? 'sent' : 'done';

            $activities->push([
                'id' => 'event_completed_' . $event->id,
                'type' => $event->event_type === EventType::Billing->value ? 'billing' : 'step',
                'entity_type' => $event->event_type === EventType::Billing->value ? 'Facturation' : 'Étape',
                'name' => $event->name,
                'company' => null,
                'status' => $status,
                'timestamp' => $event->completed_at, // Utiliser completed_at pour les tâches terminées
                'link' => '/events/' . $event->id,
                'parent_project' => [
                    'id' => $event->project->id,
                    'name' => $event->project->name,
                ],
                'parent_client' => [
                    'id' => $event->project->client->id,
                    'name' => $event->project->client->name,
                ],
                'amount' => $event->event_type === EventType::Billing->value ? $event->amount : null,
            ]);
        }

        // 5. Récupérer les facturations récemment payées
        $recentPaid = Event::with(['project.client'])
            ->whereHas('project.client', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('event_type', EventType::Billing->value)
            ->where('payment_status', 'paid')
            ->whereNotNull('paid_at')
            ->orderBy('paid_at', 'desc')
            ->limit(10)
            ->get();

        foreach ($recentPaid as $event) {
            $activities->push([
                'id' => 'event_paid_' . $event->id,
                'type' => 'billing',
                'entity_type' => 'Facturation',
                'name' => $event->name,
                'company' => null,
                'status' => 'paid',
                'timestamp' => $event->paid_at, // Utiliser paid_at pour les facturations payées
                'link' => '/events/' . $event->id,
                'parent_project' => [
                    'id' => $event->project->id,
                    'name' => $event->project->name,
                ],
                'parent_client' => [
                    'id' => $event->project->client->id,
                    'name' => $event->project->client->name,
                ],
                'amount' => $event->amount,
            ]);
        }

        // Filtrer les activités dans le passé/présent uniquement, puis trier
        return $activities
            ->filter(function ($activity) {
                // Normaliser le timestamp
                $timestamp = $activity['timestamp'];
                if (is_string($timestamp)) {
                    $timestamp = \Carbon\Carbon::parse($timestamp);
                }
                
                // Ne garder que les activités dans le passé ou maintenant
                return $timestamp instanceof \Carbon\Carbon && $timestamp->isPast() || $timestamp->isCurrentSecond();
            })
            ->sortByDesc(function ($activity) {
                // Normaliser le timestamp en objet Carbon
                $timestamp = $activity['timestamp'];
                
                // Si c'est une string, parser en Carbon
                if (is_string($timestamp)) {
                    $timestamp = \Carbon\Carbon::parse($timestamp);
                }
                
                // Si c'est déjà un Carbon, on l'utilise tel quel
                if ($timestamp instanceof \Carbon\Carbon) {
                    return $timestamp->getTimestamp(); // Timestamp Unix pour tri numérique
                }
                
                // Fallback : essayer de créer un Carbon
                return \Carbon\Carbon::parse($timestamp)->getTimestamp();
            })
            ->take($limit)
            ->values();
    }
}
