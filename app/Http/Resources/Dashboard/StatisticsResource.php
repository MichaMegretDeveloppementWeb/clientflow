<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatisticsResource extends JsonResource
{
    /**
     * Transform the statistics data into an array.
     * OPTIMIZED: Only format data actually used by StatsGrid component
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'clients' => [
                'total' => $this->resource['clients']['total'],
                'this_month' => $this->resource['clients']['this_month'],
            ],
            'projects' => [
                'active' => $this->resource['projects']['active'],
                'completed' => $this->resource['projects']['completed'],
                'on_hold' => $this->resource['projects']['on_hold'],
            ],
        ];
    }
}
