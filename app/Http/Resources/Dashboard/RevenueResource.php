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
        return [
            'labels' => $this->resource['labels'],
            'datasets' => [
                [
                    'label' => 'Revenus réels',
                    'data' => $this->resource['datasets'][0]['data'],
                    'borderColor' => 'rgb(34, 197, 94)',
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'tension' => 0.3,
                    'fill' => true,
                ],
                [
                    'label' => 'Revenus Facturé',
                    'data' => $this->resource['datasets'][1]['data'],
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'tension' => 0.3,
                    'fill' => true,
                ],
            ],
            'granularity' => $this->resource['granularity'] ?? 'month',
            'period' => $this->resource['period'] ?? '6months',
            'start_date' => $this->resource['start_date'] ?? null,
            'end_date' => $this->resource['end_date'] ?? null,
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
                            'label' => "function(context) { return context.dataset.label + ': ' + new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(context.parsed.y); }"
                        ]
                    ]
                ],
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                        'ticks' => [
                            'callback' => "function(value) { return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(value); }"
                        ]
                    ]
                ]
            ]
        ];
    }
}
