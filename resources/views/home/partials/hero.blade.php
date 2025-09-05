{{-- Hero Section --}}
<section class="relative overflow-hidden bg-gradient-to-b from-gray-50/50 via-white to-white">
    {{-- Background Pattern --}}
    <div class="bg-grid-pattern absolute inset-0 opacity-[0.02]"></div>
    <div
        class="absolute top-0 left-1/2 h-[800px] w-[800px] -translate-x-1/2 rounded-full bg-gradient-to-r from-blue-400/10 to-indigo-400/10 blur-3xl"
    ></div>

    <div class="relative mx-auto max-w-[100em] px-4 pt-24 pb-0 sm:px-6 sm:pt-32 sm:pb-0 lg:px-8">
        <div class="text-center">
            {{-- Main Heading --}}
            <h1 class="mx-auto max-w-5xl text-5xl font-bold tracking-tight text-gray-900 sm:text-6xl lg:text-7xl">
                Solution de gestion client
                <span class="relative inline-block">
                    <span class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        tout-en-un
                    </span>
                </span>
                <br />développée pour les pros
            </h1>

            {{-- Subtitle --}}
            <p class="mx-auto mt-8 max-w-3xl text-xl leading-8 text-gray-600">
                Centralisez vos clients, pilotez vos projets et optimisez vos revenus avec une plateforme pensée pour vous simplifier la vie.
                <span class="font-medium text-gray-900">Simple, rapide, française.</span>
            </p>

            {{-- Main CTA --}}
            <div class="mt-12">
                <a href="{{ route('register') }}"
                   class="group inline-flex items-center gap-3 bg-gradient-to-r from-gray-900 to-gray-800 px-6 py-3 text-lg md:text-xl font-semibold text-white shadow-xl hover:from-gray-800 hover:to-gray-700 transition-all duration-200 rounded-lg">
                    Créer mon compte gratuitement
                    <x-heroicon-o-arrow-right class="ml-2 h-5 w-5 transition-transform group-hover:translate-x-1" />
                </a>
            </div>

            {{-- Trust indicators --}}
            {{--<div class="mt-16 flex flex-col items-center space-y-6">
                <p class="text-sm font-medium text-gray-500">Faites confiance par 1000+ freelances français</p>
                <div class="flex items-center space-x-8">
                    <div class="flex items-center space-x-1 text-yellow-400">
                        <x-heroicon-s-star class="h-5 w-5 fill-current" />
                        <x-heroicon-s-star class="h-5 w-5 fill-current" />
                        <x-heroicon-s-star class="h-5 w-5 fill-current" />
                        <x-heroicon-s-star class="h-5 w-5 fill-current" />
                        <x-heroicon-s-star class="h-5 w-5 fill-current" />
                        <span class="ml-2 text-sm font-medium text-gray-700">4.9/5</span>
                    </div>
                    <div class="h-4 w-px bg-gray-300"></div>
                    <div class="text-sm text-gray-600">
                        <span class="font-medium text-gray-900">98%</span> de satisfaction
                    </div>
                    <div class="h-4 w-px bg-gray-300"></div>
                    <div class="text-sm text-gray-600">
                        <span class="font-medium text-gray-900">30 jours</span> d'essai gratuit
                    </div>
                </div>
            </div>--}}
        </div>
    </div>
</section>

<style>
.bg-grid-pattern {
    background-image: linear-gradient(rgba(0, 0, 0, 0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(0, 0, 0, 0.1) 1px, transparent 1px);
    background-size: 20px 20px;
}
</style>
