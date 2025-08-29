<?php

namespace App\DTOs;

use Illuminate\Support\Collection;

class DashboardDTO
{
    public function __construct(
        public readonly array $statistics,
        public readonly Collection $urgentTasks,
        public readonly Collection $upcomingEvents,
        public readonly Collection $recentProjects,
        public readonly array $revenueData,
    ) {}

    /**
     * Create DTO from service data
     */
    public static function fromServiceData(array $data): self
    {
        return new self(
            statistics: $data['stats'] ?? [],
            urgentTasks: collect($data['urgentTasks'] ?? [])->map(function ($task) {
                return EventDTO::fromArray($task);
            }),
            upcomingEvents: collect($data['upcomingEvents'] ?? [])->map(function ($event) {
                return EventDTO::fromArray($event);
            }),
            recentProjects: collect($data['recentProjects'] ?? [])->map(function ($project) {
                return ProjectDTO::fromArray($project);
            }),
            revenueData: $data['revenueData'] ?? [],
        );
    }

    /**
     * Convert to array for API response
     */
    public function toArray(): array
    {
        return [
            'stats' => $this->formatStatistics($this->statistics),
            'urgent_tasks' => $this->urgentTasks->toArray(),
            'upcoming_events' => $this->upcomingEvents->toArray(),
            'recent_projects' => $this->recentProjects->toArray(),
            'recent_activities' => $this->generateRecentActivities(),
            'financial_stats' => $this->formatFinancialStats(),
            'revenue_data' => $this->revenueData,
            'summary' => $this->getSummary(),
        ];
    }

    /**
     * Format statistics with additional computed values
     */
    private function formatStatistics(array $stats): array
    {
        return [
            'totalClients' => $stats['total_clients'] ?? 0,
            'activeProjects' => $stats['active_projects'] ?? 0,
            'completedProjects' => $stats['completed_projects'] ?? 0,
            'onHoldProjects' => $stats['on_hold_projects'] ?? 0,
            'monthlyRevenue' => $stats['monthly_revenue'] ?? 0,
            'revenueGrowth' => $stats['revenue_growth'] ?? 0,
            'clientsGrowth' => $stats['client_growth_rate'] ?? 0,
            'projectsCompletedThisWeek' => $stats['projects_completed_this_week'] ?? 0,
        ];
    }

    /**
     * Generate recent activities from urgent tasks and recent projects
     */
    private function generateRecentActivities(): array
    {
        $activities = [];

        // Add urgent tasks as activities
        foreach ($this->urgentTasks->take(5) as $task) {
            // Handle both EventDTO objects and arrays
            $taskData = is_array($task) ? $task : $task->toArray();
            
            $activities[] = [
                'id' => 'task_' . $taskData['id'],
                'type' => 'task',
                'title' => $taskData['name'],
                'description' => ($taskData['project']['name'] ?? '') . ' - ' . ($taskData['project']['client']['name'] ?? ''),
                'time' => $taskData['execution_date'] ?? $taskData['send_date'] ?? $taskData['created_at'],
                'icon' => $taskData['event_type'] === 'billing' ? 'euro' : 'check-circle',
                'color' => ($taskData['is_overdue'] ?? false) ? 'red' : 'orange',
                'link' => '/events/' . $taskData['id'],
            ];
        }

        // Add recent project updates
        foreach ($this->recentProjects->take(3) as $project) {
            // Handle both ProjectDTO objects and arrays
            $projectData = is_array($project) ? $project : $project->toArray();
            
            $activities[] = [
                'id' => 'project_' . $projectData['id'],
                'type' => 'project',
                'title' => 'Projet: ' . $projectData['name'],
                'description' => 'Client: ' . ($projectData['client']['name'] ?? ''),
                'time' => $projectData['updated_at'] ?? $projectData['created_at'],
                'icon' => 'folder',
                'color' => 'blue',
                'link' => '/projects/' . $projectData['id'],
            ];
        }

        // Sort by time and limit to 10
        usort($activities, function($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });

        return array_slice($activities, 0, 10);
    }

