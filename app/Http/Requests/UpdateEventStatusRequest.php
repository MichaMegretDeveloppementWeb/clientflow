<?php

namespace App\Http\Requests;

use App\Enums\EventType;
use App\Enums\EventStatus;
use App\Enums\PaymentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEventStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $event = $this->route('event');
        
        $rules = [];
        
        // Rules for status update
        if ($this->has('status')) {
            if ($event->event_type === EventType::Step->value) {
                $rules['status'] = ['required', Rule::in([
                    EventStatus::Todo->value,
                    EventStatus::Done->value,
                    EventStatus::Cancelled->value,
                ])];
            } else {
                $rules['status'] = ['required', Rule::in([
                    EventStatus::ToSend->value,
                    EventStatus::Sent->value,
                    EventStatus::Cancelled->value,
                ])];
            }
        }
        
        // Rules for payment status update (billing events only)
        if ($this->has('payment_status')) {
            if ($event->event_type === EventType::Billing->value) {
                $rules['payment_status'] = ['required', Rule::enum(PaymentStatus::class)];
                
                if ($this->payment_status === PaymentStatus::Paid->value) {
                    $rules['paid_at'] = 'nullable|date';
                }
            }
        }
        
        return $rules;
    }

    /**
     * Get custom error messages
     */
    public function messages(): array
    {
        return [
            'status.required' => 'Le statut est obligatoire.',
            'status.in' => 'Le statut sélectionné n\'est pas valide pour ce type d\'événement.',
            'payment_status.required' => 'Le statut de paiement est obligatoire.',
            'payment_status.enum' => 'Le statut de paiement n\'est pas valide.',
            'paid_at.date' => 'La date de paiement doit être une date valide.',
        ];
    }

    /**
     * Configure the validator instance
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (!$validator->errors()->any()) {
                $this->validateStatusTransition($validator);
                $this->validatePaymentStatusRequirements($validator);
            }
        });
    }

    /**
     * Validate status transition is allowed
     */
    private function validateStatusTransition($validator): void
    {
        if (!$this->has('status')) {
            return;
        }
        
        $event = $this->route('event');
        $oldStatus = $event->status;
        $newStatus = $this->status;
        
        if ($oldStatus === $newStatus) {
            return; // No change
        }
        
        // Define valid transitions
        $validTransitions = [];
        
        if ($event->event_type === EventType::Step->value) {
            $validTransitions = [
                EventStatus::Todo->value => [EventStatus::Done->value, EventStatus::Cancelled->value],
                EventStatus::Done->value => [EventStatus::Todo->value, EventStatus::Cancelled->value],
                EventStatus::Cancelled->value => [EventStatus::Todo->value],
            ];
        } else {
            $validTransitions = [
                EventStatus::ToSend->value => [EventStatus::Sent->value, EventStatus::Cancelled->value],
                EventStatus::Sent->value => [EventStatus::ToSend->value, EventStatus::Cancelled->value],
                EventStatus::Cancelled->value => [EventStatus::ToSend->value],
            ];
        }
        
        if (!isset($validTransitions[$oldStatus]) || !in_array($newStatus, $validTransitions[$oldStatus])) {
            $validator->errors()->add('status', 
                "La transition de statut de '{$oldStatus}' vers '{$newStatus}' n'est pas autorisée.");
        }
    }

    /**
     * Validate payment status requirements
     */
    private function validatePaymentStatusRequirements($validator): void
    {
        if (!$this->has('payment_status')) {
            return;
        }
        
        $event = $this->route('event');
        
        // Only billing events can have payment status
        if ($event->event_type !== EventType::Billing->value) {
            $validator->errors()->add('payment_status', 
                'Le statut de paiement ne peut être modifié que pour les événements de facturation.');
            return;
        }
        
        // If setting to paid, ensure paid_at is provided
        if ($this->payment_status === PaymentStatus::Paid->value) {
            if (!$this->paid_at && !$event->paid_at) {
                $validator->errors()->add('paid_at', 
                    'La date de paiement est requise pour marquer comme payé.');
            }
        }
    }

    /**
     * Prepare the data for validation
     */
    protected function prepareForValidation(): void
    {
        // If setting payment status to paid and no paid_at provided, use today
        if ($this->payment_status === PaymentStatus::Paid->value && !$this->has('paid_at')) {
            $this->merge(['paid_at' => now()]);
        }
    }
}
