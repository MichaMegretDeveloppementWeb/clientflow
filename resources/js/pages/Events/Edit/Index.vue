<template>
    <Head :title="pageTitle" />

    <AppLayout>
        <div class="space-y-8 mx-auto max-w-7xl">
            <!-- Header -->
            <EventEditHeader
                :event="event"
                :is-loading="isLoading"
                :has-error="hasError"
            />

            <!-- Form -->
            <EventEditForm
                :event="event"
                :is-loading="isLoading"
                :has-error="hasError"
            />
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import EventEditHeader from './Partials/EventEditHeader.vue'
import EventEditForm from './Partials/EventEditForm.vue'
import { useEventEditManager } from '@/composables/events/edit/useEventEditManager'
import type { EventEditData, EventEditSkeletonData } from '@/types/events/edit'

interface Props {
    eventId: number
    skeletonData: EventEditSkeletonData
    data?: EventEditData
}

const props = defineProps<Props>()

// Composable pour la gestion des données
const {
    event,
    isLoading,
    hasError,
} = useEventEditManager(props.eventId, props.skeletonData, props.data)

// Titre de la page
const pageTitle = computed(() => {
    if (hasError.value) return 'Erreur - Modification événement'
    if (isLoading.value) return 'Modification événement'
    return `Modifier ${event.value?.name || 'événement'}`
})
</script>
