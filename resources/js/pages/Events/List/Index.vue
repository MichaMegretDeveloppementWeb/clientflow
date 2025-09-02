<template>
    <Head title="Événements" />

    <AppLayout>
        <EventsListContainer :skeleton-data="skeletonData" :events-data="eventsData" />
    </AppLayout>

</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import EventsListContainer from './Partials/EventsListContainer.vue'
import type { EventListProps } from '@/types/events/list'

interface Props {
    skeletonData: EventListProps
    eventsData?: any
}

defineProps<Props>()
</script>
