<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO Meta Tags --}}
    <title>@yield('title', config('app.name', 'Stone') . ' - Gestion client et projet pour freelances')</title>
    <meta name="description" content="@yield('description', 'Stone : solution de gestion tout-en-un gratuite et open source pour freelances français. Clients, projets, facturation en toute simplicité.')">
    <meta name="keywords" content="@yield('keywords', 'gestion client, freelance, CRM gratuit, gestion projet, facturation, open source, France')">
    <meta name="author" content="Micha Megret - Développeur Web Freelance">
    <meta name="robots" content="@yield('robots', 'index, follow')">
    <link rel="canonical" href="@yield('canonical', request()->url())">

    {{-- Open Graph --}}
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="@yield('og_title', config('app.name', 'Stone') . ' - Gestion client et projet pour freelances')">
    <meta property="og:description" content="@yield('og_description', 'Solution de gestion tout-en-un gratuite et open source pour freelances français. Clients, projets, facturation en toute simplicité.')">
    <meta property="og:url" content="@yield('og_url', request()->url())">
    <meta property="og:site_name" content="{{ config('app.name', 'Stone') }}">
    <meta property="og:image" content="@yield('og_image', asset('images/og-default.jpg'))">
    <meta property="og:image:alt" content="@yield('og_image_alt', 'Stone - Gestion client et projet pour freelances')">
    <meta property="og:locale" content="fr_FR">

    {{-- Twitter Cards --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('twitter_title', config('app.name', 'Stone') . ' - Gestion client et projet pour freelances')">
    <meta name="twitter:description" content="@yield('twitter_description', 'Solution de gestion tout-en-un gratuite et open source pour freelances français.')">
    <meta name="twitter:image" content="@yield('twitter_image', asset('images/og-default.jpg'))">
    <meta name="twitter:image:alt" content="@yield('twitter_image_alt', 'Stone - Gestion client et projet pour freelances')">

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/favicon.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
{{--    <link rel="manifest" href="{{ asset('site.webmanifest') }}">--}}

    {{-- Hreflang (if multilingual later) --}}
    @stack('hreflang')

    {{-- Page specific head content --}}
    @stack('head')

    {{-- Vite Assets --}}
    @vite(['resources/js/app.ts'])

    {{-- Alpine.js for mobile menu --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- JSON-LD Structured Data --}}
    <script type="application/ld+json">
@php
use App\Services\JsonLdService;

$pageSchema = [];
$schemaContent = $__env->yieldContent('json-ld-schema');

if (!empty($schemaContent)) {
    $pageSchema = json_decode($schemaContent, true) ?? [];
}

echo JsonLdService::render($pageSchema);
@endphp
    </script>

</head>

<body class="min-h-screen bg-white font-sans antialiased">
    <div id="app" class="px-3">
        {{-- Navigation --}}
        <x-guest.navbar />

        {{-- Main Content --}}
        @yield('content')

        {{-- Footer --}}
        <x-guest.footer />
    </div>

    {{-- Page specific scripts --}}
    @stack('scripts')

</body>
</html>
