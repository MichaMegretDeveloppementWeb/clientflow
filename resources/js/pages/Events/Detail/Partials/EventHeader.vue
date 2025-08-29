<template>
    <div class="border-b border-gray-100 pb-6">
        <!-- Breadcrumb -->
        <div class="mb-4 flex items-center gap-3 text-sm">
            <Link :href="route('dashboard')" class="flex items-center gap-2 text-gray-500 hover:text-gray-700 transition-colors">
                <Icon name="home" class="h-4 w-4" />
                <span>Tableau de bord</span>
            </Link>
            <div class="h-4 w-px bg-gray-300"></div>
            <Link :href="route('events.index')" class="text-gray-500 transition-colors hover:text-gray-700">
                Événements
            </Link>
        </div>

        <!-- Header avec Title et Meta -->
        <div class="flex items-start gap-4 pb-6">
            <div class="rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 p-3 shadow-sm">
                <Icon name="calendar" class="h-6 w-6 text-white" />
            </div>
            <div class="flex-1 min-w-0">
                <!-- Skeleton pour le titre -->
                <div v-if="isLoading" class="space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="h-8 bg-gray-200 rounded w-48 animate-pulse"></div>
                        <div class="h-6 bg-gray-200 rounded-full w-20 animate-pulse"></div>
                        <div class="h-6 bg-gray-200 rounded-full w-16 animate-pulse"></div>
                    </div>
                </div>
                <!-- Données erreur -->
                <div v-else-if="hasError" class="flex flex-wrap items-center gap-3">
                    <h1 class="text-2xl font-bold text-gray-900">&Eacute;venement</h1>
                </div>
                <!-- Données réelles -->
                <div v-else class="flex flex-wrap items-center gap-3">
                    <h1 class="text-2xl font-bold text-gray-900">{{ event?.name }}</h1>
                    <span
                        class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-sm font-medium ring-1 ring-inset"
                        :class="getStatusClass(event?.status)"
                    >
                        <div class="h-2 w-2 rounded-full" :class="getStatusDotClass(event?.status)"></div>
                        {{ event?.status_label }}
                    </span>
                    <!-- Badge de paiement pour les facturations -->
                    <span
                        v-if="event?.event_type === 'billing' && event?.status !== 'cancelled'"
                        class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-sm font-medium ring-1 ring-inset"
                        :class="getPaymentBadgeClass"
                    >
                        <div class="h-2 w-2 rounded-full" :class="getPaymentBadgeDotClass"></div>
                        {{ getPaymentBadgeText }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Meta informations -->
        <div v-if="!hasError" class="flex flex-wrap items-center gap-4 text-sm text-gray-600 py-4 border-y border-gray-100">
            <!-- Skeleton pour les meta -->
            <div v-if="isLoading" class="flex gap-4">
                <div class="flex items-center gap-1.5">
                    <div class="h-4 w-4 bg-gray-200 rounded animate-pulse"></div>
                    <div class="h-4 bg-gray-200 rounded w-20 animate-pulse"></div>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="h-4 w-4 bg-gray-200 rounded animate-pulse"></div>
                    <div class="h-4 bg-gray-200 rounded w-24 animate-pulse"></div>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="h-4 w-4 bg-gray-200 rounded animate-pulse"></div>
                    <div class="h-4 bg-gray-200 rounded w-20 animate-pulse"></div>
                </div>
            </div>
            <!-- Données réelles -->
            <template v-else>
                <span class="flex items-center gap-1.5">
                    <Icon
                        :name="event?.event_type === 'step' ? 'flag' : 'banknote'"
                        class="h-4 w-4"
                        :class="{
                            'text-blue-500': event?.event_type === 'step',
                            'text-emerald-500': event?.event_type === 'billing'
                        }"
                    />
                    {{ event?.event_type_label }}
                </span>
                <span class="flex items-center gap-1.5">
                    <Icon name="folder" class="h-4 w-4 text-gray-400" />
                    <Link
                        :href="route('projects.show', event?.project?.id)"
                        class="font-medium hover:text-blue-600"
                    >
                        {{ event?.project?.name }}
                    </Link>
                </span>
                <span class="flex items-center gap-1.5">
                    <Icon name="user" class="h-4 w-4 text-gray-400" />
                    <Link
                        :href="route('clients.show', event?.project?.client?.id)"
                        class="font-medium hover:text-blue-600"
                    >
                        {{ event?.project?.client?.name }}
                    </Link>
                </span>
            </template>
        </div>

        <!-- Actions Bar -->
        <div v-if="!hasError" class="flex flex-col gap-3 py-4 border-t border-gray-100 sm:flex-row sm:items-center sm:justify-between">
            <!-- Skeleton pour les actions -->
            <div v-if="isLoading" class="flex justify-between">
                <div class="h-9 bg-gray-200 rounded w-20 animate-pulse"></div>
                <div class="flex gap-3">
                    <div class="h-9 bg-gray-200 rounded w-24 animate-pulse"></div>
                    <div class="h-9 bg-gray-200 rounded w-24 animate-pulse"></div>
                </div>
            </div>
            <!-- Actions réelles -->
            <template v-else>
                <!-- Actions secondaires -->
                <div class="flex items-center gap-3">
                    <Button size="sm" variant="outline" as-child>
                        <Link :href="route('events.edit', event?.id)">
                            <Icon name="edit" class="mr-2 h-4 w-4" />
                            Modifier
                        </Link>
                    </Button>
                </div>

                <!-- Actions de statut -->
                <div v-if="event?.status !== 'cancelled'" class="flex items-center gap-3">
                    <Button
                        v-if="canChangeStatus"
                        @click="$emit('toggle-status')"
                        size="sm"
                        class="bg-emerald-600 text-white hover:bg-emerald-700"
                    >
                        <Icon name="check" class="mr-2 h-4 w-4" />
                        {{ event?.event_type === 'billing' ? 'Marquer envoyé' : 'Marquer fait' }}
                    </Button>

                    <Button
                        v-if="event?.event_type === 'billing' && event?.payment_status === 'pending'"
                        @click="$emit('mark-as-paid')"
                        size="sm"
                        variant="outline"
                        class="border-emerald-300 text-emerald-700 hover:bg-emerald-50"
                    >
                        <Icon name="check-circle" class="mr-2 h-4 w-4" />
                        Marquer payé
                    </Button>
                </div>

                <!-- Message pour les événements annulés -->
                <div v-else class="flex items-center gap-2 text-sm text-gray-500">
                    <Icon name="x-circle" class="h-4 w-4 text-red-500" />
                    <span>Cet événement a été annulé</span>
                </div>
            </template>
        </div>

    </div>
</template>

<script setup lang="ts">

import { computed, toRef } from 'vue'
import Icon from '@/components/Icon.vue'
import { Button } from '@/components/ui/button'
import { Link } from '@inertiajs/vue3'
import { useEventUtils } from '@/composables/events/detail/useEventUtils'

const props = defineProps({
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
        default: false
    }
})

defineEmits(['toggle-status', 'mark-as-paid'])

const {
    getStatusClass,
    getStatusDotClass,
    getPaymentBadgeText,
    getPaymentBadgeClass,
    getPaymentBadgeDotClass
} = useEventUtils(toRef(props, 'event'))

const canChangeStatus = computed(() => {
    return props.event && ['todo', 'to_send'].includes(props.event.status)
})
</script>