    /**
     * Format financial statistics
     */
    private function formatFinancialStats(): array
    {
        $stats = $this->statistics;
        
        return [
            'total_billed' => $stats['total_billed'] ?? 0,
            'total_paid' => $stats['total_paid'] ?? 0,
            'total_pending' => $stats['total_pending'] ?? 0,
            'total_upcoming_payment' => $stats['total_upcoming_payment'] ?? 0,
            'total_overdue_payment' => $stats['total_overdue_payment'] ?? 0,
        ];
    }

    /**
     * Get dashboard summary
     */
    private function getSummary(): array
    {
        return [
            'urgent_tasks_count' => $this->urgentTasks->count(),
            'upcoming_events_count' => $this->upcomingEvents->count(),
            'recent_projects_count' => $this->recentProjects->count(),
            'overdue_projects_count' => $this->recentProjects->filter(function ($project) {
                $projectData = is_array($project) ? $project : $project->toArray();
                return $projectData['is_overdue'] ?? false;
            })->count(),
            'high_priority_events' => $this->urgentTasks->filter(function ($event) {
                $eventData = is_array($event) ? $event : $event->toArray();
                return ($eventData['is_overdue'] ?? false);
            })->count(),
            'needs_attention' => $this->urgentTasks->count() > 0 ||
                               $this->recentProjects->filter(function ($project) {
                                   $projectData = is_array($project) ? $project : $project->toArray();
                                   return $projectData['is_overdue'] ?? false;
                               })->count() > 0,
        ];
    }

    /**
     * Format currency
     */
    private function formatCurrency(float $amount): string
    {
        return number_format($amount, 2, ',', ' ') . ' €';
    }

    /**
     * Test method to verify file is loaded
     */
    public function testMethod(): string
    {
        return 'File loaded successfully at ' . now();
    }

    /**
     * Get priority alerts
     */
    public function getPriorityAlerts(): array
    {
        $alerts = [];

        // Urgent tasks alert
        if ($this->urgentTasks->count() > 0) {
            $alerts[] = [
                'type' => 'urgent',
                'level' => 'high',
                'title' => 'Tâches urgentes',
                'message' => sprintf(
                    'Vous avez %d tâche(s) en retard qui nécessitent une attention immédiate.',
                    $this->urgentTasks->count()
                ),
                'count' => $this->urgentTasks->count(),
                'action_url' => '/events?filter=overdue',
            ];
        }

        // Overdue projects alert
        $overdueProjects = $this->recentProjects->filter(function ($project) {
            $projectData = is_array($project) ? $project : $project->toArray();
            return $projectData['is_overdue'] ?? false;
        })->count();

        if ($overdueProjects > 0) {
            $alerts[] = [
                'type' => 'overdue_projects',
                'level' => 'medium',
                'title' => 'Projets en retard',
                'message' => sprintf(
                    '%d projet(s) ont dépassé leur date limite.',
                    $overdueProjects
                ),
                'count' => $overdueProjects,
                'action_url' => '/projects?filter=overdue',
            ];
        }

        // High value pending payments
        $highValuePending = $this->urgentTasks->filter(function ($event) {
            $eventData = is_array($event) ? $event : $event->toArray();
            return ($eventData['event_type'] ?? '') === 'billing' &&
                   ($eventData['amount'] ?? 0) > 5000;
        })->count();

        if ($highValuePending > 0) {
            $alerts[] = [
                'type' => 'high_value_pending',
                'level' => 'medium',
                'title' => 'Paiements importants en attente',
                'message' => sprintf(
                    '%d facture(s) de montant élevé en attente de paiement.',
                    $highValuePending
                ),
                'count' => $highValuePending,
                'action_url' => '/events?type=billing&status=pending',
            ];
        }

        return $alerts;
    }
}
