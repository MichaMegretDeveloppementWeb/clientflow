@extends('layouts.guest')

@section('title', 'Désinscription - Stone')
@section('description', 'Gérez votre abonnement à la liste de diffusion des mises à jour de Stone.')

@section('content')
<section class="bg-gray-50 py-24 min-h-screen">
    <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center h-16 w-16 bg-blue-100 rounded-full mb-4">
                    <x-heroicon-o-envelope class="h-8 w-8 text-blue-600" />
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    Gestion de votre abonnement
                </h1>
                <p class="text-gray-600">
                    Liste de diffusion des mises à jour Stone
                </p>
            </div>

            <div class="bg-gray-50 rounded-lg p-6 mb-8">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-at-symbol class="h-6 w-6 text-gray-400 mt-1" />
                    </div>
                    <div>
                        <div class="font-medium text-gray-900">{{ $subscriber->email }}</div>
                        <div class="text-sm text-gray-600 mt-1">
                            @if($subscriber->is_active)
                                <span class="inline-flex items-center gap-1">
                                    <div class="h-2 w-2 bg-green-500 rounded-full"></div>
                                    Inscrit depuis le {{ $subscriber->subscribed_at->format('d/m/Y') }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1">
                                    <div class="h-2 w-2 bg-red-500 rounded-full"></div>
                                    Désinscrit le {{ $subscriber->unsubscribed_at?->format('d/m/Y') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if($subscriber->is_active)
                {{-- Active subscriber - show unsubscribe options --}}
                <div class="space-y-6">
                    <div class="text-center">
                        <h2 class="text-xl font-semibold text-gray-900 mb-2">
                            Voulez-vous vous désinscrire ?
                        </h2>
                        <p class="text-gray-600 mb-6">
                            Vous ne recevrez plus d'emails concernant les mises à jour de Stone.
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <form method="POST" action="{{ route('newsletter.unsubscribe', $token) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                                <x-heroicon-o-x-mark class="h-5 w-5" />
                                Me désinscrire
                            </button>
                        </form>
                        
                        <a href="{{ route('home') }}" 
                           class="w-full sm:w-auto bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 text-center">
                            Garder mon abonnement
                        </a>
                    </div>
                </div>
            @else
                {{-- Inactive subscriber - show resubscribe option --}}
                <div class="space-y-6">
                    <div class="text-center">
                        <h2 class="text-xl font-semibold text-gray-900 mb-2">
                            Vous êtes actuellement désinscrit
                        </h2>
                        <p class="text-gray-600 mb-6">
                            Souhaitez-vous vous réinscrire aux mises à jour de Stone ?
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <form method="POST" action="{{ route('newsletter.resubscribe', $token) }}">
                            @csrf
                            <button type="submit" 
                                    class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                                <x-heroicon-o-check class="h-5 w-5" />
                                Me réinscrire
                            </button>
                        </form>
                        
                        <a href="{{ route('home') }}" 
                           class="w-full sm:w-auto bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 text-center">
                            Retour à l'accueil
                        </a>
                    </div>
                </div>
            @endif

            {{-- Information section --}}
            <div class="mt-12 pt-8 border-t border-gray-200">
                <div class="text-center">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        À propos de nos emails
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-gray-600">
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-check class="h-4 w-4 text-green-600" />
                            <span>Uniquement les mises à jour</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-check class="h-4 w-4 text-green-600" />
                            <span>Fréquence raisonnable</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-check class="h-4 w-4 text-green-600" />
                            <span>Désinscription facile</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-heroicon-o-check class="h-4 w-4 text-green-600" />
                            <span>Pas de spam</span>
                        </div>
                    </div>
                </div>

                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-500">
                        Des questions ? Contactez-nous à 
                        <a href="mailto:support@stone-app.fr" class="text-blue-600 hover:underline">
                            support@stone-app.fr
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection