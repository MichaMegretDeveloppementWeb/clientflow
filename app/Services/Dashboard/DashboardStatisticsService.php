<?php

namespace App\Services\Dashboard;

use App\Models\Client;
use App\Models\Event;
use App\Models\Project;
use App\Enums\EventStatus;
use App\Enums\EventType;
use App\Enums\PaymentStatus;
use App\Enums\ProjectStatus;
use Illuminate\Support\Facades\DB;

class DashboardStatisticsService
{
    /**
     * Get all dashboard statistics
     */
    public function getStatistics(): array
    {
        $userId = auth()->id();
        $projectsByStatus = $this->getProjectsByStatus($userId);
        
        return [
            'total_clients' => $this->getTotalClients($userId),
            'active_projects' => $this->getActiveProjects($userId),
            'completed_projects' => $projectsByStatus['completed'],
            'on_hold_projects' => $projectsByStatus['on_hold'],
            'cancelled_projects' => $projectsByStatus['cancelled'],
            'pending_tasks' => $this->getPendingTasks($userId),
            'unpaid_invoices' => $this->getUnpaidInvoices($userId),
            'monthly_revenue' => $this->getCurrentMonthRevenue($userId),
            'overdue_payments_amount' => $this->getOverduePaymentsAmount($userId),
            'completion_rate' => $this->getCompletionRate($userId),
            'projects_completed_this_week' => $this->getProjectsCompletedThisWeek($userId),
            'projects_completed_this_month' => $this->getProjectsCompletedThisMonth($userId),
            'on_hold_rate' => $this->getOnHoldRate($userId, $projectsByStatus),
            'total_billed' => $this->getTotalBilled($userId),
            'total_paid' => $this->getTotalPaid($userId),
            'total_pending' => $this->getTotalPending($userId),
            'total_upcoming_payment' => $this->getTotalUpcomingPayment($userId),
            'total_overdue_payment' => $this->getOverduePaymentsAmount($userId),
            'revenue_growth' => $this->getRevenueGrowth($userId),
            'client_growth_rate' => $this->getClientGrowthRate($userId),
            'clients_this_month' => $this->getClientsThisMonth($userId),
        ];
    }

    /**
     * Get total number of clients
     */
    private function getTotalClients(int $userId): int
    {
        return Client::where('user_id', $userId)->count();
    }

