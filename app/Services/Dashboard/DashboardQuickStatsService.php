<?php

declare(strict_types=1);

namespace App\Services\Dashboard;

use App\Repositories\Contracts\Dashboard\QuickStatsRepositoryInterface;

class DashboardQuickStatsService
{
    public function __construct(
        private readonly QuickStatsRepositoryInterface $quickStatsRepository
    ) {}

    /**
     * Get quick stats data with error handling
     * OPTIMIZED: 3 queries instead of 11
     */
    public function getQuickStats(): array
    {
        try {
            $userId = auth()->id();

            // Get all data from repository in 3 optimized queries
            $data = $this->quickStatsRepository->getQuickStatsData($userId);

            // Calculate derived metrics
            $completionRate = $this->calculateCompletionRate($data['total_projects'], $data['completed_projects']);
            $revenuePerClient = $this->calculateRevenuePerClient($data['total_paid_revenue'], $data['unique_clients']);
            $averageRevenuePerClient = $this->getAverageRevenuePerClient();
            $revenueGrowthRate = $this->calculateRevenueGrowthRate($revenuePerClient, $averageRevenuePerClient);

            return [
                'completion_rate' => $completionRate,
                'revenue_per_client' => $revenuePerClient,
                'average_revenue_per_client' => $averageRevenuePerClient,
                'revenue_growth_rate' => $revenueGrowthRate,
                'active_projects' => $data['active_projects'],
                'pending_invoices' => $data['pending_invoices'],
                'urgent_tasks' => $data['urgent_tasks'],
                'error' => null,
            ];
        } catch (\Exception $e) {
            // Return default values with error message
            return [
                'completion_rate' => 0.0,
                'revenue_per_client' => 0.0,
                'average_revenue_per_client' => 0.0,
                'revenue_growth_rate' => 0.0,
                'active_projects' => 0,
                'pending_invoices' => 0,
                'urgent_tasks' => 0,
                'error' => app()->environment('local')
                    ? $e->getMessage().' in '.$e->getFile().':'.$e->getLine()
                    : 'Erreur lors du chargement des statistiques rapides.',
            ];
        }
    }

    private function calculateCompletionRate(int $totalProjects, int $completedProjects): float
    {
        if ($totalProjects === 0) {
            return 0.0;
        }

        return ($completedProjects / $totalProjects) * 100;
    }

    private function calculateRevenuePerClient(float $totalRevenue, int $uniqueClients): float
    {
        if ($uniqueClients === 0) {
            return 0.0;
        }

        return $totalRevenue / $uniqueClients;
    }

    private function getAverageRevenuePerClient(): float
    {
        // Pour cette démo, on utilise une valeur fixe
        // Dans un vrai système, on calculerait la moyenne historique
        return 5000.0;
    }

    private function calculateRevenueGrowthRate(float $current, float $average): float
    {
        if ($average === 0) {
            return 0.0;
        }

        return (($current - $average) / $average) * 100;
    }
}
