<x-mail::message>
# Nouveau message de contact - {{ $subjectTypeLabel }}

Vous avez reçu un nouveau message via le formulaire de contact de {{ config('app.name') }}.

## Informations de contact

**Nom :** {{ $contactData['name'] }}  
**Email :** {{ $contactData['email'] }}  
**Type de demande :** {{ $subjectTypeLabel }}  
**Sujet :** {{ $contactData['subject'] }}  

## Message

<x-mail::panel>
{{ $contactData['message'] }}
</x-mail::panel>

## Informations techniques

**Date d'envoi :** {{ $contactData['sent_at']->format('d/m/Y à H:i') }}  
**IP :** {{ request()->ip() ?? 'N/A' }}  
**User Agent :** {{ request()->userAgent() ?? 'N/A' }}

---

<x-mail::button :url="'mailto:' . $contactData['email']">
Répondre directement
</x-mail::button>

Cordialement,  
L'équipe {{ config('app.name') }}
</x-mail::message>