    /**
     * Get number of active projects
     */
    private function getActiveProjects(int $userId): int
    {
        return Project::where('status', ProjectStatus::Active->value)
            ->whereHas('client', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->count();
    }

    /**
     * Get number of pending tasks (todo step events)
     */
    private function getPendingTasks(int $userId): int
    {
        return Event::where('event_type', EventType::Step->value)
            ->where('status', EventStatus::Todo->value)
            ->whereHas('project.client', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->count();
    }

    /**
     * Get number of unpaid invoices
     */
    private function getUnpaidInvoices(int $userId): int
    {
        return Event::where('event_type', EventType::Billing->value)
            ->where('status', EventStatus::Sent->value)
            ->where('payment_status', PaymentStatus::Pending->value)
            ->whereHas('project.client', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->count();
    }

    /**
     * Get current month revenue (paid invoices)
     */
    private function getCurrentMonthRevenue(int $userId): float
    {
        return Event::where('event_type', EventType::Billing->value)
            ->where('payment_status', PaymentStatus::Paid->value)
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->whereHas('project.client', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->sum('amount');
    }

    /**
     * Get total amount of overdue payments
     */
    private function getOverduePaymentsAmount(int $userId): float
    {
        return Event::where('event_type', EventType::Billing->value)
            ->where('payment_status', PaymentStatus::Pending->value)
            ->whereDate('payment_due_date', '<', now())
            ->whereHas('project.client', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->sum('amount');
    }

    /**
     * Get projects count by status
     */
    private function getProjectsByStatus(int $userId): array
    {
        $statuses = Project::select('status', DB::raw('count(*) as count'))
            ->whereHas('client', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'active' => $statuses[ProjectStatus::Active->value] ?? 0,
            'completed' => $statuses[ProjectStatus::Completed->value] ?? 0,
            'on_hold' => $statuses[ProjectStatus::OnHold->value] ?? 0,
            'cancelled' => $statuses[ProjectStatus::Cancelled->value] ?? 0,
        ];
    }

    /**
     * Calculate overall task completion rate
     */
    private function getCompletionRate(int $userId): float
    {
        $totalTasks = Event::where('event_type', EventType::Step->value)
            ->whereIn('status', [EventStatus::Todo->value, EventStatus::Done->value])
            ->whereHas('project.client', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->count();

        if ($totalTasks === 0) {
            return 0;
        }

        $completedTasks = Event::where('event_type', EventType::Step->value)
            ->where('status', EventStatus::Done->value)
            ->whereHas('project.client', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->count();

        return round(($completedTasks / $totalTasks) * 100, 1);
    }

    /**
     * Get projects completed this week
     */
    private function getProjectsCompletedThisWeek(int $userId): int
    {
        return Project::where('status', ProjectStatus::Completed->value)
            ->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->whereHas('client', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->count();
    }

    /**
     * Get total billed amount
     */
    private function getTotalBilled(int $userId): float
    {
        return Event::where('event_type', EventType::Billing->value)
            ->whereNot('status', EventStatus::Cancelled->value)
            ->whereHas('project.client', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->sum('amount') ?? 0;
    }

    /**
     * Get total paid amount
     */
    private function getTotalPaid(int $userId): float
    {
        return Event::where('event_type', EventType::Billing->value)
            ->where('payment_status', PaymentStatus::Paid->value)
            ->whereHas('project.client', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->sum('amount') ?? 0;
    }

    /**
     * Get total pending amount
     */
    private function getTotalPending(int $userId): float
    {
        return Event::where('event_type', EventType::Billing->value)
            ->where('payment_status', PaymentStatus::Pending->value)
            ->whereHas('project.client', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->sum('amount') ?? 0;
    }

    /**
     * Get total upcoming payment amount (not yet due)
     */
    private function getTotalUpcomingPayment(int $userId): float
    {
        return Event::where('event_type', EventType::Billing->value)
            ->where('payment_status', PaymentStatus::Pending->value)
            ->where(function($query) {
                $query->where('payment_due_date', '>=', now())
                      ->orWhereNull('payment_due_date');
            })
            ->whereHas('project.client', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->sum('amount') ?? 0;
    }

    /**
     * Calculate revenue growth rate (month over month)
     */
    private function getRevenueGrowth(int $userId): float
    {
        $currentMonthRevenue = $this->getCurrentMonthRevenue($userId);
        
        $lastMonthRevenue = Event::where('event_type', EventType::Billing->value)
            ->where('payment_status', PaymentStatus::Paid->value)
            ->whereMonth('paid_at', now()->subMonth()->month)
            ->whereYear('paid_at', now()->subMonth()->year)
            ->whereHas('project.client', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->sum('amount');

        if ($lastMonthRevenue == 0) {
            return $currentMonthRevenue > 0 ? 100 : 0;
        }

        return round((($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1);
    }

    /**
     * Calculate client growth rate (month over month)
     */
    private function getClientGrowthRate(int $userId): float
    {
        $currentMonthClients = Client::where('user_id', $userId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        $lastMonthClients = Client::where('user_id', $userId)
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        if ($lastMonthClients == 0) {
            return $currentMonthClients > 0 ? 100 : 0;
        }

        return round((($currentMonthClients - $lastMonthClients) / $lastMonthClients) * 100, 1);
    }

    /**
     * Get projects completed this month
     */
    private function getProjectsCompletedThisMonth(int $userId): int
    {
        return Project::where('status', ProjectStatus::Completed->value)
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->whereHas('client', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->count();
    }

    /**
     * Calculate on hold rate (percentage of active projects that are on hold)
     */
    private function getOnHoldRate(int $userId, array $projectsByStatus): float
    {
        $totalActiveProjects = $projectsByStatus['active'] + $projectsByStatus['on_hold'];
        
        if ($totalActiveProjects === 0) {
            return 0;
        }

        return round(($projectsByStatus['on_hold'] / $totalActiveProjects) * 100, 1);
    }

    /**
     * Get clients created this month
     */
    private function getClientsThisMonth(int $userId): int
    {
        return Client::where('user_id', $userId)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }
}