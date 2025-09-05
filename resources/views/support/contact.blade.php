@extends('layouts.guest')

@section('title', 'Contact - Stone')
@section('description', 'Contactez l\'équipe Stone pour toute question, support technique ou demande d\'information. Nous sommes là pour vous accompagner.')

@section('json-ld-schema')
{
    "@type": "ContactPage",
    "name": "Contact - Stone",
    "description": "Contactez l'équipe Stone pour toute question, support technique ou demande d'information. Nous sommes là pour vous accompagner.",
    "url": "{{ url()->current() }}",
    "breadcrumb": {
        "@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@type": "ListItem",
                "position": 1,
                "name": "Accueil",
                "item": "{{ route('home') }}"
            },
            {
                "@type": "ListItem",
                "position": 2,
                "name": "Contact"
            }
        ]
    },
    "mainEntity": {
        "@type": "Organization",
        "name": "{{ config('contact.company.name') }}",
        "email": "{{ config('contact.emails.contact') }}",
        "telephone": "{{ config('contact.phone.main') }}",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "{{ config('contact.address.street') }}",
            "addressLocality": "{{ config('contact.address.city') }}",
            "postalCode": "{{ config('contact.address.postal_code') }}",
            "addressCountry": "{{ config('contact.address.country') }}"
        }
    }
}
@endsection

@section('content')
{{-- Hero Section --}}
<section class="bg-gradient-to-b from-blue-50/50 via-white to-white pt-24 pb-0 sm:pt-32 sm:pb-0">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <div class="mb-6 inline-flex items-center gap-2 rounded-full bg-blue-100 px-4 py-2 text-blue-700">
                <x-heroicon-o-chat-bubble-left-right class="h-5 w-5" />
                <span class="text-sm font-medium">Support client</span>
            </div>
            <h1 class="mx-auto max-w-4xl text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl lg:text-6xl">
                Nous sommes là
                <span class="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent">
                    pour vous aider
                </span>
            </h1>
            <p class="mx-auto mt-6 max-w-2xl text-xl leading-8 text-gray-600">
                Une question sur Stone ? Un problème technique ? Besoin d'aide pour optimiser votre utilisation ?
                Notre équipe est à votre écoute pour vous accompagner.
            </p>
        </div>
    </div>
</section>

{{-- Contact Methods --}}
<section class="bg-white py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center mb-16">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                Plusieurs façons de nous joindre
            </h2>
            <p class="mt-4 text-lg leading-8 text-gray-600">
                Choisissez le canal qui vous convient le mieux selon votre besoin
            </p>
        </div>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
            {{-- Email Contact --}}
            <div class="text-center group">
                <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-blue-100 group-hover:bg-blue-200 transition-colors">
                    <x-heroicon-o-envelope class="h-10 w-10 text-blue-600" />
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Email</h3>
                <p class="text-gray-600 mb-4">
                    Pour toute question générale ou demande d'information
                </p>
                <div class="space-y-2">
                    <p class="text-sm text-gray-500">Contact général</p>
                    <a href="mailto:{{ config('contact.emails.contact') }}"
                       class="text-lg font-medium text-blue-600 hover:text-blue-700">
                        {{ config('contact.emails.contact') }}
                    </a>
                </div>
            </div>

            {{-- Phone Contact --}}
            <div class="text-center group">
                <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-green-100 group-hover:bg-green-200 transition-colors">
                    <x-heroicon-o-phone class="h-10 w-10 text-green-600" />
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Téléphone</h3>
                <p class="text-gray-600 mb-4">
                    Pour un contact direct et rapide
                </p>
                <div class="space-y-2">
                    <p class="text-sm text-gray-500">{{ config('contact.hours.weekdays') }}</p>
                    <a href="tel:{{ str_replace(' ', '', config('contact.phone.main')) }}"
                       class="text-lg font-medium text-green-600 hover:text-green-700">
                        {{ config('contact.phone.main') }}
                    </a>
                </div>
            </div>

            {{-- Support Technique --}}
            <div class="text-center group">
                <div class="mx-auto mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-purple-100 group-hover:bg-purple-200 transition-colors">
                    <x-heroicon-o-cog-6-tooth class="h-10 w-10 text-purple-600" />
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Support technique</h3>
                <p class="text-gray-600 mb-4">
                    Pour les problèmes techniques et bugs
                </p>
                <div class="space-y-2">
                    <p class="text-sm text-gray-500">Support prioritaire</p>
                    <a href="mailto:{{ config('contact.emails.support') }}"
                       class="text-lg font-medium text-purple-600 hover:text-purple-700">
                        {{ config('contact.emails.support') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Contact Form --}}
