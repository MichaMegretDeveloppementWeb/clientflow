<template>
    <div class="border-b border-gray-100 pb-8">
        <!-- Breadcrumb navigation -->
        <nav class="mb-6 flex items-center gap-3" aria-label="Breadcrumb">
            <Link
                :href="route('dashboard')"
                class="flex items-center gap-2 text-sm text-gray-500 transition-colors hover:text-gray-700"
            >
                <Icon name="home" class="h-4 w-4" />
                <span>Tableau de bord</span>
            </Link>
            <Icon name="chevron-right" class="h-4 w-4 text-gray-400" />
            <span class="text-sm font-medium text-gray-900">Clients</span>
        </nav>

        <!-- Main header content -->
        <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="mb-2 flex items-center gap-3">
                    <div class="rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 p-2 shadow-sm">
                        <Icon name="users" class="h-6 w-6 text-white" />
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                            Clients
                            <span v-if="hasActiveFilters" class="ml-2 text-sm font-normal text-gray-500">
                                (filtré)
                            </span>
                        </h1>
                    </div>
                </div>
                <p class="max-w-2xl leading-relaxed text-gray-600">
                    Gérez vos clients et leurs projets.
                    <span v-if="isLoading" class="ml-2 inline-flex items-center gap-1 text-sm">
                        <span class="h-2 w-2 animate-pulse rounded-full bg-blue-500"></span>
                        Chargement...
                    </span>
                </p>
            </div>

            <div class="flex flex-col gap-2 sm:flex-row">
                <!-- Create client button -->
                <Button
                    @click="$emit('create')"
                    class="w-full border-0 bg-primary text-primary-foreground shadow-sm hover:bg-primary/90 sm:w-auto"
                >
                    <Icon name="plus" class="mr-2 h-4 w-4" />
                    Nouveau client
                </Button>
            </div>
        </div>

        <!-- Quick stats bar (optionnel) -->
        <div v-if="quickStats" class="mt-4 flex gap-6 text-sm">
            <div class="flex items-center gap-2">
                <span class="text-gray-500">Total :</span>
                <span class="font-semibold text-gray-900">{{ quickStats.total }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-gray-500">Actifs :</span>
                <span class="font-semibold text-green-600">{{ quickStats.active }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-gray-500">Ce mois :</span>
                <span class="font-semibold text-blue-600">{{ quickStats.thisMonth }}</span>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import Icon from '@/components/Icon.vue'

/**
 * En-tête de la page liste clients
 * Responsabilités :
 * - Breadcrumb navigation
 * - Titre et description
 * - Actions principales (refresh, create)
 * - Indicateurs d'état (loading, filtered)
 */

interface Props {
    hasActiveFilters: boolean
    isLoading?: boolean
    quickStats?: {
        total: number
        active: number
        thisMonth: number
    }
}

defineProps<Props>()

defineEmits<{
    refresh: []
    create: []
}>()
</script>
