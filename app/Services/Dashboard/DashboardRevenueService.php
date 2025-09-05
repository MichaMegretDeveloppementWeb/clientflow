<?php

namespace App\Services\Dashboard;

use App\Repositories\Contracts\Dashboard\RevenueChartRepositoryInterface;
use Carbon\Carbon;

class DashboardRevenueService
{
    public function __construct(
        private readonly RevenueChartRepositoryInterface $revenueChartRepository
    ) {}

    /**
     * Get revenue data for chart with flexible period
     */
    public function getRevenueChartData(string $period = 'current_month'): array
    {
        try {
            $userId = auth()->id();

            // Déterminer les dates de début et fin selon la période
            [$startDate, $endDate, $granularity] = $this->getPeriodDates($period);

            // OPTIMIZED: Use repository to get both revenue and projected data
            if ($granularity === 'day') {
                [$revenueData, $projectedData] = $this->revenueChartRepository->getDailyRevenueAndProjected($startDate, $endDate, $userId);
                $labels = $this->generateDayLabels($startDate, $endDate);
            } elseif ($granularity === 'week') {
                [$revenueData, $projectedData] = $this->revenueChartRepository->getWeeklyRevenueAndProjected($startDate, $endDate, $userId);
                $labels = $this->generateWeekLabels($startDate, $endDate);
            } else {
                [$revenueData, $projectedData] = $this->revenueChartRepository->getMonthlyRevenueAndProjected($startDate, $endDate, $userId);
                $labels = $this->generateMonthLabelsForPeriod($startDate, $endDate);
            }

            return [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Revenus perçus',
                        'data' => $revenueData,
                        'borderColor' => 'rgb(34, 197, 94)',
                        'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                        'tension' => 0.3,
                    ],
                    [
                        'label' => 'Facturé',
                        'data' => $projectedData,
                        'borderColor' => 'rgb(59, 130, 246)',
                        'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                        'tension' => 0.3,
                    ],
                ],
                'granularity' => $granularity,
                'period' => $period,
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'error' => null,
            ];
        } catch (\Exception $e) {
            // Structure d'erreur cohérente pour le frontend
            return [
                'labels' => [],
                'datasets' => [],
                'granularity' => 'month',
                'period' => $period,
                'start_date' => null,
                'end_date' => null,
                'error' => app()->environment('local')
                    ? $e->getMessage().' in '.$e->getFile().':'.$e->getLine()
                    : 'Une erreur est survenue lors du chargement des données de revenus.',
            ];
        }
    }

    /**
     * Get monthly revenue statistics
     * OPTIMIZED: Single query instead of two separate queries
     */
    public function getMonthlyRevenueStats(): array
    {
        try {
            $userId = auth()->id();
            $currentMonth = now()->startOfMonth();
            $lastMonth = now()->subMonth()->startOfMonth();
            $lastMonthEnd = $lastMonth->copy()->endOfMonth();

            // Use repository for monthly revenue statistics
            return $this->revenueChartRepository->getMonthlyRevenueStats($currentMonth, $lastMonth, $lastMonthEnd, $userId);
        } catch (\Exception $e) {
            return [
                'current_month' => 0.0,
                'last_month' => 0.0,
                'growth_percentage' => 0,
                'is_positive' => false,
                'error' => app()->environment('local')
                    ? $e->getMessage().' in '.$e->getFile().':'.$e->getLine()
                    : 'Erreur lors du chargement des statistiques mensuelles.',
            ];
        }
    }

    /**
     * Get yearly revenue summary
     * OPTIMIZED: Single query instead of four separate queries
     */
    public function getYearlyRevenueSummary(): array
    {
        try {
            $userId = auth()->id();
            $yearStart = now()->startOfYear();

            // Use repository for yearly revenue summary
            return $this->revenueChartRepository->getYearlyRevenueSummary($yearStart, $userId);
        } catch (\Exception $e) {
            return [
                'total_revenue' => 0.0,
                'total_invoiced' => 0.0,
                'total_pending' => 0.0,
                'average_monthly' => 0.0,
                'error' => app()->environment('local')
                    ? $e->getMessage().' in '.$e->getFile().':'.$e->getLine()
                    : 'Erreur lors du chargement du résumé annuel.',
            ];
        }
    }

    /**
     * Determine period dates and granularity based on period type
     */
    private function getPeriodDates(string $period): array
    {
        $endDate = now()->endOfDay();

        switch ($period) {
            case 'current_month':
                $endDate = now()->endOfMonth();
                $startDate = now()->startOfMonth();
                $granularity = 'day';
                break;

            case '7days':
                $startDate = now()->subDays(6)->startOfDay();
                $granularity = 'day';
                break;

            case '30days':
                $startDate = now()->subDays(29)->startOfDay();
                $granularity = 'day';
                break;

            case '3months':
                $startDate = now()->subMonths(3)->addDay()->startOfDay();
                $granularity = 'week';
                break;

            case '6months':
                $startDate = now()->subMonths(6)->addDay()->startOfDay();
                $granularity = 'month';
                break;

            case '12months':
                $startDate = now()->subMonths(12)->addDay()->startOfDay();
                $granularity = 'month';
                break;

            case 'all':
                // Utiliser le repository pour trouver la première facture
                $firstEventDate = $this->revenueChartRepository->getFirstBillingEventDate(auth()->id());
                $startDate = $firstEventDate ? $firstEventDate->startOfDay() : now()->subYear()->startOfDay();

                // Déterminer la granularité selon la durée
                $monthsDiff = $startDate->diffInMonths($endDate);
                if ($monthsDiff >= 6) {
                    $granularity = 'month';
                } elseif ($monthsDiff >= 3) {
                    $granularity = 'week';
                } else {
                    $granularity = 'day';
                }
                break;

            default:
                $startDate = now()->subMonths(6)->addDay()->startOfDay();
                $granularity = 'month';
        }

        return [$startDate, $endDate, $granularity];
    }

    /**
     * Generate day labels (only first and last)
     */
    private function generateDayLabels(Carbon $startDate, Carbon $endDate): array
    {
        $totalDays = $startDate->diffInDays($endDate) + 1;
        $labels = [];

        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $labels[] = ($currentDate->format('d/m/Y') == $startDate->format('d/m/Y') || $currentDate->format('d/m/Y') == $endDate->format('d/m/Y')) ? $currentDate->format('d/m') : '';
            $currentDate->addDay();
        }

        return $labels;
    }

    /**
     * Generate week labels (only first and last)
     */
    private function generateWeekLabels(Carbon $startDate, Carbon $endDate): array
    {
        $labels = [];
        $endDate->startOfWeek();
        $startDate->startOfWeek();
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $labels[] = ($currentDate->format('d/m/Y') == $startDate->format('d/m/Y') || $currentDate->format('d/m/Y') == $endDate->format('d/m/Y')) ? $currentDate->format('d/m') : '';
            $currentDate->addWeek();
        }

        return $labels;
    }

    /**
     * Generate month labels (only first and last)
     */
    private function generateMonthLabelsForPeriod(Carbon $startDate, Carbon $endDate): array
    {
        $labels = [];
        $currentDate = $startDate->copy()->startOfMonth();

        while ($currentDate <= $endDate) {
            $labels[] = $currentDate->format('M y');
            $currentDate->addMonth();
        }

        return $labels;
    }
}
