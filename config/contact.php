<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Contact Information
    |--------------------------------------------------------------------------
    |
    | Configuration for all contact-related information used throughout
    | the Stone application. Values are pulled from environment variables
    | to allow easy customization per environment.
    |
    */

    // Email addresses
    'emails' => [
        'contact' => env('CONTACT_EMAIL', 'contact@stone-app.fr'),
        'support' => env('SUPPORT_EMAIL', 'support@stone-app.fr'),
        'billing' => env('BILLING_EMAIL', 'facturation@stone-app.fr'),
        'security' => env('SECURITY_EMAIL', 'security@stone-app.fr'),
        'feedback' => env('FEEDBACK_EMAIL', 'feedback@stone-app.fr'),
        'legal' => env('LEGAL_EMAIL', 'legal@stone-app.fr'),
    ],

    // Phone numbers
    'phone' => [
        'main' => env('CONTACT_PHONE', '+33 6 87 93 46 47'),
        'support' => env('SUPPORT_PHONE', '+33 6 87 93 46 47'),
    ],

    // Address information
    'address' => [
        'street' => env('CONTACT_ADDRESS_STREET', '123 Avenue de la République'),
        'city' => env('CONTACT_ADDRESS_CITY', 'Evian-les-Bains'),
        'postal_code' => env('CONTACT_ADDRESS_POSTAL', '74500'),
        'country' => env('CONTACT_ADDRESS_COUNTRY', 'France'),
    ],

    // Social media links
    'social' => [
        'x' => env('SOCIAL_X', 'https://x.com/stone_app'),
        'facebook' => env('SOCIAL_FACEBOOK', 'https://facebook.com/stone.app'),
        'instagram' => env('SOCIAL_INSTAGRAM', 'https://instagram.com/stone.app'),
        'linkedin' => env('SOCIAL_LINKEDIN', 'https://linkedin.com/company/stone-app'),
        'github' => env('SOCIAL_GITHUB', 'https://github.com/stone-app'),
    ],

    // Business hours
    'hours' => [
        'weekdays' => env('CONTACT_HOURS_WEEKDAYS', '9h00 - 18h00'),
        'weekend' => env('CONTACT_HOURS_WEEKEND', 'Fermé'),
        'timezone' => env('CONTACT_TIMEZONE', 'Europe/Paris'),
    ],

    // Company information
    'company' => [
        'name' => env('COMPANY_NAME', 'Stone'),
        'legal_name' => env('COMPANY_LEGAL_NAME', 'Stone SAS'),
        'owner' => env('COMPANY_OWNER', 'Micha Megret'),
        'siret' => env('COMPANY_SIRET', '123 456 789 00012'),
        'vat' => env('COMPANY_VAT', 'FR12345678901'),
    ],

];
