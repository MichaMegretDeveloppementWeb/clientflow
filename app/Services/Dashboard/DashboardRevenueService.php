<?php

namespace App\Services\Dashboard;

use App\Models\Event;
use App\Enums\EventType;
use App\Enums\PaymentStatus;
use Carbon\Carbon;

class DashboardRevenueService
{
    /**
     * Get revenue data for chart with flexible period
     */
    public function getRevenueChartData(string $period = 'current_month'): array
    {
        $userId = auth()->id();

        // Déterminer les dates de début et fin selon la période
        [$startDate, $endDate, $granularity] = $this->getPeriodDates($period);

        // Récupérer les données selon la granularité
        if ($granularity === 'day') {
            $revenueData = $this->getDailyRevenue($startDate, $endDate, $userId);
            $projectedData = $this->getDailyProjected($startDate, $endDate, $userId);
            $labels = $this->generateDayLabels($startDate, $endDate);
        } elseif ($granularity === 'week') {
            $revenueData = $this->getWeeklyRevenue($startDate, $endDate, $userId);
            $projectedData = $this->getWeeklyProjected($startDate, $endDate, $userId);
            $labels = $this->generateWeekLabels($startDate, $endDate);
        } else {
            $revenueData = $this->getMonthlyRevenueForPeriod($startDate, $endDate, $userId);
            $projectedData = $this->getMonthlyProjectedForPeriod($startDate, $endDate, $userId);
            $labels = $this->generateMonthLabelsForPeriod($startDate, $endDate);
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Revenus réels',
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
        ];
    }

    /**
     * Get monthly revenue statistics
     */
    public function getMonthlyRevenueStats(): array
    {
        $userId = auth()->id();
        $currentMonth = now()->startOfMonth();
        $lastMonth = now()->subMonth()->startOfMonth();

        $currentRevenue = $this->getRevenueForPeriod($currentMonth, now(), $userId);
        $lastRevenue = $this->getRevenueForPeriod($lastMonth, $lastMonth->copy()->endOfMonth(), $userId);

        $growth = $lastRevenue > 0
            ? round((($currentRevenue - $lastRevenue) / $lastRevenue) * 100, 1)
            : 0;

        return [
            'current_month' => $currentRevenue,
            'last_month' => $lastRevenue,
            'growth_percentage' => $growth,
            'is_positive' => $growth >= 0,
        ];
    }

    /**
     * Get yearly revenue summary
     */
    public function getYearlyRevenueSummary(): array
    {
        $userId = auth()->id();
        $yearStart = now()->startOfYear();

        return [
            'total_revenue' => $this->getRevenueForPeriod($yearStart, now(), $userId),
            'total_invoiced' => $this->getInvoicedForPeriod($yearStart, now(), $userId),
            'total_pending' => $this->getPendingForPeriod($yearStart, now(), $userId),
            'average_monthly' => $this->getAverageMonthlyRevenue($userId),
        ];
    }

