{{-- Mini Feature: Actions rapides --}}
<section class="bg-gradient-to-b from-white to-gray-50 py-16">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-10 lg:grid-cols-2 lg:gap-0">
            {{-- Mockup --}}
            <div class="order-2 lg:order-1">
                <div class="relative mx-auto w-[85%] max-w-md">
                    {{-- Interface mockup --}}
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-xl">
                        {{-- Client rows in skeleton --}}
                        <div class="space-y-3">
                            <div class="flex items-center justify-between rounded-lg border border-gray-100 p-4">
                                <div class="flex items-center space-x-3">
                                    <div class="h-10 w-10 rounded-full bg-gray-200"></div>
                                    <div class="space-y-2">
                                        <div class="h-3 w-24 rounded bg-gray-200"></div>
                                        <div class="h-2 w-16 rounded bg-gray-100"></div>
                                    </div>
                                </div>
                                <div class="h-2 w-2 rounded-full bg-gray-300"></div>
                            </div>

                            {{-- Featured row with dropdown --}}
                            <div class="relative flex items-center justify-between rounded-lg border-2 border-blue-200 bg-blue-50 p-4">
                                <div class="flex items-center space-x-3">
                                    <div class="h-10 w-10 rounded-full bg-blue-200"></div>
                                    <div class="space-y-2">
                                        <div class="h-3 w-24 rounded bg-blue-300"></div>
                                        <div class="h-2 w-16 rounded bg-blue-200"></div>
                                    </div>
                                </div>
                                {{-- Dropdown menu (detailed) --}}
                                <div class="absolute right-4 top-16 z-10 w-48 rounded-lg border border-gray-200 bg-white py-1 shadow-lg">
                                    <button class="flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <x-heroicon-o-eye class="mr-3 h-4 w-4 text-gray-400" />
                                        Voir le détail
                                    </button>
                                    <button class="flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <x-heroicon-o-pencil class="mr-3 h-4 w-4 text-gray-400" />
                                        Modifier
                                    </button>
                                    <button class="flex w-full items-center px-4 py-2 text-sm text-blue-600 hover:bg-blue-50">
                                        <x-heroicon-o-plus class="mr-3 h-4 w-4 text-blue-500" />
                                        Nouveau projet
                                    </button>
                                    <button class="flex w-full items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <x-heroicon-o-trash class="mr-3 h-4 w-4 text-red-500" />
                                        Supprimer
                                    </button>
                                </div>
                                {{-- Cursor --}}
                                <div class="absolute right-8 top-12">
                                    <x-heroicon-o-cursor-arrow-rays class="h-5 w-5 text-blue-600" />
                                </div>
                            </div>

                            <div class="flex items-center justify-between rounded-lg border border-gray-100 p-4">
                                <div class="flex items-center space-x-3">
                                    <div class="h-10 w-10 rounded-full bg-gray-200"></div>
                                    <div class="space-y-2">
                                        <div class="h-3 w-24 rounded bg-gray-200"></div>
                                        <div class="h-2 w-16 rounded bg-gray-100"></div>
                                    </div>
                                </div>
                                <div class="h-2 w-2 rounded-full bg-gray-300"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Content --}}
            <div class="order-1 lg:order-2 lg:pl-8 flex flex-col justify-center items-start">
                <div class="mb-4 inline-flex items-center gap-2 rounded-full bg-blue-100 px-3 py-1 text-blue-700">
                    <x-heroicon-o-bolt class="h-3 w-3" />
                    <span class="text-xs font-medium">Actions rapides</span>
                </div>
                <h3 class="mb-4 text-2xl font-bold text-gray-900">
                    Gagnez du temps sur chaque interaction
                </h3>
                <p class="text-gray-600 leading-relaxed">
                    Directement depuis votre liste clients, accédez à toutes les actions importantes : consulter les détails, créer un nouveau projet, modifier ou supprimer votre client en un clic.
                </p>
            </div>
        </div>
    </div>
</section>
