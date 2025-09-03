<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RevenueResource extends JsonResource
{
    /**
     * Transform the revenue chart data into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Le service retourne déjà la structure correcte, on ne fait que passer les données
        // en ajoutant les options de chart côté resource
        return [
            'labels' => $this->resource['labels'] ?? [],
            'datasets' => $this->resource['datasets'] ?? [],
            'granularity' => $this->resource['granularity'] ?? 'month',
            'period' => $this->resource['period'] ?? '6months',
            'start_date' => $this->resource['start_date'] ?? null,
            'end_date' => $this->resource['end_date'] ?? null,
            'error' => $this->resource['error'] ?? null,
            'chart_options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'interaction' => [
                    'intersect' => false,
                    'mode' => 'index',
                ],
                'plugins' => [
                    'legend' => [
                        'position' => 'top',
                    ],
                    'tooltip' => [
                        'callbacks' => [
                            'label' => "function(context) { return context.dataset.label + ': ' + new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(context.parsed.y); }",
                        ],
                    ],
                ],
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                        'ticks' => [
                            'callback' => "function(value) { return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(value); }",
                        ],
                    ],
                ],
            ],
        ];
    }
}