<section class="bg-gray-50 py-24">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center mb-12">
            <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">
                Envoyez-nous un message
            </h2>
            <p class="mt-4 text-lg leading-8 text-gray-600">
                Remplissez le formulaire ci-dessous et nous vous répondrons dans les plus brefs délais
            </p>
        </div>

        <div class="mx-auto max-w-xl">
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <form id="contact-form" action="{{ route('guest.contact.send') }}" method="POST" class="space-y-6">
                    @csrf

                    {{-- Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nom complet *
                        </label>
                        <input type="text"
                               id="name"
                               name="name"
                               required
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                        <div id="name-error" class="hidden mt-2 text-sm text-red-600"></div>
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Adresse email *
                        </label>
                        <input type="email"
                               id="email"
                               name="email"
                               required
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                        <div id="email-error" class="hidden mt-2 text-sm text-red-600"></div>
                    </div>

                    {{-- Subject Type --}}
                    <div>
                        <label for="subject_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Type de demande *
                        </label>
                        <select id="subject_type"
                                name="subject_type"
                                required
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                            <option value="">Sélectionnez un type</option>
                            <option value="general">Question générale</option>
                            <option value="support">Support technique</option>
                            <option value="billing">Facturation</option>
                            <option value="feature">Demande de fonctionnalité</option>
                            <option value="bug">Signalement de bug</option>
                            <option value="other">Autre</option>
                        </select>
                        <div id="subject_type-error" class="hidden mt-2 text-sm text-red-600"></div>
                    </div>

                    {{-- Subject --}}
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                            Sujet *
                        </label>
                        <input type="text"
                               id="subject"
                               name="subject"
                               required
                               placeholder="Décrivez brièvement votre demande"
                               class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                        <div id="subject-error" class="hidden mt-2 text-sm text-red-600"></div>
                    </div>

                    {{-- Message --}}
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                            Message *
                        </label>
                        <textarea id="message"
                                  name="message"
                                  rows="6"
                                  required
                                  placeholder="Décrivez votre demande en détail..."
                                  class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white resize-none"></textarea>
                        <div id="message-error" class="hidden mt-2 text-sm text-red-600"></div>
                    </div>

                    {{-- Privacy Notice --}}
                    <div>
                        <div class="flex items-start gap-3">
                            <input type="checkbox"
                                   id="privacy"
                                   name="privacy"
                                   required
                                   class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="privacy" class="text-sm text-gray-600">
                                J'accepte que mes données soient utilisées pour traiter ma demande conformément à notre
                                <a href="{{ route('legal.privacy') }}" class="text-blue-600 hover:underline">politique de confidentialité</a>.
                            </label>
                        </div>
                        <div id="privacy-error" class="hidden mt-2 text-sm text-red-600"></div>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit"
                            id="contact-submit"
                            class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold py-4 px-6 rounded-lg shadow-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 flex items-center justify-center gap-2">
                        <x-heroicon-o-paper-airplane class="h-5 w-5" />
                        Envoyer le message
                    </button>
                </form>

                {{-- Success/Error Messages --}}
                <div id="contact-messages" class="mt-6 hidden">
                    <div id="contact-success" class="hidden p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center gap-3">
                            <x-heroicon-o-check-circle class="h-6 w-6 text-green-600" />
                            <div>
                                <h3 class="font-medium text-green-800">Message envoyé !</h3>
                                <p class="text-sm text-green-700">Nous vous répondrons dans les plus brefs délais.</p>
                            </div>
                        </div>
                    </div>

                    <div id="contact-error" class="hidden p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-center gap-3">
                            <x-heroicon-o-x-circle class="h-6 w-6 text-red-600" />
                            <div>
                                <h3 class="font-medium text-red-800">Erreur d'envoi</h3>
                                <p class="text-sm text-red-700">Une erreur est survenue. Veuillez réessayer ou nous contacter directement.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Additional Contact Info --}}
