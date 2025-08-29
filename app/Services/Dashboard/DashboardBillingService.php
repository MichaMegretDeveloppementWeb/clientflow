<?php

namespace App\Services\Dashboard;

use App\Models\Event;
use App\Enums\EventType;
use App\Enums\EventStatus;
use App\Enums\PaymentStatus;
use Carbon\Carbon;

class DashboardBillingService
{
    /**
     * Get billing cards data for dashboard
     */
    public function getBillingCardsData(): array
    {
        $userId = auth()->id();

        return [
            'total_billed' => $this->getTotalBilled($userId),
            'total_to_send' => $this->getTotalToSend($userId),
            'total_sent' => $this->getTotalSent($userId),
            'total_paid' => $this->getTotalPaid($userId),
            'total_overdue_payment' => $this->getTotalOverduePayment($userId),
            'total_upcoming_payment' => $this->getTotalUpcomingPayment($userId),
            'payment_rate' => $this->getPaymentRate($userId),
            'invoices_to_send_count' => $this->getInvoicesToSendCount($userId),
            'unpaid_invoices_count' => $this->getUnpaidInvoicesCount($userId),
            'overdue_invoices_count' => $this->getOverdueInvoicesCount($userId),
        ];
    }

    /**
     * Get total billed amount (all non-cancelled billing events)
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
     * Get total amount of invoices to send
     */
    private function getTotalToSend(int $userId): float
    {
        return Event::where('event_type', EventType::Billing->value)
            ->where('status', EventStatus::ToSend->value)
            ->whereHas('project.client', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->sum('amount') ?? 0;
    }

    /**
     * Get total amount of invoices to send
     */
    private function getTotalSent(int $userId): float
    {
        return Event::where('event_type', EventType::Billing->value)
            ->where('status', EventStatus::Sent->value)
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
            ->where('status', EventStatus::Sent->value)
            ->where('payment_status', PaymentStatus::Paid->value)
            ->whereHas('project.client', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->sum('amount') ?? 0;
    }

    /**
     * Get total upcoming payment amount (sent, not yet due or no due date)
     */
    private function getTotalUpcomingPayment(int $userId): float
    {
        return Event::where('event_type', EventType::Billing->value)
            ->where('status', EventStatus::Sent->value)
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
     * Get total overdue payment amount (sent and past due)
     */
    private function getTotalOverduePayment(int $userId): float
    {
        return Event::where('event_type', EventType::Billing->value)
            ->where('status', EventStatus::Sent->value)
            ->where('payment_status', PaymentStatus::Pending->value)
            ->whereDate('payment_due_date', '<', now())
            ->whereHas('project.client', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->sum('amount') ?? 0;
    }

    /**
     * Calculate payment rate (paid / billed)
     */
    private function getPaymentRate(int $userId): float
    {
        $totalBilled = $this->getTotalBilled($userId);

        if ($totalBilled == 0) {
            return 0;
        }

        $totalPaid = $this->getTotalPaid($userId);

        return round(($totalPaid / $totalBilled) * 100, 1);
    }

    /**
     * Get count of invoices to send
     */
    private function getInvoicesToSendCount(int $userId): int
    {
        return Event::where('event_type', EventType::Billing->value)
            ->where('status', EventStatus::ToSend->value)
            ->whereHas('project.client', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->count();
    }

    /**
     * Get count of unpaid invoices
     */
    private function getUnpaidInvoicesCount(int $userId): int
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
     * Get count of overdue invoices (sent and past due)
     */
    private function getOverdueInvoicesCount(int $userId): int
    {
        return Event::where('event_type', EventType::Billing->value)
            ->where('status', EventStatus::Sent->value)
            ->where('payment_status', PaymentStatus::Pending->value)
            ->whereDate('payment_due_date', '<', now())
            ->whereHas('project.client', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->count();
    }
}
