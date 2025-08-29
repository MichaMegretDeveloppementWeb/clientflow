<?php

namespace App\QueryBuilders;

use App\Models\Client;
use App\Models\Project;
use App\Models\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class OptimizedQueries
{
    /**
     * Get dashboard statistics with minimal queries
     */
    public static function getDashboardStatistics(): array
    {
        // Single query for all counts using subqueries
        $stats = DB::select("
            SELECT 
                (SELECT COUNT(*) FROM clients) as total_clients,
                (SELECT COUNT(DISTINCT client_id) FROM projects WHERE status = 'active') as active_clients,
                (SELECT COUNT(*) FROM projects) as total_projects,
                (SELECT COUNT(*) FROM projects WHERE status = 'active') as active_projects,
                (SELECT COUNT(*) FROM projects WHERE status = 'completed') as completed_projects,
                (SELECT COUNT(*) FROM projects WHERE status = 'on_hold') as on_hold_projects,
                (SELECT COUNT(*) FROM projects WHERE status = 'cancelled') as cancelled_projects,
                (SELECT COUNT(*) FROM projects WHERE status = 'active' AND end_date < CURRENT_DATE) as overdue_projects,
                (SELECT COUNT(*) FROM events) as total_events,
                (SELECT COUNT(*) FROM events WHERE event_type = 'step' AND status = 'todo') as pending_tasks,
                (SELECT COUNT(*) FROM events WHERE event_type = 'step' AND status = 'done') as completed_tasks,
                (SELECT COUNT(*) FROM events WHERE event_type = 'step' AND status = 'todo' AND execution_date < CURRENT_DATE) as overdue_tasks,
                (SELECT COUNT(*) FROM events WHERE event_type = 'billing' AND payment_status = 'pending') as pending_invoices,
                (SELECT COUNT(*) FROM events WHERE event_type = 'billing' AND status = 'sent') as sent_invoices,
                (SELECT COALESCE(SUM(amount), 0) FROM events WHERE event_type = 'billing' AND payment_status = 'paid') as total_revenue,
                (SELECT COALESCE(SUM(amount), 0) FROM events WHERE event_type = 'billing' AND payment_status = 'pending') as pending_amount,
                (SELECT COALESCE(SUM(amount), 0) FROM events WHERE event_type = 'billing' AND payment_status = 'pending' AND payment_due_date < CURRENT_DATE) as overdue_amount
        ")[0];

        return (array) $stats;
    }

    /**
     * Get client list with optimized financial calculations
     */
    public static function getClientsWithFinancials(array $filters = []): Collection
    {
        $query = Client::select([
            'clients.*',
            DB::raw('(SELECT COUNT(*) FROM projects WHERE client_id = clients.id) as projects_count'),
            DB::raw('(SELECT COUNT(*) FROM projects WHERE client_id = clients.id AND status = "active") as active_projects_count'),
            DB::raw('(
                SELECT COALESCE(SUM(events.amount), 0)
                FROM events
                JOIN projects ON events.project_id = projects.id
                WHERE projects.client_id = clients.id
                AND events.event_type = "billing"
                AND events.payment_status = "paid"
            ) as total_revenue'),
            DB::raw('(
                SELECT COALESCE(SUM(events.amount), 0)
                FROM events
                JOIN projects ON events.project_id = projects.id
                WHERE projects.client_id = clients.id
                AND events.event_type = "billing"
                AND events.payment_status = "pending"
            ) as pending_amount'),
            DB::raw('(
                SELECT MAX(projects.updated_at)
                FROM projects
                WHERE projects.client_id = clients.id
            ) as last_project_update')
        ]);

        // Apply filters
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['has_active_projects'])) {
            $query->active();
        }

        if (!empty($filters['has_unpaid_invoices'])) {
            $query->withUnpaidInvoices();
        }

        return $query->orderBy('last_project_update', 'desc')
                    ->orderBy('total_revenue', 'desc')
                    ->get();
    }

    /**
     * Get projects with financial summary optimized
     */
    public static function getProjectsWithFinancials(array $filters = []): Collection
    {
        $query = Project::select([
            'projects.*',
            DB::raw('(SELECT COUNT(*) FROM events WHERE project_id = projects.id) as events_count'),
            DB::raw('(SELECT COUNT(*) FROM events WHERE project_id = projects.id AND event_type = "step" AND status = "done") as completed_tasks_count'),
            DB::raw('(SELECT COUNT(*) FROM events WHERE project_id = projects.id AND event_type = "step" AND status = "todo") as pending_tasks_count'),
            DB::raw('(
                SELECT COALESCE(SUM(amount), 0)
                FROM events
                WHERE project_id = projects.id
                AND event_type = "billing"
                AND status != "cancelled"
            ) as total_billed'),
            DB::raw('(
                SELECT COALESCE(SUM(amount), 0)
                FROM events
                WHERE project_id = projects.id
                AND event_type = "billing"
                AND payment_status = "paid"
            ) as total_paid'),
            DB::raw('(
                SELECT COALESCE(SUM(amount), 0)
                FROM events
                WHERE project_id = projects.id
                AND event_type = "billing"
                AND payment_status = "pending"
            ) as total_unpaid')
        ])
        ->with(['client:id,name,company']);

        // Apply filters
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['client_id'])) {
            $query->where('client_id', $filters['client_id']);
        }

        if (!empty($filters['overdue'])) {
            $query->overdue();
        }

        if (!empty($filters['budget_exceeded'])) {
            $query->budgetExceeded();
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get urgent tasks with minimal data transfer
     */
    public static function getUrgentTasksOptimized(int $limit = 10): Collection
    {
        return Event::select([
            'events.id',
            'events.project_id',
            'events.name',
            'events.event_type',
            'events.status',
            'events.amount',
            'events.execution_date',
            'events.send_date',
            'events.payment_due_date',
            'projects.name as project_name',
            'clients.name as client_name',
            'clients.company as client_company'
        ])
        ->join('projects', 'events.project_id', '=', 'projects.id')
        ->join('clients', 'projects.client_id', '=', 'clients.id')
        ->where(function ($query) {
            $query->where(function ($q) {
                // Overdue step events
                $q->where('events.event_type', 'step')
                  ->where('events.status', 'todo')
                  ->where('events.execution_date', '<', now());
            })
            ->orWhere(function ($q) {
                // Overdue billing events
                $q->where('events.event_type', 'billing')
                  ->where('events.status', 'to_send')
                  ->where('events.send_date', '<', now());
            })
            ->orWhere(function ($q) {
                // Overdue payments
                $q->where('events.event_type', 'billing')
                  ->where('events.payment_status', 'pending')
                  ->where('events.payment_due_date', '<', now());
            });
        })
        ->orderByRaw('
            CASE 
                WHEN events.event_type = "step" THEN events.execution_date
                WHEN events.event_type = "billing" AND events.status = "to_send" THEN events.send_date
                ELSE events.payment_due_date
            END ASC
        ')
        ->limit($limit)
        ->get();
    }

    /**
     * Get revenue data for chart with single query
     */
    public static function getRevenueChartData(int $months = 6): array
    {
        $startDate = now()->subMonths($months - 1)->startOfMonth();
        
        // Get paid revenue by month
        $paidRevenue = DB::select("
            WITH RECURSIVE months AS (
                SELECT ? as month_start
                UNION ALL
                SELECT DATE_ADD(month_start, INTERVAL 1 MONTH)
                FROM months
                WHERE month_start < DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
            )
            SELECT 
                DATE_FORMAT(months.month_start, '%Y-%m') as month,
                DATE_FORMAT(months.month_start, '%b %Y') as month_label,
                COALESCE(SUM(events.amount), 0) as paid_amount,
                COUNT(events.id) as paid_count
            FROM months
            LEFT JOIN events ON DATE_FORMAT(events.paid_at, '%Y-%m') = DATE_FORMAT(months.month_start, '%Y-%m')
                AND events.event_type = 'billing'
                AND events.payment_status = 'paid'
            GROUP BY months.month_start
            ORDER BY months.month_start
        ", [$startDate]);

        // Get invoiced amount by month
        $invoicedAmount = DB::select("
            WITH RECURSIVE months AS (
                SELECT ? as month_start
                UNION ALL
                SELECT DATE_ADD(month_start, INTERVAL 1 MONTH)
                FROM months
                WHERE month_start < DATE_SUB(CURDATE(), INTERVAL 1 MONTH)
            )
            SELECT 
                DATE_FORMAT(months.month_start, '%Y-%m') as month,
                COALESCE(SUM(events.amount), 0) as invoiced_amount
            FROM months
            LEFT JOIN events ON DATE_FORMAT(events.created_date, '%Y-%m') = DATE_FORMAT(months.month_start, '%Y-%m')
                AND events.event_type = 'billing'
                AND events.status IN ('to_send', 'sent')
            GROUP BY months.month_start
            ORDER BY months.month_start
        ", [$startDate]);

        return [
            'paid' => collect($paidRevenue),
            'invoiced' => collect($invoicedAmount),
        ];
    }

    /**
     * Get financial report with optimized aggregations
     */
    public static function getFinancialReport($startDate = null, $endDate = null): array
    {
        $baseQuery = Event::query()->where('event_type', 'billing');

        if ($startDate) {
            $baseQuery->where('send_date', '>=', $startDate);
        }

        if ($endDate) {
            $baseQuery->where('send_date', '<=', $endDate);
        }

        // Single query with multiple aggregations
        $summary = $baseQuery->selectRaw('
            COUNT(*) as total_invoices,
            SUM(amount) as total_amount,
            SUM(CASE WHEN payment_status = "paid" THEN amount ELSE 0 END) as paid_amount,
            SUM(CASE WHEN payment_status = "pending" THEN amount ELSE 0 END) as pending_amount,
            SUM(CASE WHEN payment_status = "pending" AND payment_due_date < CURDATE() THEN amount ELSE 0 END) as overdue_amount,
            COUNT(CASE WHEN payment_status = "paid" THEN 1 END) as paid_count,
            COUNT(CASE WHEN payment_status = "pending" THEN 1 END) as pending_count,
            AVG(amount) as average_amount
        ')->first();

        // Get top clients by revenue
        $topClients = $baseQuery
            ->join('projects', 'events.project_id', '=', 'projects.id')
            ->join('clients', 'projects.client_id', '=', 'clients.id')
            ->where('events.payment_status', 'paid')
            ->selectRaw('
                clients.id,
                clients.name,
                clients.company,
                SUM(events.amount) as total_revenue,
                COUNT(events.id) as invoice_count
            ')
            ->groupBy('clients.id', 'clients.name', 'clients.company')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        return [
            'summary' => $summary,
            'top_clients' => $topClients,
        ];
    }

    /**
     * Get project performance metrics optimized
     */
    public static function getProjectPerformanceMetrics(int $projectId): array
    {
        return DB::select("
            SELECT 
                p.id,
                p.name,
                p.budget,
                p.start_date,
                p.end_date,
                c.name as client_name,
                
                -- Task metrics
                (SELECT COUNT(*) FROM events WHERE project_id = p.id AND event_type = 'step') as total_tasks,
                (SELECT COUNT(*) FROM events WHERE project_id = p.id AND event_type = 'step' AND status = 'done') as completed_tasks,
                (SELECT COUNT(*) FROM events WHERE project_id = p.id AND event_type = 'step' AND status = 'todo') as pending_tasks,
                (SELECT COUNT(*) FROM events WHERE project_id = p.id AND event_type = 'step' AND status = 'todo' AND execution_date < CURDATE()) as overdue_tasks,
                
                -- Financial metrics
                (SELECT COALESCE(SUM(amount), 0) FROM events WHERE project_id = p.id AND event_type = 'billing' AND status != 'cancelled') as total_billed,
                (SELECT COALESCE(SUM(amount), 0) FROM events WHERE project_id = p.id AND event_type = 'billing' AND payment_status = 'paid') as total_paid,
                (SELECT COALESCE(SUM(amount), 0) FROM events WHERE project_id = p.id AND event_type = 'billing' AND payment_status = 'pending') as total_pending,
                
                -- Timeline metrics
                CASE 
                    WHEN p.start_date IS NOT NULL AND p.end_date IS NOT NULL 
                    THEN DATEDIFF(p.end_date, p.start_date)
                    ELSE NULL 
                END as planned_duration_days,
                
                CASE 
                    WHEN p.end_date IS NOT NULL AND p.end_date < CURDATE() AND p.status = 'active'
                    THEN DATEDIFF(CURDATE(), p.end_date)
                    ELSE 0
                END as days_overdue
                
            FROM projects p
            JOIN clients c ON p.client_id = c.id
            WHERE p.id = ?
        ", [$projectId])[0] ?? null;
    }

    /**
     * Search across all models optimized
     */
    public static function globalSearch(string $term, int $limit = 50): array
    {
        $searchTerm = '%' . $term . '%';
        
        // Search clients
        $clients = Client::select('id', 'name', 'company', 'email')
            ->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('company', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm);
            })
            ->limit($limit / 3)
            ->get()
            ->map(fn($client) => [
                'type' => 'client',
                'id' => $client->id,
                'title' => $client->name,
                'subtitle' => $client->company,
                'url' => "/clients/{$client->id}",
            ]);

        // Search projects
        $projects = Project::select('projects.id', 'projects.name', 'clients.name as client_name')
            ->join('clients', 'projects.client_id', '=', 'clients.id')
            ->where(function ($q) use ($searchTerm) {
                $q->where('projects.name', 'like', $searchTerm)
                  ->orWhere('clients.name', 'like', $searchTerm);
            })
            ->limit($limit / 3)
            ->get()
            ->map(fn($project) => [
                'type' => 'project',
                'id' => $project->id,
                'title' => $project->name,
                'subtitle' => "Client: {$project->client_name}",
                'url' => "/projects/{$project->id}",
            ]);

        // Search events
        $events = Event::select('events.id', 'events.name', 'events.event_type', 'projects.name as project_name')
            ->join('projects', 'events.project_id', '=', 'projects.id')
            ->where('events.name', 'like', $searchTerm)
            ->limit($limit / 3)
            ->get()
            ->map(fn($event) => [
                'type' => 'event',
                'id' => $event->id,
                'title' => $event->name,
                'subtitle' => "Projet: {$event->project_name}",
                'url' => "/events/{$event->id}",
            ]);

        return [
            'clients' => $clients,
            'projects' => $projects,
            'events' => $events,
            'total' => $clients->count() + $projects->count() + $events->count(),
        ];
    }
}