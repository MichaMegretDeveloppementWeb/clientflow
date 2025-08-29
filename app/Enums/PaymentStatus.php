<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';

    /**
     * Get the label for display in French
     */
    public function label(): string
    {
        return match($this) {
            self::Pending => 'A payer',
            self::Paid => 'Payé',
        };
    }

    /**
     * Get the color for UI display
     */
    public function color(): string
    {
        return match($this) {
            self::Pending => 'orange',
            self::Paid => 'green',
        };
    }

    /**
     * Get the icon name for UI display
     */
    public function icon(): string
    {
        return match($this) {
            self::Pending => 'clock',
            self::Paid => 'check-circle',
        };
    }

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return $this === self::Pending;
    }

    /**
     * Check if payment is completed
     */
    public function isPaid(): bool
    {
        return $this === self::Paid;
    }
}
