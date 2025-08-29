<?php

namespace App\Http\Requests;

use App\Enums\ProjectStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProjectRequest extends FormRequest
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
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'client_id' => 'required|exists:clients,id',
            'status' => ['required', Rule::enum(ProjectStatus::class)],
            'budget' => 'nullable|numeric|min:0|max:9999999.99',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ];
    }

    /**
     * Get custom error messages
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom du projet est obligatoire.',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'description.max' => 'La description ne peut pas dépasser 1000 caractères.',
            'client_id.required' => 'Le client est obligatoire.',
            'client_id.exists' => 'Le client sélectionné n\'existe pas.',
            'status.required' => 'Le statut est obligatoire.',
            'status.enum' => 'Le statut sélectionné n\'est pas valide.',
            'budget.numeric' => 'Le budget doit être un nombre.',
            'budget.min' => 'Le budget ne peut pas être négatif.',
            'budget.max' => 'Le budget ne peut pas dépasser 9 999 999,99 €.',
            'start_date.required' => 'La date de début est obligatoire.',
            'start_date.date' => 'La date de début doit être une date valide.',
            'end_date.date' => 'La date de fin doit être une date valide.',
            'end_date.after_or_equal' => 'La date de fin doit être après ou égale à la date de début.',
        ];
    }

    /**
     * Prepare the data for validation
     */
    protected function prepareForValidation(): void
    {
        // Set default status if not provided
        if (!$this->has('status')) {
            $this->merge([
                'status' => ProjectStatus::Active->value,
            ]);
        }

        // Trim whitespace from strings
        $this->merge([
            'name' => $this->name ? trim($this->name) : null,
            'description' => $this->description ? trim($this->description) : null,
        ]);

        // Convert budget to float if provided
        if ($this->has('budget') && $this->budget !== null && $this->budget !== '') {
            $this->merge([
                'budget' => floatval(str_replace(',', '.', $this->budget)),
            ]);
        }
    }
}
