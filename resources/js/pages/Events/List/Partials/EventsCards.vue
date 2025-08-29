<template>
    <Card class="border border-slate-200 bg-white shadow-sm p-0">
        <CardContent class="p-0">
            <div v-if="isLoading" class="divide-y divide-slate-100">
                <EventCardSkeleton
                    v-for="i in 5"
                    :key="i"
                />
            </div>

            <div v-else-if="events.length > 0" class="divide-y divide-slate-100">
                <EventCard
                    v-for="event in events"
                    :key="event.id"
                    :event="event"
                    @event-click="handleEventClick"
                    @view="handleView"
                    @edit="handleEdit"
                    @delete="handleDelete"
                />
            </div>

        </CardContent>
    </Card>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import { Card, CardContent } from '@/components/ui/card'
import { route } from 'ziggy-js'
import type { EventDTO } from '@/types/models'
import EventCardSkeleton from './EventCardSkeleton.vue'
import EventCard from './EventCard.vue'

interface Props {
    events: EventDTO[]
    isLoading: boolean
    hasActiveFilters: boolean
}

defineProps<Props>()

const emit = defineEmits<{
    'event-click': [event: EventDTO]
    'event-delete': [eventId: number]
}>()

// Handlers qui délèguent aux événements du parent
const handleEventClick = (event: EventDTO) => {
    emit('event-click', event)
}

const handleView = (event: EventDTO) => {
    router.visit(route('events.show', { event: event.id }))
}

const handleEdit = (event: EventDTO) => {
    router.visit(route('events.edit', { event: event.id }))
}

const handleDelete = (eventId: number) => {
    emit('event-delete', eventId)
}
</script>