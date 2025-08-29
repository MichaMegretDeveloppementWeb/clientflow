<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
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
        $clientId = $this->route('client')->id ?? $this->route('client');
        
        return [
            'name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'email' => 'required|email|max:255|unique:clients,email,' . $clientId,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom error messages
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom du client est obligatoire.',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'L\'adresse email doit être valide.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'email.max' => 'L\'adresse email ne peut pas dépasser 255 caractères.',
            'company.max' => 'Le nom de l\'entreprise ne peut pas dépasser 255 caractères.',
            'phone.max' => 'Le numéro de téléphone ne peut pas dépasser 20 caractères.',
            'address.max' => 'L\'adresse ne peut pas dépasser 500 caractères.',
            'notes.max' => 'Les notes ne peuvent pas dépasser 1000 caractères.',
        ];
    }

    /**
     * Prepare the data for validation
     */
    protected function prepareForValidation(): void
    {
        // Clean phone number
        if ($this->has('phone') && $this->phone) {
            $this->merge([
                'phone' => preg_replace('/[^0-9+]/', '', $this->phone),
            ]);
        }

        // Trim whitespace from strings
        $this->merge([
            'name' => $this->name ? trim($this->name) : null,
            'company' => $this->company ? trim($this->company) : null,
            'email' => $this->email ? strtolower(trim($this->email)) : null,
            'address' => $this->address ? trim($this->address) : null,
            'notes' => $this->notes ? trim($this->notes) : null,
        ]);
    }
}
