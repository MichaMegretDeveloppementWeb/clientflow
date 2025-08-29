<?php

namespace App\Enums;

enum EventType: string
{
    case Step = 'step';
    case Billing = 'billing';

    /**
     * Get the label for display in French
     */
    public function label(): string
    {
        return match($this) {
            self::Step => 'Étape',
            self::Billing => 'Facturation',
        };
    }

    /**
     * Get the icon name for UI display
     */
    public function icon(): string
    {
        return match($this) {
            self::Step => 'check-circle',
            self::Billing => 'euro',
        };
    }

    /**
     * Check if this is a billing event
     */
    public function isBilling(): bool
    {
        return $this === self::Billing;
    }

    /**
     * Check if this is a step event
     */
    public function isStep(): bool
    {
        return $this === self::Step;
    }

    /**
     * Get allowed statuses for this event type
     */
    public function allowedStatuses(): array
    {
        return match($this) {
            self::Step => [EventStatus::Todo, EventStatus::Done, EventStatus::Cancelled],
            self::Billing => [EventStatus::ToSend, EventStatus::Sent, EventStatus::Cancelled],
        };
    }

    /**
     * Get the default status for this event type
     */
    public function defaultStatus(): EventStatus
    {
        return match($this) {
            self::Step => EventStatus::Todo,
            self::Billing => EventStatus::ToSend,
        };
    }
}