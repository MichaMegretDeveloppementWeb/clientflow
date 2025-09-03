<?php

declare(strict_types=1);

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuickStatsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'completion_rate' => [
                'value' => round($this->resource['completion_rate'] ?? 0, 1),
                'formatted' => number_format($this->resource['completion_rate'] ?? 0, 1).'%',
            ],
            'revenue_per_client' => [
                'value' => round($this->resource['revenue_per_client'] ?? 0, 2),
                'formatted' => $this->resource['revenue_per_client'] ?? 0,
            ],
            'revenue_growth' => [
                'rate' => round($this->resource['revenue_growth_rate'] ?? 0, 1),
                'formatted' => $this->resource['revenue_growth_rate'] ?? 0,
                'is_positive' => ($this->resource['revenue_growth_rate'] ?? 0) > 0,
                'trend_icon' => ($this->resource['revenue_growth_rate'] ?? 0) > 0 ? 'trending-up' : 'trending-down',
                'trend_color' => ($this->resource['revenue_growth_rate'] ?? 0) > 0 ? 'emerald' : 'red',
            ],
            'metrics' => [
                'active_projects' => [
                    'value' => $this->resource['active_projects'] ?? 0,
                    'label' => 'Projets actifs',
                ],
                'pending_invoices' => [
                    'value' => $this->resource['pending_invoices'] ?? 0,
                    'label' => 'Factures en attente',
                ],
                'urgent_tasks' => [
                    'value' => $this->resource['urgent_tasks'] ?? 0,
                    'label' => 'Tâches urgentes',
                ],
            ],
            'error' => $this->resource['error'] ?? null,
        ];
    }
}
