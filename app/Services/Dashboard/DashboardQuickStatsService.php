<?php

declare(strict_types=1);

namespace App\Services\Dashboard;

use App\Models\Event;
use App\Models\Project;
use App\Enums\EventType;
use App\Enums\EventStatus;
use App\Enums\ProjectStatus;

class DashboardQuickStatsService
{
    public function getQuickStats(): array
    {
        $userId = auth()->id();

        return [
            'completion_rate' => $this->getCompletionRate($userId),
            'revenue_per_client' => $this->getRevenuePerClient($userId),
            'average_revenue_per_client' => $this->getAverageRevenuePerClient($userId),
            'revenue_growth_rate' => $this->getRevenueGrowthRate($userId),
            'active_projects' => $this->getActiveProjectsCount($userId),
            'pending_invoices' => $this->getPendingInvoicesCount($userId),
            'urgent_tasks' => $this->getUrgentTasksCount($userId),
        ];
    }

    private function getCompletionRate(int $userId): float
    {
        $totalProjects = Project::whereHas('client', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->whereIn('status', [ProjectStatus::Active->value, ProjectStatus::Completed->value])
        ->count();

        if ($totalProjects === 0) {
            return 0;
        }

        $completedProjects = Project::whereHas('client', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->where('status', ProjectStatus::Completed->value)
        ->count();

        return ($completedProjects / $totalProjects) * 100;
    }

    private function getRevenuePerClient(int $userId): float
    {
        $totalRevenue = Event::where('event_type', EventType::Billing->value)
            ->whereHas('project.client', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('payment_status', 'paid')
            ->sum('amount') ?? 0;

        $totalClients = Project::whereHas('client', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->distinct('client_id')->count('client_id');

        if ($totalClients === 0) {
            return 0;
        }

        return $totalRevenue / $totalClients;
    }

    private function getAverageRevenuePerClient(int $userId): float
    {
        // Pour cette démo, on utilise une valeur fixe
        // Dans un vrai système, on calculerait la moyenne historique
        return 5000;
    }

    private function getRevenueGrowthRate(int $userId): float
    {
        $current = $this->getRevenuePerClient($userId);
        $average = $this->getAverageRevenuePerClient($userId);

        if ($average === 0) {
            return 0;
        }

        return (($current - $average) / $average) * 100;
    }

    private function getActiveProjectsCount(int $userId): int
    {
        return Project::whereHas('client', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->where('status', ProjectStatus::Active->value)
        ->count();
    }

    private function getPendingInvoicesCount(int $userId): int
    {
        return Event::where('event_type', EventType::Billing->value)
            ->whereHas('project.client', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('payment_status', 'pending')
            ->count();
    }

    private function getUrgentTasksCount(int $userId): int
    {
        return Event::where('event_type', EventType::Step->value)
            ->whereHas('project.client', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('status', EventStatus::Todo->value)
            ->where('execution_date', '<=', now())
            ->count();
    }
}