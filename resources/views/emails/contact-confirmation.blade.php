<x-mail::message>
# Confirmation de réception - {{ $subjectTypeLabel }}

Bonjour {{ $contactData['name'] }},

Nous avons bien reçu votre message concernant "{{ $contactData['subject'] }}".

## Récapitulatif de votre demande

**Type de demande :** {{ $subjectTypeLabel }}  
**Sujet :** {{ $contactData['subject'] }}  
**Envoyé le :** {{ $contactData['sent_at']->format('d/m/Y à H:i') }}

<x-mail::panel>
{{ $contactData['message'] }}
</x-mail::panel>

## Prochaines étapes

Notre équipe examinera votre demande et vous répondra dans les plus brefs délais, généralement sous 24-48 heures ouvrables.

Si votre demande est urgente, n'hésitez pas à nous contacter directement.

<x-mail::button :url="config('app.url')">
Retourner sur {{ config('app.name') }}
</x-mail::button>

Merci de nous avoir contactés,  
L'équipe {{ config('app.name') }}
</x-mail::message>
