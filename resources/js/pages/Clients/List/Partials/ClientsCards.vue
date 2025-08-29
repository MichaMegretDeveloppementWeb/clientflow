<template>
    <!-- Container avec style événements -->
    <div class="border border-gray-200 bg-white shadow-sm p-0 overflow-hidden">
        <!-- État de chargement -->
        <div v-if="isLoading" class="divide-y divide-gray-100">
            <ClientCardSkeleton v-for="i in 6" :key="`client-skeleton-${i}`" />
        </div>

        <!-- État vide -->
<!--        <div v-else-if="clients.length === 0" class="py-20 text-center">
            <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-50 border border-slate-200/60">
                <Icon name="users" class="h-7 w-7 text-slate-400" />
            </div>
            <h3 class="mb-2 text-lg font-medium text-slate-900">
                {{ hasActiveFilters ? 'Aucun client trouvé' : 'Aucun client' }}
            </h3>
            <p class="text-sm text-slate-500 max-w-sm mx-auto">
                {{ hasActiveFilters
                    ? 'Aucun résultat ne correspond à vos critères de recherche'
                    : 'Commencez par ajouter votre premier client pour démarrer'
                }}
            </p>
        </div>-->

        <!-- Liste des clients -->
        <div v-else class="divide-y divide-gray-100">
            <ClientCard
                v-for="client in clients"
                :key="client.id"
                :client="client"
                @click="$emit('client-click', client)"
                @delete="$emit('client-delete', client.id)"
            />
        </div>
    </div>
</template>

<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card'
import Icon from '@/components/Icon.vue'
import ClientCard from './ClientCard.vue'
import ClientCardSkeleton from './ClientCardSkeleton.vue'
import type { ClientDTO } from '@/types/models'

/**
 * Grille de cartes des clients
 * Responsabilités :
 * - Affichage moderne en grille responsive
 * - États de chargement et vide
 * - Émission des événements de clic et suppression
 */

interface Props {
    clients: ClientDTO[]
    isLoading?: boolean
    hasActiveFilters?: boolean
}

const props = withDefaults(defineProps<Props>(), {
    isLoading: false,
    hasActiveFilters: false
})

const emit = defineEmits<{
    'client-click': [client: ClientDTO]
    'client-delete': [clientId: number]
}>()
</script>
