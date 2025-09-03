<?php

namespace App\Repositories\Dashboard;

use App\Enums\ProjectStatus;
use App\Models\Client;
use App\Models\Project;
use App\Repositories\Contracts\Dashboard\StatisticsRepositoryInterface;
use Illuminate\Support\Facades\DB;

class StatisticsRepository implements StatisticsRepositoryInterface
{
    /**
     * Get basic statistics for StatsGrid component
     * OPTIMIZED: Only what StatsGrid actually needs (6 values instead of 21)
     */
    public function getBasicStatistics(int $userId): array
    {
        // 1. Client statistics - only what StatsGrid needs
        $clientStats = Client::select([
            DB::raw('COUNT(*) as total_clients'),
            DB::raw('SUM(CASE WHEN MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW()) THEN 1 ELSE 0 END) as clients_this_month'),
        ])
            ->where('user_id', $userId)
            ->first();

        // 2. Project statistics - only what StatsGrid needs
        $projectStats = Project::select([
            DB::raw('SUM(CASE WHEN status = "'.ProjectStatus::Active->value.'" THEN 1 ELSE 0 END) as active_projects'),
            DB::raw('SUM(CASE WHEN status = "'.ProjectStatus::Completed->value.'" THEN 1 ELSE 0 END) as completed_projects'),
            DB::raw('SUM(CASE WHEN status = "'.ProjectStatus::OnHold->value.'" THEN 1 ELSE 0 END) as on_hold_projects'),
        ])
            ->join('clients', 'projects.client_id', '=', 'clients.id')
            ->where('clients.user_id', $userId)
            ->first();

        return [
            // Client data for StatsGrid
            'clients' => [
                'total' => (int) ($clientStats->total_clients ?? 0),
                'this_month' => (int) ($clientStats->clients_this_month ?? 0),
            ],

            // Project data for StatsGrid
            'projects' => [
                'active' => (int) ($projectStats->active_projects ?? 0),
                'completed' => (int) ($projectStats->completed_projects ?? 0),
                'on_hold' => (int) ($projectStats->on_hold_projects ?? 0),
            ],
        ];
    }
}
