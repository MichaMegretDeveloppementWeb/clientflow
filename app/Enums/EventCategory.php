<?php

namespace App\Enums;

enum EventCategory: string
{
    // Catégories universelles - applicables aux étapes ET facturations
    case Meeting = 'meeting';
    case Consultation = 'consultation';
    case Planning = 'planning';
    case Execution = 'execution';
    case Review = 'review';
    case Delivery = 'delivery';
    case FollowUp = 'follow_up';
    case Training = 'training';
    case Maintenance = 'maintenance';
    case Research = 'research';
    case Other = 'other';

    /**
     * Get the label for display in French
     */
    public function label(): string
    {
        return match($this) {
            // Catégories universelles
            self::Meeting => 'Réunion',
            self::Consultation => 'Consultation',
            self::Planning => 'Planification',
            self::Execution => 'Exécution',
            self::Review => 'Révision',
            self::Delivery => 'Livraison',
            self::FollowUp => 'Suivi',
            self::Training => 'Formation',
            self::Maintenance => 'Maintenance',
            self::Research => 'Recherche',
            self::Other => 'Autre',
        };
    }

    /**
     * Get the icon name for UI display
     */
    public function icon(): string
    {
        return match($this) {
            // Catégories universelles
            self::Meeting => 'users',
            self::Consultation => 'message-circle',
            self::Planning => 'calendar',
            self::Execution => 'play',
            self::Review => 'eye',
            self::Delivery => 'package',
            self::FollowUp => 'repeat',
            self::Training => 'graduation-cap',
            self::Maintenance => 'wrench',
            self::Research => 'search',
            self::Other => 'circle',
        };
    }

    /**
     * Get categories for a specific event type
     */
    public static function forEventType(EventType $eventType): array
    {
        // Toutes les catégories sont disponibles pour les deux types d'événements
        return [
            self::Meeting,
            self::Consultation,
            self::Planning,
            self::Execution,
            self::Review,
            self::Delivery,
            self::FollowUp,
            self::Training,
            self::Maintenance,
            self::Research,
            self::Other,
        ];
    }

    /**
     * Get all available categories
     */
    public static function all(): array
    {
        return self::cases();
    }
}