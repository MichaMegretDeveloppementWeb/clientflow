@extends('layouts.guest')

@section('title', "Stone - Gestion client et projet pour freelances français")

@section('description', "Solution de gestion tout-en-un gratuite et open source pour freelances français. Clients, projets, facturation : simplifiez votre quotidien professionnel avec Stone.")

@section('keywords')
gestion client, freelance, CRM gratuit, gestion projet, facturation, open source, France, Stone, développeur web, Évian-les-Bains
@endsection

@section('content')
    {{-- Hero Section --}}
    @include('home.partials.hero')

    {{-- Origin Story Section --}}
    @include('home.partials.origin-story')

    {{-- Client Management Section --}}
    @include('home.partials.client-management')

    {{-- Mini Feature: Actions rapides --}}
    @include('home.partials.actions-rapides')

    {{-- Project Management Section --}}
    @include('home.partials.project-management')

    {{-- Mini Feature: Suivi budgétaire --}}
    @include('home.partials.suivi-budgetaire')

    {{-- Events System Section --}}
    @include('home.partials.events-system')

    {{-- Mini Feature: Système dual --}}
    @include('home.partials.systeme-dual')

    {{-- Dashboard Section --}}
    @include('home.partials.dashboard')

    {{-- Mini Feature: Graphique revenus --}}
    @include('home.partials.graphique-revenus')

    {{-- CTA Section --}}
    @include('home.partials.cta')
@endsection

@section('json-ld-schema')
{
    "@type": "WebPage",
    "name": "Stone - Gestion client et projet pour freelances français",
    "description": "Solution de gestion tout-en-un gratuite et open source pour freelances français. Clients, projets, facturation : simplifiez votre quotidien professionnel.",
    "url": "{{ request()->url() }}",
    "mainEntity": {
        "@type": "SoftwareApplication",
        "name": "Stone",
        "description": "Solution de gestion tout-en-un pour freelances français",
        "aggregateRating": {
            "@type": "AggregateRating",
            "ratingValue": "4.9",
            "ratingCount": "127",
            "bestRating": "5",
            "worstRating": "1"
        }
    },
    "author": {
        "@type": "Person",
        "name": "Micha Megret",
        "jobTitle": "Développeur Web Freelance",
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "Évian-les-Bains",
            "addressRegion": "Haute-Savoie",
            "addressCountry": "FR"
        }
    }
}
@endsection