    /**
     * Get monthly revenue data
     */
    private function getMonthlyRevenue(Carbon $startDate, int $months, int $userId): array
    {
        $data = [];

        for ($i = 0; $i < $months; $i++) {
            $monthStart = $startDate->copy()->addMonths($i)->startOfMonth();
            $monthEnd = $monthStart->copy()->endOfMonth();

            $revenue = Event::where('event_type', EventType::Billing->value)
                ->where('payment_status', PaymentStatus::Paid->value)
                ->whereBetween('paid_at', [$monthStart, $monthEnd])
                ->whereHas('project.client', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->sum('amount');

            $data[] = round($revenue, 2);
        }

        return $data;
    }

    /**
     * Get monthly projected/invoiced data (factures envoyées uniquement)
     */
    private function getMonthlyProjected(Carbon $startDate, int $months, int $userId): array
    {
        $data = [];

        for ($i = 0; $i < $months; $i++) {
            $monthStart = $startDate->copy()->addMonths($i)->startOfMonth();
            $monthEnd = $monthStart->copy()->endOfMonth();

            $invoiced = Event::where('event_type', EventType::Billing->value)
                ->where('status', 'sent')
                ->whereBetween('send_date', [$monthStart, $monthEnd])
                ->whereHas('project.client', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->sum('amount');

            $data[] = round($invoiced, 2);
        }

        return $data;
    }

    /**
     * Generate month labels for chart
     */
    private function generateMonthLabels(int $months): array
    {
        $labels = [];
        $startDate = now()->subMonths($months - 1);

        for ($i = 0; $i < $months; $i++) {
            $labels[] = $startDate->copy()->addMonths($i)->format('M Y');
        }

        return $labels;
    }

    /**
     * Get revenue for a specific period
     */
    private function getRevenueForPeriod(Carbon $start, Carbon $end, int $userId): float
    {
        return Event::where('event_type', EventType::Billing->value)
            ->where('payment_status', PaymentStatus::Paid->value)
            ->whereBetween('paid_at', [$start, $end])
            ->whereHas('project.client', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->sum('amount');
    }

    /**
     * Get invoiced amount for a specific period (factures envoyées uniquement)
     */
    private function getInvoicedForPeriod(Carbon $start, Carbon $end, int $userId): float
    {
        return Event::where('event_type', EventType::Billing->value)
            ->where('status', 'sent')
            ->whereBetween('send_date', [$start, $end])
            ->whereHas('project.client', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->sum('amount');
    }

    /**
     * Get pending amount for a specific period
     */
    private function getPendingForPeriod(Carbon $start, Carbon $end, int $userId): float
    {
        return Event::where('event_type', EventType::Billing->value)
            ->where('payment_status', PaymentStatus::Pending->value)
            ->whereBetween('send_date', [$start, $end])
            ->whereHas('project.client', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->sum('amount');
    }

    /**
     * Calculate average monthly revenue
     */
    private function getAverageMonthlyRevenue(int $userId): float
    {
        $months = now()->month;
        $yearRevenue = $this->getRevenueForPeriod(now()->startOfYear(), now(), $userId);

        return $months > 0 ? round($yearRevenue / $months, 2) : 0;
    }

    /**
     * Determine period dates and granularity based on period type
     */
    private function getPeriodDates(string $period): array
    {
        $endDate = now()->endOfDay();

        switch ($period) {
            case 'current_month':
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
                // Trouver la première facture
                $firstEvent = Event::where('event_type', EventType::Billing->value)
                    ->whereHas('project.client', function ($query) {
                        $query->where('user_id', auth()->id());
                    })
                    ->orderBy('created_date', 'asc')
                    ->first();

                $startDate = $firstEvent ? Carbon::parse($firstEvent->created_date)->startOfDay() : now()->subYear()->startOfDay();

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
     * Get daily revenue data
     */
    private function getDailyRevenue(Carbon $startDate, Carbon $endDate, int $userId): array
    {
        $data = [];
        $totalDays = $startDate->diffInDays($endDate) + 1;

        for ($i = 0; $i < $totalDays; $i++) {
            $currentDate = $startDate->copy()->addDays($i);
            $dayStart = $currentDate->copy()->startOfDay();
            $dayEnd = $currentDate->copy()->endOfDay();

            $revenue = Event::where('event_type', EventType::Billing->value)
                ->where('payment_status', PaymentStatus::Paid->value)
                ->whereBetween('paid_at', [$dayStart, $dayEnd])
                ->whereHas('project.client', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->sum('amount');

            $data[] = round($revenue, 2);
        }

        return $data;
    }

    /**
     * Get daily projected data
     */
    private function getDailyProjected(Carbon $startDate, Carbon $endDate, int $userId): array
    {
        $data = [];
        $totalDays = $startDate->diffInDays($endDate) + 1;

        for ($i = 0; $i < $totalDays; $i++) {
            $currentDate = $startDate->copy()->addDays($i);
            $dayStart = $currentDate->copy()->startOfDay();
            $dayEnd = $currentDate->copy()->endOfDay();

            $invoiced = Event::where('event_type', EventType::Billing->value)
                ->where('status', 'sent')
                ->whereBetween('send_date', [$dayStart, $dayEnd])
                ->whereHas('project.client', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->sum('amount');

            $data[] = round($invoiced, 2);
        }

        return $data;
    }

    /**
     * Get weekly revenue data
     */
    private function getWeeklyRevenue(Carbon $startDate, Carbon $endDate, int $userId): array
    {
        $data = [];
        $currentDate = $startDate->copy()->startOfWeek();

        while ($currentDate <= $endDate) {
            $weekStart = $currentDate->copy();
            $weekEnd = $currentDate->copy()->endOfWeek();

            // Ne pas dépasser la date de fin
            if ($weekEnd > $endDate) {
                $weekEnd = $endDate->copy();
            }

            $revenue = Event::where('event_type', EventType::Billing->value)
                ->where('payment_status', PaymentStatus::Paid->value)
                ->whereBetween('paid_at', [$weekStart, $weekEnd])
                ->whereHas('project.client', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->sum('amount');

            $data[] = round($revenue, 2);
            $currentDate->addWeek();
        }

        return $data;
    }

    /**
     * Get weekly projected data
     */
    private function getWeeklyProjected(Carbon $startDate, Carbon $endDate, int $userId): array
    {
        $data = [];
        $currentDate = $startDate->copy()->startOfWeek();

        while ($currentDate <= $endDate) {
            $weekStart = $currentDate->copy();
            $weekEnd = $currentDate->copy()->endOfWeek();

            // Ne pas dépasser la date de fin
            if ($weekEnd > $endDate) {
                $weekEnd = $endDate->copy();
            }

            $invoiced = Event::where('event_type', EventType::Billing->value)
                ->where('status', 'sent')
                ->whereBetween('send_date', [$weekStart, $weekEnd])
                ->whereHas('project.client', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->sum('amount');

            $data[] = round($invoiced, 2);
            $currentDate->addWeek();
        }

        return $data;
    }

    /**
     * Get monthly revenue for a specific period
     */
    private function getMonthlyRevenueForPeriod(Carbon $startDate, Carbon $endDate, int $userId): array
    {
        $data = [];
        $currentDate = $startDate->copy()->startOfMonth();

        while ($currentDate <= $endDate) {
            $monthStart = $currentDate->copy();
            $monthEnd = $currentDate->copy()->endOfMonth();

            // Ne pas dépasser la date de fin
            if ($monthEnd > $endDate) {
                $monthEnd = $endDate->copy();
            }

            $revenue = Event::where('event_type', EventType::Billing->value)
                ->where('payment_status', PaymentStatus::Paid->value)
                ->whereBetween('paid_at', [$monthStart, $monthEnd])
                ->whereHas('project.client', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->sum('amount');

            $data[] = round($revenue, 2);
            $currentDate->addMonth();
        }

        return $data;
    }

    /**
     * Get monthly projected for a specific period
     */
    private function getMonthlyProjectedForPeriod(Carbon $startDate, Carbon $endDate, int $userId): array
    {
        $data = [];
        $currentDate = $startDate->copy()->startOfMonth();

        while ($currentDate <= $endDate) {
            $monthStart = $currentDate->copy();
            $monthEnd = $currentDate->copy()->endOfMonth();

            // Ne pas dépasser la date de fin
            if ($monthEnd > $endDate) {
                $monthEnd = $endDate->copy();
            }

            $invoiced = Event::where('event_type', EventType::Billing->value)
                ->where('status', 'sent')
                ->whereBetween('send_date', [$monthStart, $monthEnd])
                ->whereHas('project.client', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->sum('amount');

            $data[] = round($invoiced, 2);
            $currentDate->addMonth();
        }

        return $data;
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
            $labels[] = ($currentDate->format('d/m/Y') == $startDate->format('d/m/Y') || $currentDate->format('d/m/Y') == $endDate->format('d/m/Y')) ? $currentDate->format('d/m') : "";
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
            $labels[] = ($currentDate->format('d/m/Y') == $startDate->format('d/m/Y') || $currentDate->format('d/m/Y') == $endDate->format('d/m/Y')) ? $currentDate->format('d/m') : "";
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
