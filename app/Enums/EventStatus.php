<?php

namespace App\Enums;

enum EventStatus: string
{
    case Todo = 'todo';
    case Done = 'done';
    case ToSend = 'to_send';
    case Sent = 'sent';
    case Cancelled = 'cancelled';

    /**
     * Get the label for display in French
     */
    public function label(): string
    {
        return match($this) {
            self::Todo => 'À faire',
            self::Done => 'Fait',
            self::ToSend => 'À envoyer',
            self::Sent => 'Envoyé',
            self::Cancelled => 'Annulé',
        };
    }

    /**
     * Get the color for UI display
     */
    public function color(): string
    {
        return match($this) {
            self::Todo => 'yellow',
            self::Done => 'green',
            self::ToSend => 'orange',
            self::Sent => 'blue',
            self::Cancelled => 'red',
        };
    }

    /**
     * Check if the status is pending (not completed)
     */
    public function isPending(): bool
    {
        return in_array($this, [self::Todo, self::ToSend]);
    }

    /**
     * Check if the status is completed
     */
    public function isCompleted(): bool
    {
        return in_array($this, [self::Done, self::Sent]);
    }

    /**
     * Check if the status is cancelled
     */
    public function isCancelled(): bool
    {
        return $this === self::Cancelled;
    }

    /**
     * Get the appropriate status for a given event type
     */
    public static function forEventType(EventType $eventType, bool $pending = true): self
    {
        if ($pending) {
            return match($eventType) {
                EventType::Step => self::Todo,
                EventType::Billing => self::ToSend,
            };
        }

        return match($eventType) {
            EventType::Step => self::Done,
            EventType::Billing => self::Sent,
        };
    }
}