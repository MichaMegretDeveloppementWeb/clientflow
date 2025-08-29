<template>
    <div class="border-b border-gray-100 pb-8">
        <!-- Breadcrumb -->
        <div class="mb-6 flex items-center gap-3 text-sm">
            <!-- Skeleton pour breadcrumb -->
            <div v-if="isLoading" class="flex items-center gap-3">
                <div class="h-4 w-16 bg-gray-200 rounded animate-pulse"></div>
                <div class="h-4 w-px bg-gray-300"></div>
                <div class="h-4 w-20 bg-gray-200 rounded animate-pulse"></div>
                <div class="h-4 w-px bg-gray-300"></div>
                <div class="h-4 w-24 bg-gray-200 rounded animate-pulse"></div>
                <div class="h-4 w-px bg-gray-300"></div>
                <div class="h-4 w-28 bg-gray-200 rounded animate-pulse"></div>
            </div>
            <!-- Breadcrumb réel -->
            <template v-else-if="!hasError">
                <Link :href="route('dashboard')" class="flex items-center gap-2 text-gray-500 hover:text-gray-700 transition-colors">
                    <Icon name="home" class="h-4 w-4" />
                    <span>Tableau de bord</span>
                </Link>
                <div class="h-4 w-px bg-gray-300"></div>
                <Link :href="route('clients.index')" class="text-gray-500 transition-colors hover:text-gray-700">
                    Clients
                </Link>
                <div class="h-4 w-px bg-gray-300"></div>
                <Link
                    v-if="client"
                    :href="route('clients.show', client.id)"
                    class="text-gray-500 transition-colors hover:text-gray-700"
                >
                    {{ client.name }}
                </Link>
                <div class="h-4 w-px bg-gray-300"></div>
                <span class="font-medium text-gray-900">Modifier</span>
            </template>
        </div>

        <!-- Main header content -->
        <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="mb-2 flex items-center gap-3">
                    <div class="rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 p-2 shadow-sm">
                        <Icon name="user-pen" class="h-6 w-6 text-white" />
                    </div>
                    <div>
                        <!-- Skeleton pour titre -->
                        <div v-if="isLoading" class="space-y-2">
                            <div class="h-8 w-64 bg-gray-200 rounded animate-pulse"></div>
                            <div class="h-4 w-48 bg-gray-200 rounded animate-pulse"></div>
                        </div>
                        <!-- Erreur -->
                        <div v-else-if="hasError">
                            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Erreur</h1>
                            <p class="text-sm text-gray-600">Impossible de charger les données du client</p>
                        </div>
                        <!-- Données réelles -->
                        <div v-else>
                            <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                                Modifier le client
                            </h1>
                            <p class="text-sm text-gray-600">
                                {{ client?.name || '' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</template>

<script setup lang="ts">
import { route } from 'ziggy-js'
import Icon from '@/components/Icon.vue'
import { Link } from '@inertiajs/vue3'
import type { ClientEditHeaderProps } from '@/types/clients/edit'

defineProps<ClientEditHeaderProps>()

</script>
