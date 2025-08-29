<template>
    <Head :title="pageTitle" />

    <AppLayout>
        <EventDetailContainer
            :event="event"
            :isLoading="isLoading"
            :hasError="hasError"
            :error="error"
            @toggle-status="toggleStatus"
            @mark-as-paid="markAsPaid"
            @retry="fetchData"
        />
    </AppLayout>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import EventDetailContainer from './Partials/EventDetailContainer.vue'
import { useEventDetailManager } from '@/composables/events/detail/useEventDetailManager'
import type { EventDetailProps } from '@/types/events/detail'

const props = defineProps<EventDetailProps>()

// Utiliser le composable principal pour gérer l'état
const {
    event,
    isLoading,
    error,
    hasError,
    fetchData,
    toggleStatus,
    markAsPaid,
} = useEventDetailManager(props.eventId, props.skeletonData)



// Titre de page dynamique avec fallback pour skeleton
const pageTitle = computed(() => {
    if (isLoading.value || !event.value) {
        return 'Détails de l\'événement'
    }
    return event.value.name
})

// Gestion des erreurs (si besoin d'afficher quelque chose de spécial)
// Pour l'instant on laisse les composants gérer leur propre état de chargement
</script>
