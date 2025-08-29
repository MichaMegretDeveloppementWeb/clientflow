<?php

namespace App\Services\Events;

use App\Repositories\Contracts\Events\EventUpdateRepositoryInterface;
use App\Models\Event;
use App\Enums\EventType;
use App\Enums\PaymentStatus;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EventUpdateService
{
    public function __construct(
        private readonly EventUpdateRepositoryInterface $eventRepository,
    ) {}

    /**
     * Update existing event
     */
    public function updateEvent(Event $event, array $data): Event
    {
        return DB::transaction(function () use ($event, $data) {
            $oldStatus = $event->status;
            $oldPaymentStatus = $event->payment_status;

            // Update the event
            $updatedEvent = $this->eventRepository->update($event, $data);

            // Handle status changes
            if (isset($data['status']) && $oldStatus !== $data['status']) {
                $this->handleStatusChange($updatedEvent, $oldStatus, $data['status']);
            }

            // Handle payment status changes
            if (isset($data['payment_status']) && $oldPaymentStatus !== $data['payment_status']) {
                $this->handlePaymentStatusChange($updatedEvent, $data['payment_status']);
            }

            // Log activity
            $this->logEventActivity($updatedEvent, 'updated');

            return $updatedEvent;
        });
    }

    /**
     * Handle status change
     */
    private function handleStatusChange(Event $event, string $oldStatus, string $newStatus): void
    {
        // Mark as completed
        if ($event->event_type === EventType::Step->value &&
            $newStatus === 'done' &&
            !$event->completed_at) {
            $event->update(['completed_at' => now()]);
        }

        // Clear completed date if reverting from done
        if ($event->event_type === EventType::Step->value &&
            $oldStatus === 'done' &&
            $newStatus !== 'done') {
            $event->update(['completed_at' => null]);
        }

        // Set send date when marking as sent
        if ($event->event_type === EventType::Billing->value &&
            $newStatus === 'sent' &&
            !$event->send_date) {
            $event->update(['send_date' => now()]);
        }

        // Clear completed date if reverting from to_send
        if ($event->event_type === EventType::Billing->value &&
            $oldStatus === 'sent' &&
            $newStatus !== 'sent') {
            $event->update(['completed_at' => null]);
        }
    }

    /**
     * Handle payment status change
     */
    private function handlePaymentStatusChange(Event $event, string $newStatus): void
    {
        if ($newStatus === PaymentStatus::Paid->value && !$event->paid_at) {
            $event->update(['paid_at' => now()]);
        }

        // Si le statut devient 'pending', forcer paid_at à null (logique backend)
        // Mais préserver la date de paiement dans les autres cas pour UX
        if ($newStatus === PaymentStatus::Pending->value) {
            $event->update(['paid_at' => null]);
        }
    }

    /**
     * Log event activity
     */
    private function logEventActivity(Event $event, string $action): void
    {
        \Log::info("Event {$action}", [
            'event_id' => $event->id,
            'event_name' => $event->name,
            'event_type' => $event->event_type,
            'project_id' => $event->project_id,
            'user_id' => auth()->id(),
            'action' => $action,
            'timestamp' => now(),
        ]);
    }
}
