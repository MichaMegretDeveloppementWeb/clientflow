<?php

namespace App\Repositories\Dashboard;

use App\Enums\EventStatus;
use App\Enums\EventType;
use App\Enums\PaymentStatus;
use App\Enums\ProjectStatus;
use App\Models\Event;
use App\Models\Project;
use App\Repositories\Contracts\Dashboard\QuickStatsRepositoryInterface;
use Illuminate\Support\Facades\DB;

class QuickStatsRepository implements QuickStatsRepositoryInterface
{
    /**
     * Get all quick stats data in optimized queries
     * OPTIMIZED: 3 queries instead of 11
     */
    public function getQuickStatsData(int $userId): array
    {
        // Query 1: Project statistics (completion rate + active projects count)
        $projectStats = Project::select([
            DB::raw('COUNT(*) as total_projects'),
            DB::raw('SUM(CASE WHEN projects.status = "'.ProjectStatus::Completed->value.'" THEN 1 ELSE 0 END) as completed_projects'),
            DB::raw('SUM(CASE WHEN projects.status = "'.ProjectStatus::Active->value.'" THEN 1 ELSE 0 END) as active_projects'),
            DB::raw('COUNT(DISTINCT projects.client_id) as unique_clients'),
        ])
            ->join('clients', 'projects.client_id', '=', 'clients.id')
            ->where('clients.user_id', $userId)
            ->whereIn('projects.status', [ProjectStatus::Active->value, ProjectStatus::Completed->value])
            ->first();

        // Query 2: Revenue statistics (total paid revenue + pending invoices)
        $revenueStats = Event::select([
            DB::raw('SUM(CASE WHEN events.payment_status = "'.PaymentStatus::Paid->value.'" THEN events.amount ELSE 0 END) as total_paid_revenue'),
            DB::raw('COUNT(CASE WHEN events.payment_status = "'.PaymentStatus::Pending->value.'" THEN 1 END) as pending_invoices'),
        ])
            ->where('events.event_type', EventType::Billing->value)
            ->join('projects', 'events.project_id', '=', 'projects.id')
            ->join('clients', 'projects.client_id', '=', 'clients.id')
            ->where('clients.user_id', $userId)
            ->first();

        // Query 3: Urgent tasks count
        $urgentTasks = Event::where('events.event_type', EventType::Step->value)
            ->join('projects', 'events.project_id', '=', 'projects.id')
            ->join('clients', 'projects.client_id', '=', 'clients.id')
            ->where('clients.user_id', $userId)
            ->where('events.status', EventStatus::Todo->value)
            ->where('events.execution_date', '<=', now())
            ->count();

        return [
            'total_projects' => (int) ($projectStats->total_projects ?? 0),
            'completed_projects' => (int) ($projectStats->completed_projects ?? 0),
            'active_projects' => (int) ($projectStats->active_projects ?? 0),
            'unique_clients' => (int) ($projectStats->unique_clients ?? 0),
            'total_paid_revenue' => (float) ($revenueStats->total_paid_revenue ?? 0),
            'pending_invoices' => (int) ($revenueStats->pending_invoices ?? 0),
            'urgent_tasks' => (int) $urgentTasks,
        ];
    }
}
