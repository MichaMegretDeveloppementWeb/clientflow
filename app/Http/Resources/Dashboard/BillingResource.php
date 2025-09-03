<?php

namespace App\Http\Resources\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BillingResource extends JsonResource
{
    /**
     * Transform the billing data into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'totals' => [
                'billed' => round($this->resource['total_billed'], 2),
                'paid' => round($this->resource['total_paid'], 2),
                'to_send' => round($this->resource['total_to_send'], 2),
                'sent' => round($this->resource['total_sent'], 2),
                'upcoming_payment' => round($this->resource['total_upcoming_payment'], 2),
                'overdue_payment' => round($this->resource['total_overdue_payment'], 2),
            ],
            'metrics' => [
                'payment_rate' => $this->resource['payment_rate'],
                'invoices_to_send_count' => $this->resource['invoices_to_send_count'],
                'unpaid_invoices_count' => $this->resource['unpaid_invoices_count'],
                'overdue_invoices_count' => $this->resource['overdue_invoices_count'],
            ],
            'cards' => [
                [
                    'id' => 'total_billed',
                    'title' => 'Montant total des factures',
                    'value' => round($this->resource['total_billed'], 2),
                    'icon' => 'calculator',
                    'color' => 'purple',
                    'link' => route('events.index').'?event_type=billing',
                    'description' => null,
                ],
                [
                    'id' => 'to_send',
                    'title' => 'Factures à envoyer',
                    'value' => round($this->resource['total_to_send'], 2),
                    'icon' => 'clock',
                    'color' => 'orange',
                    'link' => route('events.index').'?event_type=billing&status=to_send',
                ],
                [
                    'id' => 'sent_details',
                    'title' => 'Factures envoyées',
                    'value' => round($this->resource['total_sent'], 2),
                    'icon' => 'send',
                    'color' => 'blue',
                    'link' => route('events.index').'?event_type=billing&status=sent',
                    'description' => null,
                    'details' => [
                        'sent' => round($this->resource['total_sent'], 2),
                        'paid' => round($this->resource['total_paid'], 2),
                        'upcoming' => round($this->resource['total_upcoming_payment'], 2),
                        'overdue' => round($this->resource['total_overdue_payment'], 2),
                        'payment_rate' => $this->resource['payment_rate'],
                        'unpaid_count' => $this->resource['unpaid_invoices_count'],
                        'overdue_count' => $this->resource['overdue_invoices_count'],
                    ],
                ],
            ],
            'error' => $this->resource['error'] ?? null,
        ];
    }
}
