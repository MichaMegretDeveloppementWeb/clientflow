<template>
    <Head title="Clients" />

    <AppLayout>
        <ClientsListContainer
            :skeleton-data="skeletonData"
            :clients-data="clientsData"
            @client-deleted="handleClientDeleted"
            @filters-changed="handleFiltersChanged"
        />
    </AppLayout>

</template>


<script setup lang="ts">
import { toRefs } from 'vue'
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import ClientsListContainer from './Partials/ClientsListContainer.vue'
import type { ClientListProps } from '@/types/clients/list'

/**
 * Page principale de la liste des clients
 * Responsabilité unique : Point d'entrée et orchestration de haut niveau
 * Délègue toute la logique au ClientsListContainer
 */

interface Props {
    skeletonData: ClientListProps
    clientsData?: any
}

const props = defineProps<Props>()

// Props utilisées dans le template via destructuring automatique
const { skeletonData, clientsData } = toRefs(props)

// Gestionnaires d'événements de haut niveau
const handleClientDeleted = (clientId: number) => {
    // Réservé : Analytics ou notifications globales futures
    console.log('Client deleted:', clientId)
}

const handleFiltersChanged = (filters: any) => {
    // Réservé : Persistance des préférences utilisateur
    console.log('Filters changed:', filters)
}
</script>
