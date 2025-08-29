<?php

namespace App\Http\Resources\Dashboard;

use App\Enums\EventStatus;
use App\Enums\EventType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'event_type' => $this->event_type,
            'event_type_label' => EventType::from($this->event_type)->label(),
            'status' => $this->status,
            'status_label' => EventStatus::from($this->status)->label(),
            'status_color' => EventStatus::from($this->status)->color(),
            'project' => [
                'id' => $this->project->id,
                'name' => $this->project->name,
                'client' => [
                    'id' => $this->project->client->id,
                    'name' => $this->project->client->name,
                ],
            ],
            'is_overdue' => $this->is_overdue,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        // Add type-specific fields
        if ($this->event_type === EventType::Step->value) {
            $data['execution_date'] = $this->execution_date;
            $data['days_overdue'] = $this->execution_date ?
                abs(now()->diffInDays($this->execution_date, false)) : null;
        } else {
            $data['send_date'] = $this->send_date;
            $data['payment_due_date'] = $this->payment_due_date;
            $data['amount'] = $this->amount;
            $data['formatted_amount'] = $this->formatted_amount;
            $data['payment_status'] = $this->payment_status;
            $data['is_payment_overdue'] = $this->is_payment_overdue;
        }

        return $data;
    }
}
