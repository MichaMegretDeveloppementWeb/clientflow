<?php

namespace App\Repositories\Dashboard;

use App\Enums\EventType;
use App\Enums\PaymentStatus;
use App\Models\Event;
use App\Repositories\Contracts\Dashboard\RevenueChartRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RevenueChartRepository implements RevenueChartRepositoryInterface
{
    /**
     * Get daily revenue and projected data in optimized queries
     * OPTIMIZED: 2 queries instead of 2 × number_of_days queries
     */
    public function getDailyRevenueAndProjected(Carbon $startDate, Carbon $endDate, int $userId): array
    {
        // Single query for all daily revenue data
        $revenueResults = Event::select([
            DB::raw('DATE(events.paid_at) as date'),
            DB::raw('SUM(events.amount) as total'),
        ])
            ->where('events.event_type', EventType::Billing->value)
            ->where('events.payment_status', PaymentStatus::Paid->value)
            ->whereBetween('events.paid_at', [$startDate, $endDate])
            ->join('projects', 'events.project_id', '=', 'projects.id')
            ->join('clients', 'projects.client_id', '=', 'clients.id')
            ->where('clients.user_id', $userId)
            ->groupBy('date')
            ->pluck('total', 'date')
            ->toArray();

        // Single query for all daily projected data
        $projectedResults = Event::select([
            DB::raw('DATE(events.send_date) as date'),
            DB::raw('SUM(events.amount) as total'),
        ])
            ->where('events.event_type', EventType::Billing->value)
            ->where('events.status', 'sent')
            ->whereBetween('events.send_date', [$startDate, $endDate])
            ->join('projects', 'events.project_id', '=', 'projects.id')
            ->join('clients', 'projects.client_id', '=', 'clients.id')
            ->where('clients.user_id', $userId)
            ->groupBy('date')
            ->pluck('total', 'date')
            ->toArray();

        // Fill in missing dates with 0
        $revenueData = [];
        $projectedData = [];
        $totalDays = $startDate->diffInDays($endDate) + 1;

        for ($i = 0; $i < $totalDays; $i++) {
            $currentDate = $startDate->copy()->addDays($i)->format('Y-m-d');
            $revenueData[] = round($revenueResults[$currentDate] ?? 0, 2);
            $projectedData[] = round($projectedResults[$currentDate] ?? 0, 2);
        }

        return [$revenueData, $projectedData];
    }

    /**
     * Get weekly revenue and projected data in optimized queries
     * OPTIMIZED: 2 queries instead of 2 × number_of_weeks queries
     */
    public function getWeeklyRevenueAndProjected(Carbon $startDate, Carbon $endDate, int $userId): array
    {
        // Single query for all weekly revenue data
        $revenueResults = Event::select([
            DB::raw('YEAR(events.paid_at) as year'),
            DB::raw('WEEK(events.paid_at) as week'),
            DB::raw('SUM(events.amount) as total'),
        ])
            ->where('events.event_type', EventType::Billing->value)
            ->where('events.payment_status', PaymentStatus::Paid->value)
            ->whereBetween('events.paid_at', [$startDate->startOfWeek(), $endDate])
            ->join('projects', 'events.project_id', '=', 'projects.id')
            ->join('clients', 'projects.client_id', '=', 'clients.id')
            ->where('clients.user_id', $userId)
            ->groupBy('year', 'week')
            ->get()
            ->keyBy(function ($item) {
                return $item->year.'-'.$item->week;
            });

        // Single query for all weekly projected data
        $projectedResults = Event::select([
            DB::raw('YEAR(events.send_date) as year'),
            DB::raw('WEEK(events.send_date) as week'),
            DB::raw('SUM(events.amount) as total'),
        ])
            ->where('events.event_type', EventType::Billing->value)
            ->where('events.status', 'sent')
            ->whereBetween('events.send_date', [$startDate->startOfWeek(), $endDate])
            ->join('projects', 'events.project_id', '=', 'projects.id')
            ->join('clients', 'projects.client_id', '=', 'clients.id')
            ->where('clients.user_id', $userId)
            ->groupBy('year', 'week')
            ->get()
            ->keyBy(function ($item) {
                return $item->year.'-'.$item->week;
            });

        // Fill in missing weeks with 0
        $revenueData = [];
        $projectedData = [];
        $currentDate = $startDate->copy()->startOfWeek();

        while ($currentDate <= $endDate) {
            $weekKey = $currentDate->year.'-'.$currentDate->week;
            $revenueData[] = round($revenueResults[$weekKey]->total ?? 0, 2);
            $projectedData[] = round($projectedResults[$weekKey]->total ?? 0, 2);
            $currentDate->addWeek();
        }

        return [$revenueData, $projectedData];
    }

    /**
     * Get monthly revenue and projected data in optimized queries
     * OPTIMIZED: 2 queries instead of 2 × number_of_months queries
     */
    public function getMonthlyRevenueAndProjected(Carbon $startDate, Carbon $endDate, int $userId): array
    {
        // Single query for all monthly revenue data
        $revenueResults = Event::select([
            DB::raw('YEAR(events.paid_at) as year'),
            DB::raw('MONTH(events.paid_at) as month'),
            DB::raw('SUM(events.amount) as total'),
        ])
            ->where('events.event_type', EventType::Billing->value)
            ->where('events.payment_status', PaymentStatus::Paid->value)
            ->whereBetween('events.paid_at', [$startDate->startOfMonth(), $endDate])
            ->join('projects', 'events.project_id', '=', 'projects.id')
            ->join('clients', 'projects.client_id', '=', 'clients.id')
            ->where('clients.user_id', $userId)
            ->groupBy('year', 'month')
            ->get()
            ->keyBy(function ($item) {
                return $item->year.'-'.str_pad($item->month, 2, '0', STR_PAD_LEFT);
            });

        // Single query for all monthly projected data
        $projectedResults = Event::select([
            DB::raw('YEAR(events.send_date) as year'),
            DB::raw('MONTH(events.send_date) as month'),
            DB::raw('SUM(events.amount) as total'),
        ])
            ->where('events.event_type', EventType::Billing->value)
            ->where('events.status', 'sent')
            ->whereBetween('events.send_date', [$startDate->startOfMonth(), $endDate])
            ->join('projects', 'events.project_id', '=', 'projects.id')
            ->join('clients', 'projects.client_id', '=', 'clients.id')
            ->where('clients.user_id', $userId)
            ->groupBy('year', 'month')
            ->get()
            ->keyBy(function ($item) {
                return $item->year.'-'.str_pad($item->month, 2, '0', STR_PAD_LEFT);
            });

        // Fill in missing months with 0
        $revenueData = [];
        $projectedData = [];
        $currentDate = $startDate->copy()->startOfMonth();

        while ($currentDate <= $endDate) {
            $monthKey = $currentDate->format('Y-m');
            $revenueData[] = round($revenueResults[$monthKey]->total ?? 0, 2);
            $projectedData[] = round($projectedResults[$monthKey]->total ?? 0, 2);
            $currentDate->addMonth();
        }

        return [$revenueData, $projectedData];
    }

    /**
     * Get the first billing event date for a user
     */
    public function getFirstBillingEventDate(int $userId): ?Carbon
    {
        $firstEvent = Event::select('events.created_date')
            ->where('events.event_type', EventType::Billing->value)
            ->join('projects', 'events.project_id', '=', 'projects.id')
            ->join('clients', 'projects.client_id', '=', 'clients.id')
            ->where('clients.user_id', $userId)
            ->orderBy('events.created_date', 'asc')
            ->first();

        return $firstEvent ? Carbon::parse($firstEvent->created_date) : null;
    }

    /**
     * Get monthly revenue statistics
     */
    public function getMonthlyRevenueStats(Carbon $currentMonth, Carbon $lastMonth, Carbon $lastMonthEnd, int $userId): array
    {
        // Single query to get both current and last month revenue
        $stats = Event::select([
            DB::raw('SUM(CASE WHEN events.paid_at >= ? AND events.paid_at <= NOW() THEN events.amount ELSE 0 END) as current_revenue'),
            DB::raw('SUM(CASE WHEN events.paid_at >= ? AND events.paid_at <= ? THEN events.amount ELSE 0 END) as last_revenue'),
        ])
            ->where('events.event_type', EventType::Billing->value)
            ->where('events.payment_status', PaymentStatus::Paid->value)
            ->join('projects', 'events.project_id', '=', 'projects.id')
            ->join('clients', 'projects.client_id', '=', 'clients.id')
            ->where('clients.user_id', $userId)
            ->addBinding([$currentMonth, $lastMonth, $lastMonthEnd])
            ->first();

        $currentRevenue = $stats->current_revenue ?? 0;
        $lastRevenue = $stats->last_revenue ?? 0;

        $growth = $lastRevenue > 0
            ? round((($currentRevenue - $lastRevenue) / $lastRevenue) * 100, 1)
            : 0;

        return [
            'current_month' => (float) $currentRevenue,
            'last_month' => (float) $lastRevenue,
            'growth_percentage' => $growth,
            'is_positive' => $growth >= 0,
        ];
    }

    /**
     * Get yearly revenue summary
     */
    public function getYearlyRevenueSummary(Carbon $yearStart, int $userId): array
    {
        // Single query to get all yearly statistics
        $stats = Event::select([
            DB::raw('SUM(CASE WHEN events.payment_status = "paid" AND events.paid_at >= ? AND events.paid_at <= NOW() THEN events.amount ELSE 0 END) as total_revenue'),
            DB::raw('SUM(CASE WHEN events.status = "sent" AND events.send_date >= ? AND events.send_date <= NOW() THEN events.amount ELSE 0 END) as total_invoiced'),
            DB::raw('SUM(CASE WHEN events.payment_status = "pending" AND events.send_date >= ? AND events.send_date <= NOW() THEN events.amount ELSE 0 END) as total_pending'),
        ])
            ->where('events.event_type', EventType::Billing->value)
            ->join('projects', 'events.project_id', '=', 'projects.id')
            ->join('clients', 'projects.client_id', '=', 'clients.id')
            ->where('clients.user_id', $userId)
            ->addBinding([$yearStart, $yearStart, $yearStart])
            ->first();

        $totalRevenue = $stats->total_revenue ?? 0;
        $months = now()->month;
        $averageMonthly = $months > 0 ? round($totalRevenue / $months, 2) : 0;

        return [
            'total_revenue' => (float) $totalRevenue,
            'total_invoiced' => (float) ($stats->total_invoiced ?? 0),
            'total_pending' => (float) ($stats->total_pending ?? 0),
            'average_monthly' => (float) $averageMonthly,
        ];
    }
}