<section class="bg-white py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
            {{-- Business Info --}}
            <div class="bg-gray-50 rounded-2xl p-8">
                <h3 class="text-xl font-semibold text-gray-900 mb-6">Informations entreprise</h3>
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <x-heroicon-o-building-office class="h-6 w-6 text-gray-400 mt-1" />
                        <div>
                            <div class="font-medium text-gray-900">{{ config('contact.company.legal_name') }}</div>
                            <div class="text-sm text-gray-600">{{ config('contact.company.owner') }}</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <x-heroicon-o-map-pin class="h-6 w-6 text-gray-400 mt-1" />
                        <div>
                            <div class="text-gray-900">{{ config('contact.address.street') }}</div>
                            <div class="text-gray-900">{{ config('contact.address.postal_code') }} {{ config('contact.address.city') }}</div>
                            <div class="text-gray-900">{{ config('contact.address.country') }}</div>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <x-heroicon-o-clock class="h-6 w-6 text-gray-400 mt-1" />
                        <div>
                            <div class="text-gray-900">Lun - Ven : {{ config('contact.hours.weekdays') }}</div>
                            <div class="text-gray-900">Weekend : {{ config('contact.hours.weekend') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Contact Rapide --}}
            <div class="bg-blue-50 rounded-2xl p-8">
                <h3 class="text-xl font-semibold text-gray-900 mb-6">Contacts spécialisés</h3>
                <div class="space-y-4">
                    <div>
                        <div class="font-medium text-gray-900 mb-1">Support technique</div>
                        <a href="mailto:{{ config('contact.emails.support') }}"
                           class="text-blue-600 hover:text-blue-700">{{ config('contact.emails.support') }}</a>
                    </div>

                    <div>
                        <div class="font-medium text-gray-900 mb-1">Facturation</div>
                        <a href="mailto:{{ config('contact.emails.billing') }}"
                           class="text-blue-600 hover:text-blue-700">{{ config('contact.emails.billing') }}</a>
                    </div>

                    <div>
                        <div class="font-medium text-gray-900 mb-1">Sécurité</div>
                        <a href="mailto:{{ config('contact.emails.security') }}"
                           class="text-blue-600 hover:text-blue-700">{{ config('contact.emails.security') }}</a>
                    </div>

                    <div>
                        <div class="font-medium text-gray-900 mb-1">Suggestions</div>
                        <a href="mailto:{{ config('contact.emails.feedback') }}"
                           class="text-blue-600 hover:text-blue-700">{{ config('contact.emails.feedback') }}</a>
                    </div>
                </div>

                {{-- Social Links --}}
                <div class="mt-8 pt-6 border-t border-blue-200">
                    <div class="font-medium text-gray-900 mb-4">Suivez-nous</div>
                    <div class="flex items-center gap-3">
                        <a href="{{ config('contact.social.x') }}" target="_blank"
                           class="flex items-center justify-center h-10 w-10 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow">
                            <svg class="h-5 w-5 text-gray-900" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                        </a>

                        <a href="{{ config('contact.social.facebook') }}" target="_blank"
                           class="flex items-center justify-center h-10 w-10 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow">
                            <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>

                        <a href="{{ config('contact.social.instagram') }}" target="_blank"
                           class="flex items-center justify-center h-10 w-10 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow">
                            <svg class="h-5 w-5 text-pink-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                        </a>

                        <a href="{{ config('contact.social.linkedin') }}" target="_blank"
                           class="flex items-center justify-center h-10 w-10 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow">
                            <svg class="h-5 w-5 text-blue-700" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                            </svg>
                        </a>

                        <a href="{{ config('contact.social.github') }}" target="_blank"
                           class="flex items-center justify-center h-10 w-10 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow">
                            <svg class="h-5 w-5 text-gray-900" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- JavaScript pour le formulaire avec gestion des erreurs de validation --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('contact-form');
    const submitBtn = document.getElementById('contact-submit');
    const messagesContainer = document.getElementById('contact-messages');
    const successMessage = document.getElementById('contact-success');
    const errorMessage = document.getElementById('contact-error');

    // Store original button content
    const originalBtnContent = submitBtn.innerHTML;

    // Function to clear all field errors
    function clearFieldErrors() {
        const errorFields = ['name', 'email', 'subject_type', 'subject', 'message', 'privacy'];
        errorFields.forEach(field => {
            const errorDiv = document.getElementById(field + '-error');
            const inputField = document.querySelector(`[name="${field}"]`);
            if (errorDiv) {
                errorDiv.classList.add('hidden');
                errorDiv.textContent = '';
            }
            if (inputField) {
                inputField.classList.remove('border-red-500', 'ring-red-500');
                inputField.classList.add('border-gray-300');
            }
        });
    }

    // Function to display field-specific errors
    function displayFieldErrors(errors) {
        Object.keys(errors).forEach(field => {
            const errorDiv = document.getElementById(field + '-error');
            const inputField = document.querySelector(`[name="${field}"]`);

            if (errorDiv && errors[field].length > 0) {
                errorDiv.textContent = errors[field][0]; // Show first error message
                errorDiv.classList.remove('hidden');

                // Highlight the input field
                if (inputField) {
                    inputField.classList.remove('border-gray-300');
                    inputField.classList.add('border-red-500', 'ring-red-500');
                }
            }
        });
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Clear previous errors
        clearFieldErrors();
        messagesContainer.classList.add('hidden');
        successMessage.classList.add('hidden');
        errorMessage.classList.add('hidden');

        // Show loading state
        submitBtn.innerHTML = `
            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Envoi en cours...
        `;
        submitBtn.disabled = true;

        // Get form data
        const formData = new FormData(form);

        // Send AJAX request
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(async response => {
            const data = await response.json();

            if (response.ok && data.success) {
                // Show success message
                messagesContainer.classList.remove('hidden');
                successMessage.classList.remove('hidden');
                errorMessage.classList.add('hidden');

                // Reset form
                form.reset();

                // Scroll to success message
                messagesContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
            } else {
                // Handle validation errors (422) or other errors
                if (response.status === 422 && data.errors) {
                    // Display field-specific validation errors
                    displayFieldErrors(data.errors);

                    // Scroll to first error field
                    const firstErrorField = Object.keys(data.errors)[0];
                    const firstErrorInput = document.querySelector(`[name="${firstErrorField}"]`);
                    if (firstErrorInput) {
                        firstErrorInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstErrorInput.focus();
                    }
                } else {
                    // Show generic error message for other types of errors
                    messagesContainer.classList.remove('hidden');
                    errorMessage.classList.remove('hidden');
                    successMessage.classList.add('hidden');
                    messagesContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        })
        .catch(error => {
            console.error('Network error:', error);

            // Show generic error message for network errors
            messagesContainer.classList.remove('hidden');
            errorMessage.classList.remove('hidden');
            successMessage.classList.add('hidden');
            messagesContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
        })
        .finally(() => {
            // Reset button state
            submitBtn.innerHTML = originalBtnContent;
            submitBtn.disabled = false;
        });
    });

    // Clear field errors on input change
    const formInputs = form.querySelectorAll('input, select, textarea');
    formInputs.forEach(input => {
        input.addEventListener('input', function() {
            const fieldName = this.getAttribute('name');
            const errorDiv = document.getElementById(fieldName + '-error');

            if (errorDiv && !errorDiv.classList.contains('hidden')) {
                errorDiv.classList.add('hidden');
                errorDiv.textContent = '';
                this.classList.remove('border-red-500', 'ring-red-500');
                this.classList.add('border-gray-300');
            }
        });
    });
});
</script>
@endsection
