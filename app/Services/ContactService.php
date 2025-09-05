<?php

namespace App\Services;

use App\Mail\ContactFormMail;
use App\Mail\ContactConfirmationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactService
{
    /**
     * Process contact form submission
     */
    public function processContactForm(array $validatedData, Request $request): array
    {
        try {
            $contactData = $this->prepareContactData($validatedData, $request);
            $recipientEmail = $this->getRecipientEmail($validatedData['subject_type']);

            $this->sendContactEmail($contactData, $recipientEmail);
            $this->sendConfirmationEmail($contactData);
            $this->logSuccess($contactData, $recipientEmail);

            return [
                'success' => true,
                'message' => 'Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.',
            ];

        } catch (\Exception $e) {
            $this->logError($e, $request);
            
            return [
                'success' => false,
                'message' => 'Une erreur est survenue lors de l\'envoi. Veuillez réessayer ou nous contacter directement.',
                'status_code' => 500,
            ];
        }
    }

    /**
     * Prepare contact data from validated input
     */
    private function prepareContactData(array $validatedData, Request $request): array
    {
        return [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'subject_type' => $validatedData['subject_type'],
            'subject' => $validatedData['subject'],
            'message' => $validatedData['message'],
            'sent_at' => now(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ];
    }

    /**
     * Get recipient email based on subject type
     */
    private function getRecipientEmail(string $subjectType): string
    {
        return match ($subjectType) {
            'support', 'bug' => config('contact.emails.support'),
            'billing' => config('contact.emails.billing'),
            'feature' => config('contact.emails.feedback'),
            default => config('contact.emails.contact'),
        };
    }

    /**
     * Send contact email to appropriate recipient
     */
    private function sendContactEmail(array $contactData, string $recipientEmail): void
    {
        Mail::to($recipientEmail)->send(new ContactFormMail($contactData));
    }

    /**
     * Send confirmation email to the sender
     */
    private function sendConfirmationEmail(array $contactData): void
    {
        try {
            Mail::to($contactData['email'])->send(new ContactConfirmationMail($contactData));
        } catch (\Exception $e) {
            // Log but don't fail the main process if confirmation fails
            Log::warning('Failed to send contact confirmation email', [
                'error' => $e->getMessage(),
                'email' => $contactData['email'],
            ]);
        }
    }

    /**
     * Log successful contact form submission
     */
    private function logSuccess(array $contactData, string $recipientEmail): void
    {
        Log::info('Contact form submitted successfully', [
            'email' => $contactData['email'],
            'subject_type' => $contactData['subject_type'],
            'recipient' => $recipientEmail,
            'ip' => $contactData['ip'],
        ]);
    }

    /**
     * Log contact form submission error
     */
    private function logError(\Exception $e, Request $request): void
    {
        Log::error('Contact form submission failed', [
            'error' => $e->getMessage(),
            'email' => $request->input('email'),
            'ip' => $request->ip(),
            'trace' => $e->getTraceAsString(),
        ]);
    }
}