<template>
    <Head title="Créer un événement" />

    <AppLayout>
        <div class="space-y-8">
            <!-- Header -->
            <EventCreateHeader
                :selected-project="selectedProject"
                :is-loading="isLoading"
                :has-error="hasError"
            />

            <!-- Form -->
            <EventCreateForm
                :project-id="projectId"
                :is-loading="isLoading"
                :has-error="hasError"
                :projects="projects"
                :selected-project="selectedProject"
                :event-data="event"
            />
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import EventCreateHeader from './Partials/EventCreateHeader.vue'
import EventCreateForm from './Partials/EventCreateForm.vue'
import { useEventCreateManager } from '@/composables/events/create/useEventCreateManager'
import type { EventCreateSkeletonData, EventCreateData } from '@/types/events/create'

interface Props {
    projectId?: number | null
    skeletonData: EventCreateSkeletonData
    data?: EventCreateData
}

const _props = defineProps<Props>()

// State management
const {
    event,
    projects,
    selectedProject,
    isLoading,
    hasError
} = useEventCreateManager(_props.projectId, _props.skeletonData, _props.data)
</script>
