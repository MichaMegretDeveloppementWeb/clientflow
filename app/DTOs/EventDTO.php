<?php

namespace App\DTOs;

use App\Models\Event;
use App\Enums\EventType;
use App\Enums\EventStatus;
use App\Enums\PaymentStatus;
use App\Enums\EventCategory;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class EventDTO
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $projectId,
        public readonly string $name,
        public readonly ?string $description,
        public readonly ?string $type,
        public readonly string $eventType,
        public readonly string $status,
        public readonly Carbon $createdDate,

        // Step event fields
        public readonly ?Carbon $executionDate = null,
        public readonly ?Carbon $completedAt = null,

        // Billing event fields
        public readonly ?float $amount = null,
        public readonly ?string $paymentStatus = null,
        public readonly ?Carbon $sendDate = null,
        public readonly ?Carbon $paymentDueDate = null,
        public readonly ?Carbon $paidAt = null,

        // Related data
        public readonly ?ProjectDTO $project = null,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
    ) {}

    /**
     * Create DTO from model
     */
    public static function fromModel(Event $event): self
    {
        return new self(
            id: $event->id,
            projectId: $event->project_id,
            name: $event->name,
            description: $event->description,
            type: $event->type,
            eventType: $event->event_type,
            status: $event->status,
            createdDate: $event->created_date,
            executionDate: $event->execution_date,
            completedAt: $event->completed_at,
            amount: $event->amount,
            paymentStatus: $event->payment_status,
            sendDate: $event->send_date,
            paymentDueDate: $event->payment_due_date,
            paidAt: $event->paid_at,
            project: $event->relationLoaded('project') && $event->project
                ? ProjectDTO::fromModel($event->project)
                : null,
            createdAt: $event->created_at?->toISOString(),
            updatedAt: $event->updated_at?->toISOString(),
        );
    }


    /**
     * Create DTO from array data (for dashboard services)
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            projectId: $data['project']['id'] ?? 0,
            name: $data['name'] ?? '',
            description: $data['description'] ?? null,
            type: $data['type'] ?? null,
            eventType: $data['event_type'] ?? '',
            status: $data['status'] ?? '',
            createdDate: isset($data['created_date'])
                ? Carbon::parse($data['created_date'])
                : now(),
            executionDate: isset($data['execution_date'])
                ? Carbon::parse($data['execution_date'])
                : null,
            completedAt: isset($data['completed_at'])
                ? Carbon::parse($data['completed_at'])
                : null,
            amount: isset($data['amount']) ? floatval($data['amount']) : null,
            paymentStatus: $data['payment_status'] ?? null,
            sendDate: isset($data['send_date'])
                ? Carbon::parse($data['send_date'])
                : null,
            paymentDueDate: isset($data['payment_due_date'])
                ? Carbon::parse($data['payment_due_date'])
                : null,
            paidAt: isset($data['paid_at'])
                ? Carbon::parse($data['paid_at'])
                : null,
            project: isset($data['project']) ? ProjectDTO::fromArray($data['project']) : null,
        );
    }

    /**
     * Convert to array for API response
     */
    public function toArray(): array
    {
        $eventTypeEnum = EventType::from($this->eventType);
        $statusEnum = EventStatus::from($this->status);
        $typeEnum = $this->type ? EventCategory::tryFrom($this->type) : null;

        $data = [
            'id' => $this->id,
            'project_id' => $this->projectId,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'type_label' => $typeEnum?->label(),
            'event_type' => $this->eventType,
            'event_type_label' => $eventTypeEnum->label(),
            'status' => $this->status,
            'status_label' => $statusEnum->label(),
            'status_color' => $statusEnum->color(),
            'created_date' => $this->createdDate->format('Y-m-d'),
            'formatted_created_date' => $this->createdDate->format('d/m/Y'),
            'is_overdue' => $this->isOverdue(),
            'project' => $this->project?->toArray(),
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];

        // Add step event specific fields
        if ($this->eventType === EventType::Step->value) {
            $data['execution_date'] = $this->executionDate?->format('Y-m-d');
            $data['formatted_execution_date'] = $this->executionDate?->format('d/m/Y');
            $data['completed_at'] = $this->completedAt?->format('Y-m-d H:i:s');
            $data['formatted_completed_at'] = $this->completedAt?->format('d/m/Y à H:i');
            $data['days_until_due'] = $this->calculateDaysUntilDue();
        }

        // Add billing event specific fields
        if ($this->eventType === EventType::Billing->value) {
            $paymentStatusEnum = $this->paymentStatus
                ? PaymentStatus::from($this->paymentStatus)
                : null;

            $data['amount'] = $this->amount;
            $data['formatted_amount'] = $this->formatCurrency($this->amount);
            $data['payment_status'] = $this->paymentStatus;
            $data['payment_status_label'] = $paymentStatusEnum?->label();
            $data['payment_status_color'] = $paymentStatusEnum?->color();
            $data['send_date'] = $this->sendDate?->format('Y-m-d');
            $data['formatted_send_date'] = $this->sendDate?->format('d/m/Y');
            $data['payment_due_date'] = $this->paymentDueDate?->format('Y-m-d');
            $data['formatted_payment_due_date'] = $this->paymentDueDate?->format('d/m/Y');
            $data['paid_at'] = $this->paidAt?->format('Y-m-d');
            $data['formatted_paid_at'] = $this->paidAt?->format('d/m/Y');
            $data['is_payment_overdue'] = $this->isPaymentOverdue();
            $data['days_until_payment_due'] = $this->calculateDaysUntilPaymentDue();
        }

        return $data;
    }


    /**
     * Create collection of DTOs from models
     */
    public static function collection(Collection $events): Collection
    {
        return $events->map(fn(Event $event) => self::fromModel($event));
    }

    /**
     * Check if event is overdue (only considers days, not hours)
     */
    public function isOverdue(): bool
    {
        if ($this->eventType === EventType::Step->value) {
            return $this->status === EventStatus::Todo->value &&
                   $this->executionDate &&
                   $this->executionDate->startOfDay()->lt(now()->startOfDay());
        }

        if ($this->eventType === EventType::Billing->value) {
            return $this->status === EventStatus::ToSend->value &&
                   $this->sendDate &&
                   $this->sendDate->startOfDay()->lt(now()->startOfDay());
        }

        return false;
    }

    /**
     * Check if payment is overdue (only considers days, not hours)
     */
    public function isPaymentOverdue(): bool
    {
        return $this->eventType === EventType::Billing->value &&
               $this->paymentStatus === PaymentStatus::Pending->value &&
               $this->paymentDueDate &&
               $this->paymentDueDate->startOfDay()->lt(now()->startOfDay());
    }

    /**
     * Calculate days until due
     */
    private function calculateDaysUntilDue(): ?int
    {
        if (!$this->executionDate) {
            return null;
        }

        return (int) now()->diffInDays($this->executionDate, false);
    }

    /**
     * Calculate days until payment due
     */
    private function calculateDaysUntilPaymentDue(): ?int
    {
        if (!$this->paymentDueDate) {
            return null;
        }

        return (int) now()->diffInDays($this->paymentDueDate, false);
    }

    /**
     * Format currency
     */
    private function formatCurrency(?float $amount): string
    {
        if ($amount === null) {
            return '0,00 €';
        }

        return number_format($amount, 2, ',', ' ') . ' €';
    }






    /**
     * Convert to array for event detail page (mirrors Event model's toArray structure)
     */
    public function toDetailArray(): array
    {
        // Base structure identical to Event model's toArray()
        $data = [
            'id' => $this->id,
            'project_id' => $this->projectId,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'event_type' => $this->eventType,
            'status' => $this->status,
            'amount' => $this->amount,
            'payment_status' => $this->paymentStatus,
            'created_date' => $this->createdDate,
            'execution_date' => $this->executionDate,
            'send_date' => $this->sendDate,
            'payment_due_date' => $this->paymentDueDate,
            'completed_at' => $this->completedAt,
            'paid_at' => $this->paidAt,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,

            // Accessors from Event model (exact same logic)
            'event_type_label' => $this->getModelEventTypeLabel(),
            'status_label' => $this->getModelStatusLabel(),
            'payment_status_label' => $this->getModelPaymentStatusLabel(),
            'formatted_amount' => $this->getModelFormattedAmount(),
            'is_overdue' => $this->getModelIsOverdue(),
            'is_payment_overdue' => $this->getModelIsPaymentOverdue(),
            'is_todo' => in_array($this->status, ['todo', 'to_send']),

            'project' => $this->project?->toArray(),
        ];

        return $data;
    }

    /**
     * Get event type label using exact model logic
     */
    private function getModelEventTypeLabel(): string
    {
        $labels = [
            'step' => 'Étape',
            'billing' => 'Facturation',
        ];
        return $labels[$this->eventType] ?? $this->eventType;
    }

    /**
     * Get status label using exact model logic
     */
    private function getModelStatusLabel(): string
    {
        $labels = [
            'todo' => 'À faire',
            'done' => 'Fait',
            'to_send' => 'À envoyer',
            'sent' => 'Envoyé',
            'cancelled' => 'Annulé',
        ];
        return $labels[$this->status] ?? $this->status;
    }

    /**
     * Get payment status label using exact model logic
     */
    private function getModelPaymentStatusLabel(): ?string
    {
        if ($this->eventType !== 'billing') {
            return null;
        }
        $labels = [
            'paid' => 'Payé',
            'pending' => 'En attente',
        ];
        return $labels[$this->paymentStatus] ?? $this->paymentStatus;
    }

    /**
     * Get formatted amount using exact model logic
     */
    private function getModelFormattedAmount(): ?string
    {
        if ($this->eventType !== 'billing' || !$this->amount) {
            return null;
        }
        return number_format($this->amount, 2, ',', ' ') . ' €';
    }

    /**
     * Get is overdue using exact model logic
     */
    private function getModelIsOverdue(): bool
    {
        if ($this->eventType === 'step') {
            return $this->status === 'todo' && $this->executionDate && $this->executionDate->startOfDay()->lt(now()->startOfDay());
        } elseif ($this->eventType === 'billing') {
            return $this->status === 'to_send' && $this->sendDate && $this->sendDate->startOfDay()->lt(now()->startOfDay());
        }
        return false;
    }

    /**
     * Get is payment overdue using exact model logic
     */
    private function getModelIsPaymentOverdue(): bool
    {
        return $this->eventType === 'billing' &&
               $this->paymentStatus === 'pending' &&
               $this->paymentDueDate &&
               $this->paymentDueDate->startOfDay()->lt(now()->startOfDay());
    }

    /**
     * Convert to array for event edit form
     */
    public function toEditArray(): array
    {
        return [
            'id' => $this->id,
            'project_id' => $this->projectId,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'event_type' => $this->eventType,
            'status' => $this->status,
            'created_date' => $this->createdDate?->format('Y-m-d'),
            'execution_date' => $this->executionDate?->format('Y-m-d'),
            'send_date' => $this->sendDate?->format('Y-m-d'),
            'payment_due_date' => $this->paymentDueDate?->format('Y-m-d'),
            'completed_at' => $this->completedAt?->format('Y-m-d'),
            'amount' => $this->amount,
            'payment_status' => $this->paymentStatus,
            'paid_at' => $this->paidAt?->format('Y-m-d'),
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'project' => $this->project ? [
                'id' => $this->project->id,
                'name' => $this->project->name,
                'start_date' => $this->project->startDate?->format('Y-m-d'),
                'client' => [
                    'id' => $this->project->client?->id,
                    'name' => $this->project->client?->name,
                ]
            ] : null,
        ];
    }
}
