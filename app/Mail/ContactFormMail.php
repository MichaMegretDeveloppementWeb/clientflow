<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $contactData;

    /**
     * Create a new message instance.
     */
    public function __construct(array $contactData)
    {
        $this->contactData = $contactData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subjectType = $this->getSubjectTypeLabel($this->contactData['subject_type']);
        
        return new Envelope(
            from: new Address($this->contactData['email'], $this->contactData['name']),
            replyTo: [
                new Address($this->contactData['email'], $this->contactData['name']),
            ],
            subject: "[Stone Contact] {$subjectType} - {$this->contactData['subject']}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contact-form',
            with: [
                'contactData' => $this->contactData,
                'subjectTypeLabel' => $this->getSubjectTypeLabel($this->contactData['subject_type']),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Get human-readable subject type label
     */
    private function getSubjectTypeLabel(string $subjectType): string
    {
        return match ($subjectType) {
            'general' => 'Question générale',
            'support' => 'Support technique',
            'billing' => 'Facturation',
            'feature' => 'Demande de fonctionnalité',
            'bug' => 'Signalement de bug',
            'other' => 'Autre',
            default => 'Contact',
        };
    }
}
