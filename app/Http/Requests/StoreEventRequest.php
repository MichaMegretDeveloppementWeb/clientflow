<?php

namespace App\Http\Requests;

use App\Enums\EventType;
use App\Enums\EventStatus;
use App\Enums\PaymentStatus;
use App\Enums\EventCategory;
use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEventRequest extends FormRequest
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
        $rules = [
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => ['nullable', Rule::enum(EventCategory::class)],
            'event_type' => ['required', Rule::enum(EventType::class)],
            'created_date' => 'nullable|date|before_or_equal:today',
        ];

        // Rules specific to step events
        if ($this->event_type === EventType::Step->value) {
            $rules['status'] = ['required', Rule::in([
                EventStatus::Todo->value,
                EventStatus::Done->value,
                EventStatus::Cancelled->value,
            ])];
            
            $rules['execution_date'] = 'required|date|after_or_equal:created_date';
            
            // completed_at requise si statut = "done"
            if ($this->status === EventStatus::Done->value) {
                $rules['completed_at'] = 'required|date|after_or_equal:created_date';
            } else {
                $rules['completed_at'] = 'nullable|date|after_or_equal:created_date';
            }
        }

        // Rules specific to billing events
        if ($this->event_type === EventType::Billing->value) {
            $rules['status'] = ['required', Rule::in([
                EventStatus::ToSend->value,
                EventStatus::Sent->value,
                EventStatus::Cancelled->value,
            ])];
            $rules['amount'] = 'required|numeric|min:0|max:9999999.99';
            $rules['payment_status'] = ['nullable', Rule::enum(PaymentStatus::class)];
            
            $rules['send_date'] = 'required|date|after_or_equal:created_date';
            $rules['payment_due_date'] = 'required|date|after_or_equal:created_date';
            
            // completed_at requise si statut = "sent"
            if ($this->status === EventStatus::Sent->value) {
                $rules['completed_at'] = 'required|date|after_or_equal:created_date';
            } else {
                $rules['completed_at'] = 'nullable|date|after_or_equal:created_date';
            }
            
            if ($this->payment_status === PaymentStatus::Paid->value) {
                $rules['paid_at'] = 'required|date|after_or_equal:created_date';
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
            // Project
            'project_id.required' => 'Le projet est obligatoire.',
            'project_id.exists' => 'Le projet sélectionné n\'existe pas.',
            
            // Name
            'name.required' => 'Le nom de l\'événement est obligatoire.',
            'name.string' => 'Le nom de l\'événement doit être du texte.',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            
            // Description
            'description.string' => 'La description doit être du texte.',
            'description.max' => 'La description ne peut pas dépasser 1000 caractères.',
            
            // Type (EventCategory)
            'type.enum' => 'Le type sélectionné n\'est pas valide.',
            'type' => 'Le type sélectionné n\'est pas valide.',
            
            // Event type (EventType)
            'event_type.required' => 'Le type d\'événement est obligatoire.',
            'event_type.enum' => 'Le type d\'événement n\'est pas valide.',
            'event_type' => 'Le type d\'événement n\'est pas valide.',
            
            // Status
            'status.required' => 'Le statut est obligatoire.',
            'status.in' => 'Le statut sélectionné n\'est pas valide pour ce type d\'événement.',
            
            // Amount (billing events)
            'amount.required' => 'Le montant est obligatoire pour une facturation.',
            'amount.numeric' => 'Le montant doit être un nombre.',
            'amount.min' => 'Le montant ne peut pas être négatif.',
            'amount.max' => 'Le montant ne peut pas dépasser 9 999 999,99 €.',
            
            // Payment status
            'payment_status.enum' => 'Le statut de paiement sélectionné n\'est pas valide.',
            'payment_status' => 'Le statut de paiement sélectionné n\'est pas valide.',
            
            // Dates
            'created_date.date' => 'La date de création doit être une date valide.',
            'created_date.before_or_equal' => 'La date de création ne peut pas être dans le futur.',
            
            'execution_date.required' => 'La date d\'exécution est obligatoire.',
            'execution_date.date' => 'La date d\'exécution doit être une date valide.',
            'execution_date.after_or_equal' => 'La date d\'exécution doit être postérieure ou égale à la date de création.',
            
            'completed_at.required' => 'La date d\'achèvement est obligatoire quand le statut est "Fait" ou "Envoyé".',
            'completed_at.date' => 'La date d\'achèvement doit être une date valide.',
            'completed_at.after_or_equal' => 'La date d\'achèvement doit être postérieure ou égale à la date de création.',
            
            'send_date.required' => 'La date d\'envoi est obligatoire.',
            'send_date.date' => 'La date d\'envoi doit être une date valide.',
            'send_date.after_or_equal' => 'La date d\'envoi doit être postérieure ou égale à la date de création.',
            
            'payment_due_date.required' => 'La date d\'échéance est obligatoire pour une facturation.',
            'payment_due_date.date' => 'La date d\'échéance doit être une date valide.',
            'payment_due_date.after_or_equal' => 'La date d\'échéance doit être postérieure ou égale à la date d\'envoi.',
            
            'paid_at.required' => 'La date de paiement est obligatoire quand le statut est "Payé".',
            'paid_at.date' => 'La date de paiement doit être une date valide.',
            'paid_at.after_or_equal' => 'La date de paiement doit être postérieure ou égale à la date de création.',
        ];
    }

    /**
     * Configure the validator instance
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (!$validator->errors()->any()) {
                $this->validateProjectDates($validator);
                $this->validatePaymentStatus($validator);
            }
        });
    }

    /**
     * Validate dates against project dates
     */
    private function validateProjectDates($validator): void
    {
        $project = Project::find($this->project_id);
        if (!$project) {
            return;
        }

        // Check if dates are within project bounds
        if ($project->start_date) {
            $dates = ['created_date', 'execution_date', 'send_date', 'payment_due_date', 'paid_at'];

            foreach ($dates as $dateField) {
                if ($this->has($dateField) && $this->$dateField) {
                    $date = \Carbon\Carbon::parse($this->$dateField);
                    if ($date->lt($project->start_date)) {
                        $validator->errors()->add($dateField,
                            'La date doit être après la date de début du projet (' .
                            $project->start_date->format('d/m/Y') . ').');
                    }
                }
            }
        }
    }

    /**
     * Validate payment status requirements
     */
    private function validatePaymentStatus($validator): void
    {
        if ($this->event_type === EventType::Billing->value) {
            // If payment_status is 'paid', paid_at is required
            if ($this->payment_status === PaymentStatus::Paid->value && !$this->paid_at) {
                $validator->errors()->add('paid_at',
                    'La date de paiement est requise quand le statut de paiement est "Payé".');
            }
        }
    }

    /**
     * Prepare the data for validation
     */
    protected function prepareForValidation(): void
    {
        // Set default values
        if (!$this->has('created_date')) {
            $this->merge(['created_date' => now()]);
        }

        // Set default status based on event type
        if (!$this->has('status')) {
            if ($this->event_type === EventType::Step->value) {
                $this->merge(['status' => EventStatus::Todo->value]);
            } elseif ($this->event_type === EventType::Billing->value) {
                $this->merge(['status' => EventStatus::ToSend->value]);
            }
        }

        // Set default payment status for billing events
        if ($this->event_type === EventType::Billing->value && !$this->has('payment_status')) {
            $this->merge(['payment_status' => PaymentStatus::Pending->value]);
        }

        // Trim whitespace from strings
        $this->merge([
            'name' => $this->name ? trim($this->name) : null,
            'description' => $this->description ? trim($this->description) : null,
        ]);

        // Convert amount to float if provided
        if ($this->has('amount') && $this->amount !== null && $this->amount !== '') {
            $this->merge([
                'amount' => floatval(str_replace(',', '.', $this->amount)),
            ]);
        }
    }
}
