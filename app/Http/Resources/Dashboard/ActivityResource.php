<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource['id'],
            'type' => $this->resource['type'], // client, project, step, billing
            'entity_type' => $this->resource['entity_type'], // Client, Projet, Étape, Facturation
            'name' => $this->resource['name'],
            'company' => $this->resource['company'],
            'status' => $this->resource['status'], // created, done, sent
            'status_label' => $this->getStatusLabel(),
            'timestamp' => $this->resource['timestamp'],
            'time_ago' => $this->getTimeAgo(),
            'link' => $this->resource['link'],
            'parent_project' => $this->resource['parent_project'],
            'parent_client' => $this->resource['parent_client'],
            'amount' => $this->resource['amount'],
            'formatted_amount' => $this->resource['amount'] ? number_format($this->resource['amount'], 2, ',', ' ') . ' €' : null,
            'icon' => $this->getIcon(),
            'icon_color' => $this->getIconColor(),
        ];
    }

    /**
     * Get the status label in French
     */
    private function getStatusLabel(): string
    {
        return match($this->resource['status']) {
            'created' => 'Créé',
            'done' => 'Fait',
            'sent' => 'Envoyé',
            'paid' => 'Payé',
            default => ucfirst($this->resource['status']),
        };
    }

    /**
     * Get the appropriate icon based on type
     */
    private function getIcon(): string
    {
        return match($this->resource['type']) {
            'client' => 'user',
            'project' => 'folder',
            'step' => 'flag',
            'billing' => 'banknote',
            default => 'circle',
        };
    }

    /**
     * Get the appropriate icon color based on type (not status)
     */
    private function getIconColor(): string
    {
        return match($this->resource['type']) {
            'client' => 'text-blue-600',
            'project' => 'text-purple-600',
            'step' => 'text-green-600',
            'billing' => 'text-emerald-600',
            default => 'text-gray-600',
        };
    }

    /**
     * Get human readable time ago
     */
    private function getTimeAgo(): string
    {
        // Convertir le timestamp en objet Carbon si ce n'est pas déjà fait
        $timestamp = $this->resource['timestamp'];
        if (is_string($timestamp)) {
            $timestamp = \Carbon\Carbon::parse($timestamp);
        }

        $now = now();
        $diffInDays = round($now->diffInDays($timestamp, true));

        // Moins de 5 jours = temps relatif
        if ($diffInDays < 1) {
            return 'Aujourd\'hui';
        }
        elseif ($diffInDays < 5){
            return 'Il y a ' . $diffInDays . ' jour' . ($diffInDays > 1 ? 's' : '');
        }

        // Plus de 5 jours = date formatée
        return $timestamp->format('d/m/Y');
    }
}
