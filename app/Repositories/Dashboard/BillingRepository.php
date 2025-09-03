<?php

namespace App\Repositories\Dashboard;

use App\Enums\EventType;
use App\Models\Event;
use App\Repositories\Contracts\Dashboard\BillingRepositoryInterface;
use Illuminate\Support\Facades\DB;

class BillingRepository implements BillingRepositoryInterface
{
    /**
     * Get billing statistics data
     * OPTIMIZED: Single JOIN query instead of whereHas subquery
     */
    public function getBillingStatistics(int $userId): array
    {
        // Get all billing statistics in one optimized query with JOIN
        $stats = Event::select([
            // Total amounts
            DB::raw('SUM(CASE WHEN events.status != "cancelled" THEN events.amount ELSE 0 END) as total_billed'),
            DB::raw('SUM(CASE WHEN events.status = "to_send" THEN events.amount ELSE 0 END) as total_to_send'),
            DB::raw('SUM(CASE WHEN events.status = "sent" THEN events.amount ELSE 0 END) as total_sent'),
            DB::raw('SUM(CASE WHEN events.status = "sent" AND events.payment_status = "paid" THEN events.amount ELSE 0 END) as total_paid'),
            DB::raw('SUM(CASE WHEN events.status = "sent" AND events.payment_status = "pending" AND events.payment_due_date < NOW() THEN events.amount ELSE 0 END) as total_overdue_payment'),
            DB::raw('SUM(CASE WHEN events.status = "sent" AND events.payment_status = "pending" AND (events.payment_due_date >= NOW() OR events.payment_due_date IS NULL) THEN events.amount ELSE 0 END) as total_upcoming_payment'),

            // Counts
            DB::raw('COUNT(CASE WHEN events.status = "to_send" THEN 1 END) as invoices_to_send_count'),
            DB::raw('COUNT(CASE WHEN events.status = "sent" AND events.payment_status = "pending" THEN 1 END) as unpaid_invoices_count'),
            DB::raw('COUNT(CASE WHEN events.status = "sent" AND events.payment_status = "pending" AND events.payment_due_date < NOW() THEN 1 END) as overdue_invoices_count'),
        ])
            ->where('events.event_type', EventType::Billing->value)
            ->join('projects', 'events.project_id', '=', 'projects.id')
            ->join('clients', 'projects.client_id', '=', 'clients.id')
            ->where('clients.user_id', $userId)
            ->first();

        return [
            'total_billed' => (float) ($stats->total_billed ?? 0),
            'total_to_send' => (float) ($stats->total_to_send ?? 0),
            'total_sent' => (float) ($stats->total_sent ?? 0),
            'total_paid' => (float) ($stats->total_paid ?? 0),
            'total_overdue_payment' => (float) ($stats->total_overdue_payment ?? 0),
            'total_upcoming_payment' => (float) ($stats->total_upcoming_payment ?? 0),
            'invoices_to_send_count' => (int) ($stats->invoices_to_send_count ?? 0),
            'unpaid_invoices_count' => (int) ($stats->unpaid_invoices_count ?? 0),
            'overdue_invoices_count' => (int) ($stats->overdue_invoices_count ?? 0),
        ];
    }
}
