<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatisticsResource extends JsonResource
{
    /**
     * Transform the statistics data into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'clients' => [
                'total' => $this->resource['total_clients'],
                'growth_rate' => $this->resource['client_growth_rate'],
                'this_month' => $this->resource['clients_this_month'],
            ],
            'projects' => [
                'active' => $this->resource['active_projects'],
                'completed' => $this->resource['completed_projects'],
                'on_hold' => $this->resource['on_hold_projects'],
                'cancelled' => $this->resource['cancelled_projects'],
                'completed_this_week' => $this->resource['projects_completed_this_week'],
                'completed_this_month' => $this->resource['projects_completed_this_month'],
                'on_hold_rate' => $this->resource['on_hold_rate'],
            ],
            'tasks' => [
                'pending' => $this->resource['pending_tasks'],
                'completion_rate' => $this->resource['completion_rate'],
            ],
            'revenue' => [
                'monthly' => $this->resource['monthly_revenue'],
                'total_billed' => $this->resource['total_billed'],
                'total_paid' => $this->resource['total_paid'],
                'total_pending' => $this->resource['total_pending'],
                'upcoming_payment' => $this->resource['total_upcoming_payment'],
                'overdue_payment' => $this->resource['total_overdue_payment'],
                'overdue_amount' => $this->resource['overdue_payments_amount'],
                'growth' => $this->resource['revenue_growth'],
            ],
            'invoices' => [
                'unpaid' => $this->resource['unpaid_invoices'],
            ],
        ];
    }
}