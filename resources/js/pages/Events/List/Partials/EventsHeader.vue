<template>
    <div class="border-b border-gray-200 pb-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <!-- Titre et breadcrumb -->
            <div class="min-w-0 flex-1">
                <nav class="flex mb-2" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2 text-sm">
                        <li>
                            <Link :href="route('dashboard')" class="text-gray-500 hover:text-gray-700">
                                Tableau de bord
                            </Link>
                        </li>
                        <Icon name="chevron-right" class="h-4 w-4 text-gray-400" />
                        <li>
                            <span class="text-gray-900 font-medium">Événements</span>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-2xl font-semibold text-gray-900">
                    Gestion des événements
                </h1>
                <p class="mt-2 text-sm text-gray-600">
                    Gérez vos tâches et facturations pour tous vos projets
                </p>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3">
                <!-- Bouton Créer -->
                <Button
                    size="sm"
                    @click="$emit('create')"
                    class="w-full border-0 bg-primary text-primary-foreground shadow-sm hover:bg-primary/90 sm:w-auto"
                >
                    <Icon name="plus" class="h-4 w-4" />
                    <span class="ml-2">Nouvel événement</span>
                </Button>
            </div>
        </div>

        <!-- Indicateurs de filtres actifs -->
        <div v-if="hasActiveFilters" class="mt-4">
            <div class="flex items-center gap-2">
                <Icon name="filter" class="h-4 w-4 text-amber-500" />
                <span class="text-sm text-amber-600 font-medium">
                    Des filtres sont actifs
                </span>
                <div class="h-1 w-1 rounded-full bg-amber-400"></div>
                <span class="text-sm text-gray-500">
                    Utilisez le panneau de filtres pour les modifier
                </span>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import Icon from '@/components/Icon.vue'
import { Button } from '@/components/ui/button'

interface Props {
    hasActiveFilters: boolean
    isLoading: boolean
}

defineProps<Props>()

defineEmits<{
    refresh: []
    create: []
}>()
</script>
