<template>
    <div class="space-y-6">
        <!-- Page Header -->
        <EventHeader
            :event="event"
            :isLoading="isLoading"
            :hasError="hasError"
            @toggle-status="toggleStatus"
            @mark-as-paid="markAsPaid"
        />


        <!-- Section d'erreur stylée -->
        <div v-if="hasError" class="max-w-2xl mx-auto">
            <div class="rounded-lg border border-red-200 bg-red-50 p-6 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0">
                        <div class="rounded-full bg-red-100 p-2">
                            <Icon name="alert-circle" class="h-5 w-5 text-red-600" />
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-medium text-red-700 mb-2 mt-2">
                            Des erreurs se sont produites
                        </h3>
                        <div class="space-y-2 mt-6 mb-10">
                            <div v-for="(err, key) in error" :key="key" class="text-md text-red-800">
                                - {{ err }}
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end gap-3">
                            <Button
                                @click="$emit('retry')"
                                size="sm"
                                variant="outline"
                                class="border-red-300 text-red-700 hover:bg-red-100 hover:border-red-400"
                            >
                                <Icon name="refresh-cw" class="mr-2 h-4 w-4" />
                                Réessayer
                            </Button>
                            <Button
                                @click="goBack"
                                size="sm"
                                variant="ghost"
                                class="text-red-700 hover:bg-red-100"
                            >
                                <Icon name="arrow-left" class="mr-2 h-4 w-4" />
                                Retour
                            </Button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content avec Sidebar Layout -->
        <div v-else class="grid gap-6 lg:grid-cols-4">
            <!-- Sidebar - Informations critiques -->
            <div class="space-y-6 lg:col-span-1">
                <!-- Status & Timeline -->
                <EventStatusCard
                    :event="event"
                    :isLoading="isLoading"
                />

                <!-- Facturation - si applicable -->
                <EventBillingCard
                    :event="event"
                    :isLoading="isLoading"
                />
            </div>

            <!-- Main Content -->
            <div class="space-y-6 lg:col-span-3">
                <!-- Description et détails -->
                <EventDetailsCard
                    :event="event"
                    :isLoading="isLoading"
                />

                <!-- Timeline des dates (version étendue) -->
                <EventTimelineCard
                    :event="event"
                    :isLoading="isLoading"
                />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import EventHeader from './EventHeader.vue'
import EventStatusCard from './EventStatusCard.vue'
import EventBillingCard from './EventBillingCard.vue'
import EventDetailsCard from './EventDetailsCard.vue'
import EventTimelineCard from './EventTimelineCard.vue'
import Icon from '@/components/Icon.vue'
import { Button } from '@/components/ui/button'

// eslint-disable-next-line @typescript-eslint/no-unused-vars
const _props = defineProps({
    event: {
        type: Object,
        default: null
    },
    isLoading: {
        type: Boolean,
        default: false
    },
    hasError: {
        type: Boolean,
        default: false,
    },
    error: {
        type: Array,
        default: () => [],
    },
})

const emit = defineEmits(['toggle-status', 'mark-as-paid', 'retry'])

const toggleStatus = () => {
    emit('toggle-status')
}

const markAsPaid = () => {
    emit('mark-as-paid')
}

const goBack = () => {
    // Utiliser history.back() pour revenir à la page précédente
    window.history.back()
}
</script>
