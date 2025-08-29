<?php

namespace App\DTOs;

use App\Models\Client;
use Illuminate\Support\Collection;

class ClientDTO
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly ?string $company,
        public readonly ?string $email,
        public readonly ?string $phone,
        public readonly ?string $address,
        public readonly ?string $notes,
        public readonly ?int $projectsCount = 0,
        public readonly ?int $activeProjectsCount = 0,
        public readonly ?float $totalRevenue = 0,
        public readonly ?float $pendingAmount = 0,
        public readonly ?bool $hasOverduePayments = false,
        public readonly ?string $createdAt = null,
        public readonly ?string $updatedAt = null,
    ) {}

    /**
     * Create DTO from model
     */
    public static function fromModel(Client $client): self
    {
        return new self(
            id: $client->id,
            name: $client->name,
            company: $client->company,
            email: $client->email,
            phone: $client->phone,
            address: $client->address,
            notes: $client->notes,
            projectsCount: $client->projects_count ?? 0,
            activeProjectsCount: $client->active_projects_count ?? 0,
            totalRevenue: $client->total_revenue ?? 0,
            pendingAmount: $client->pending_amount ?? 0,
            hasOverduePayments: $client->has_overdue_payments ?? false,
            createdAt: $client->created_at?->toISOString(),
            updatedAt: $client->updated_at?->toISOString(),
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
            company: $data['company'] ?? null,
            email: $data['email'],
            phone: $data['phone'] ?? null,
            address: $data['address'] ?? null,
            notes: $data['notes'] ?? null,
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
            company: $data['company'] ?? null,
            email: $data['email'] ?? '',
            phone: $data['phone'] ?? null,
            address: $data['address'] ?? null,
            notes: $data['notes'] ?? null,
            projectsCount: $data['projects_count'] ?? 0,
            activeProjectsCount: $data['active_projects_count'] ?? 0,
            totalRevenue: $data['total_revenue'] ?? 0,
            pendingAmount: $data['pending_amount'] ?? 0,
            hasOverduePayments: $data['has_overdue_payments'] ?? false,
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
        );
    }

    /**
     * Convert to array for API response
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'company' => $this->company,
            'email' => $this->email,
            'phone' => $this->phone,
            'formatted_phone' => $this->formatPhone(),
            'address' => $this->address,
            'notes' => $this->notes,
            'projects_count' => $this->projectsCount,
            'active_projects_count' => $this->activeProjectsCount,
            'total_revenue' => $this->totalRevenue,
            'formatted_revenue' => $this->formatCurrency($this->totalRevenue),
            'pending_amount' => $this->pendingAmount,
            'formatted_pending' => $this->formatCurrency($this->pendingAmount),
            'has_overdue_payments' => $this->hasOverduePayments,
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
            'company' => $this->company,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'notes' => $this->notes,
        ], fn($value) => $value !== null);
    }

    /**
     * Convert to array for editing forms
     */
    public function toEditArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'company' => $this->company,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'notes' => $this->notes,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }

    /**
     * Create collection of DTOs from models
     */
    public static function collection(Collection $clients): Collection
    {
        return $clients->map(fn(Client $client) => self::fromModel($client));
    }

    /**
     * Format phone number for display
     */
    private function formatPhone(): ?string
    {
        if (!$this->phone || strlen($this->phone) < 10) {
            return $this->phone;
        }

        if (strlen($this->phone) === 10) {
            return sprintf(
                '%s %s %s %s %s',
                substr($this->phone, 0, 2),
                substr($this->phone, 2, 2),
                substr($this->phone, 4, 2),
                substr($this->phone, 6, 2),
                substr($this->phone, 8, 2)
            );
        }

        return $this->phone;
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
}