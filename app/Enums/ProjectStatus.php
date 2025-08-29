<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case Active = 'active';
    case Completed = 'completed';
    case OnHold = 'on_hold';
    case Cancelled = 'cancelled';

    /**
     * Get the label for display in French
     */
    public function label(): string
    {
        return match($this) {
            self::Active => 'Actif',
            self::Completed => 'Terminé',
            self::OnHold => 'En pause',
            self::Cancelled => 'Annulé',
        };
    }

    /**
     * Get the color for UI display
     */
    public function color(): string
    {
        return match($this) {
            self::Active => 'blue',
            self::Completed => 'green',
            self::OnHold => 'yellow',
            self::Cancelled => 'red',
        };
    }

    /**
     * Get the badge variant for UI components
     */
    public function badgeVariant(): string
    {
        return match($this) {
            self::Active => 'default',
            self::Completed => 'success',
            self::OnHold => 'warning',
            self::Cancelled => 'destructive',
        };
    }

    /**
     * Check if the project is considered active
     */
    public function isActive(): bool
    {
        return $this === self::Active;
    }

    /**
     * Check if the project is considered finished
     */
    public function isFinished(): bool
    {
        return in_array($this, [self::Completed, self::Cancelled]);
    }
}