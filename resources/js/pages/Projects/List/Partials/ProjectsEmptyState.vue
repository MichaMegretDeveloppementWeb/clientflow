<template>
    <div class="py-12 text-center">
        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-50">
            <Icon
                :name="hasActiveFilters ? 'search-x' : 'folder-plus'"
                class="h-8 w-8 text-gray-400"
            />
        </div>

        <h3 class="mb-1 text-lg font-medium text-gray-900">
            {{ hasActiveFilters ? 'Aucun projet trouvé' : 'Aucun projet' }}
        </h3>

        <p class="mb-4 text-sm text-gray-500">
            {{ hasActiveFilters
                ? 'Essayez de modifier vos critères de recherche'
                : 'Créez votre premier projet pour commencer'
            }}
        </p>

        <div class="flex flex-col gap-2 sm:flex-row sm:justify-center sm:gap-3">
            <Button
                v-if="hasActiveFilters"
                variant="outline"
                @click="$emit('clear-filters')"
                class="border-gray-300 hover:bg-gray-50"
            >
                <Icon name="filter-x" class="mr-2 h-4 w-4" />
                Effacer les filtres
            </Button>

            <Button
                as-child
                class="bg-purple-600 text-white hover:bg-purple-700"
                @click="$emit('create-project')"
            >
                <Link :href="route('projects.create')">
                    <Icon name="plus" class="mr-2 h-4 w-4" />
                    Créer un projet
                </Link>
            </Button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button'
import Icon from '@/components/Icon.vue'
import { Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

interface Props {
    hasActiveFilters: boolean
}

defineProps<Props>()

defineEmits<{
    'clear-filters': []
    'create-project': []
}>()
</script>
