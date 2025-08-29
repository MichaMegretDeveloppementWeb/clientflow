<?php

namespace App\DTOs;

use App\Models\Project;
use App\Enums\ProjectStatus;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ProjectDTO
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly ?string $description,
        public readonly int $clientId,
        public readonly string $status,
        public readonly ?float $budget,
        public readonly ?Carbon $startDate,
        public readonly ?Carbon $endDate,
        public readonly ?ClientDTO $client = null,
        public readonly ?int $eventsCount = 0,
        public readonly ?float $totalBilled = 0,
        public readonly ?float $totalPaid = 0,
        public readonly ?float $totalUnpaid = 0,
        public readonly ?float $budgetProgress = 0,
        public readonly ?bool $budgetExceeded = false,
        public readonly ?bool $isOverdue = false,
        public readonly ?bool $hasOverdueEvents = false,
        public readonly ?bool $hasPaymentOverdue = false,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
    ) {}

    /**
     * Create DTO from model
     */
    public static function fromModel(Project $project): self
    {
        return new self(
            id: $project->id,
            name: $project->name,
            description: $project->description,
            clientId: $project->client_id,
            status: $project->status,
            budget: $project->budget,
            startDate: $project->start_date,
            endDate: $project->end_date,
            client: $project->relationLoaded('client') && $project->client 
                ? ClientDTO::fromModel($project->client) 
                : null,
            eventsCount: $project->events_count ?? $project->events()->count(),
            totalBilled: $project->total_billed ?? 0,
            totalPaid: $project->total_paid ?? 0,
            totalUnpaid: $project->total_unpaid ?? 0,
            budgetProgress: $project->budget_progress ?? 0,
            budgetExceeded: $project->budget_exceeded ?? false,
            isOverdue: $project->status === ProjectStatus::Active->value && 
                       $project->end_date && 
                       $project->end_date->isPast(),
            hasOverdueEvents: $project->has_overdue_events ?? false,
            hasPaymentOverdue: $project->has_payment_overdue ?? false,
            createdAt: $project->created_at?->toISOString(),
            updatedAt: $project->updated_at?->toISOString(),
        );
    }

    /**
     * Create DTO from request data
     */
    public static function fromRequest(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'],
            description: $data['description'] ?? null,
            clientId: $data['client_id'],
            status: $data['status'] ?? ProjectStatus::Active->value,
            budget: isset($data['budget']) ? floatval($data['budget']) : null,
            startDate: isset($data['start_date']) ? Carbon::parse($data['start_date']) : null,
            endDate: isset($data['end_date']) ? Carbon::parse($data['end_date']) : null,
        );
    }

    /**
     * Create DTO from array data (for dashboard services)
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'] ?? '',
            description: $data['description'] ?? null,
            clientId: $data['client']['id'] ?? 0,
            status: $data['status'] ?? ProjectStatus::Active->value,
            budget: isset($data['budget']) ? floatval($data['budget']) : null,
            startDate: isset($data['start_date']) ? Carbon::parse($data['start_date']) : null,
            endDate: isset($data['end_date']) ? Carbon::parse($data['end_date']) : null,
            client: isset($data['client']) ? ClientDTO::fromArray($data['client']) : null,
            eventsCount: $data['events_count'] ?? 0,
            totalBilled: $data['total_billed'] ?? 0,
            totalPaid: $data['total_paid'] ?? 0,
            totalUnpaid: $data['total_unpaid'] ?? 0,
            budgetProgress: $data['budget_progress'] ?? 0,
            budgetExceeded: $data['budget_exceeded'] ?? false,
            isOverdue: $data['is_overdue'] ?? false,
            hasOverdueEvents: $data['has_overdue_events'] ?? false,
            hasPaymentOverdue: $data['has_payment_overdue'] ?? false,
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
        );
    }

    /**
     * Convert to array for API response
     */
    public function toArray(): array
    {
        $statusEnum = ProjectStatus::from($this->status);
        
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'client_id' => $this->clientId,
            'status' => $this->status,
            'status_label' => $statusEnum->label(),
            'status_color' => $statusEnum->color(),
            'budget' => $this->budget,
            'formatted_budget' => $this->formatCurrency($this->budget),
            'start_date' => $this->startDate?->format('Y-m-d'),
            'end_date' => $this->endDate?->format('Y-m-d'),
            'formatted_start_date' => $this->startDate?->format('d/m/Y'),
            'formatted_end_date' => $this->endDate?->format('d/m/Y'),
            'client' => $this->client?->toArray(),
            'events_count' => $this->eventsCount,
            'total_billed' => $this->totalBilled,
            'formatted_billed' => $this->formatCurrency($this->totalBilled),
            'total_paid' => $this->totalPaid,
            'formatted_paid' => $this->formatCurrency($this->totalPaid),
            'total_unpaid' => $this->totalUnpaid,
            'formatted_unpaid' => $this->formatCurrency($this->totalUnpaid),
            'budget_progress' => $this->budgetProgress,
            'budget_exceeded' => $this->budgetExceeded,
            'is_overdue' => $this->isOverdue,
            'has_overdue_events' => $this->hasOverdueEvents,
            'has_payment_overdue' => $this->hasPaymentOverdue,
            'duration_days' => $this->calculateDuration(),
            'days_remaining' => $this->calculateDaysRemaining(),
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }

    /**
     * Convert to array for project detail view
     */
    public function toDetailArray(): array
    {
        $statusEnum = ProjectStatus::from($this->status);
        
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
            'status_label' => $statusEnum->label(),
            'budget' => $this->budget,
            'budget_formatted' => $this->formatCurrency($this->budget),
            'start_date' => $this->startDate?->toISOString(),
            'end_date' => $this->endDate?->toISOString(),
            'is_overdue' => $this->isOverdue,
            'total_billed' => $this->totalBilled,
            'total_paid' => $this->totalPaid,
            'total_unpaid' => $this->totalUnpaid,
            'budget_progress' => $this->budgetProgress,
            'budget_exceeded' => $this->budgetExceeded,
            'client' => $this->client ? [
                'id' => $this->client->id,
                'name' => $this->client->name,
            ] : null,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }

    /**
     * Convert to array for database
     */
    public function toDatabaseArray(): array
    {
        return array_filter([
            'name' => $this->name,
            'description' => $this->description,
            'client_id' => $this->clientId,
            'status' => $this->status,
            'budget' => $this->budget,
            'start_date' => $this->startDate?->format('Y-m-d'),
            'end_date' => $this->endDate?->format('Y-m-d'),
        ], fn($value) => $value !== null);
    }

    /**
     * Convert to array for editing form
     */
    public function toEditArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
            'budget' => $this->budget,
            'start_date' => $this->startDate?->toISOString(),
            'end_date' => $this->endDate?->toISOString(),
            'client_id' => $this->clientId,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'client' => $this->client ? [
                'id' => $this->client->id,
                'name' => $this->client->name,
                'company' => $this->client->company,
            ] : null,
        ];
    }

    /**
     * Create collection of DTOs from models
     */
    public static function collection(Collection $projects): Collection
    {
        return $projects->map(fn(Project $project) => self::fromModel($project));
    }

    /**
     * Calculate project duration in days
     */
    private function calculateDuration(): ?int
    {
        if (!$this->startDate || !$this->endDate) {
            return null;
        }

        return $this->startDate->diffInDays($this->endDate);
    }

    /**
     * Calculate days remaining
     */
    private function calculateDaysRemaining(): ?int
    {
        if (!$this->endDate) {
            return null;
        }

        if ($this->endDate->isPast()) {
            return 0;
        }

        return now()->diffInDays($this->endDate);
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
     * Check if project can be completed
     */
    public function canBeCompleted(): bool
    {
        return $this->status === ProjectStatus::Active->value;
    }

    /**
     * Check if project can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, [
            ProjectStatus::Active->value,
            ProjectStatus::OnHold->value,
        ]);
    }

    /**
     * Check if project can be reactivated
     */
    public function canBeReactivated(): bool
    {
        return in_array($this->status, [
            ProjectStatus::Completed->value,
            ProjectStatus::OnHold->value,
            ProjectStatus::Cancelled->value,
        ]);
    }
}