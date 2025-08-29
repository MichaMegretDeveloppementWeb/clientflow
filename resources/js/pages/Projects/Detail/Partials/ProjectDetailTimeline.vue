<template>
    <!-- Events Timeline - Design moderne -->
    <Card class="border-0 bg-white shadow-sm ring-1 ring-gray-200/60">
        <CardHeader class="border-b border-gray-100 pb-6">
            <div class="flex items-center justify-between">
                <CardTitle class="flex items-center gap-3 text-xl font-semibold text-gray-900">
                    <div class="rounded-lg bg-gray-50 p-2">
                        <Icon name="activity" class="h-5 w-5 text-gray-600" />
                    </div>
                    Timeline des événements
                </CardTitle>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-gray-50 text-gray-700 ring-1 ring-gray-300/50 min-w-max">
                        {{ timelineEvents.length }} événement{{ (timelineEvents.length) > 1 ? 's' : '' }}
                    </span>
                </div>
            </div>
        </CardHeader>
        <CardContent class="p-6">
            <div v-if="timelineEvents.length === 0" class="p-12 text-center">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-gray-50 to-gray-100">
                    <Icon name="calendar" class="h-8 w-8 text-gray-400" />
                </div>
                <h3 class="mb-2 text-lg font-semibold text-gray-900">Aucun événement</h3>
                <p class="mb-6 text-sm text-gray-500 max-w-sm mx-auto">Ce projet n'a pas encore d'événement. Commencez par créer une étape ou une facturation.</p>
                <Button as-child class="bg-blue-600 hover:bg-blue-700 text-white shadow-sm">
                    <Link :href="route('events.create', { project_id: project.id })">
                        <Icon name="plus" class="mr-2 h-4 w-4" />
                        Créer le premier événement
                    </Link>
                </Button>
            </div>
            <div v-else class="relative pl-10">
                <div class="space-y-0">

                    <!-- Événements existants -->
                    <div
                        v-for="(event, index) in timelineEvents"
                        :key="event.id"
                        class="relative"
                    >
                        <!-- Ligne verticale entre les événements (pas pour le dernier) -->
                        <div
                            v-if="index < timelineEvents.length"
                            class="absolute left-[-22px] top-12 h-[calc(100%+24px)] w-0.5 bg-gray-300 z-0"
                        ></div>

                        <!-- Cercle de la timeline (plus gros et mieux positionné) -->
                        <div
                            class="absolute left-[-33px] top-8 h-6 w-6 rounded-full border-4 border-white z-10"
                            :class="{
                                'bg-blue-500': event.event_type === 'step' && event.status !== 'cancelled',
                                'bg-emerald-500': event.event_type === 'billing' && event.status !== 'cancelled',
                                'bg-gray-400': event.status === 'cancelled'
                            }"
                        ></div>

                        <!-- Contenu de l'événement avec design client aligné -->
                        <Link
                            :href="route('events.show', event.id)"
                            class="group cursor-pointer transition-all duration-200 relative block rounded-lg"
                            :class="{
                                'opacity-60': event.status === 'cancelled'
                            }"
                        >

                            <div class="flex flex-col gap-4 p-4 py-6 rounded-sm lg:flex-row hover:bg-gray-100 lg:items-center lg:justify-between relative">
                                <!-- Contenu principal -->
                                <div class="flex-1 space-y-2.5">
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                        <div class="space-y-1.5">
                                            <!-- Nom de l'événement -->
                                            <div class="flex items-center gap-4">
                                                <h3 class="text-base font-medium transition-colors group-hover:text-emerald-700 text-slate-900"
                                                    :class="{
                                                        'text-gray-500 line-through': event.status === 'cancelled'
                                                    }">
                                                    {{ event.name }}
                                                </h3>

                                                <!-- Badge de type -->
                                                <span
                                                    class="inline-flex w-fit items-center rounded-full text-sm font-medium gap-1"
                                                    :class="getEventTypeClasses(event.event_type)"
                                                >
                                                    <Icon v-if="event.event_type === 'billing'" name="banknote" class="h-3.5 w-3.5 text-emerald-600" />
                                                    <Icon v-else name="flag" class="h-3.5 w-3.5 text-blue-600" />
                                                    {{ getEventTypeLabel(event.event_type) }}
                                                </span>

                                                <!-- Badge de statut -->
                                                <span
                                                    class="inline-flex w-fit items-center rounded-full px-2.5 py-0.5 text-[0.8em] font-medium ring-1 ring-inset"
                                                    :class="getStatusClass(event.status)"
                                                >
                                                    {{ getEnhancedStatusLabel(event) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Date d'éxécution avec indication de retard -->
                                    <div class="flex flex-col gap-1.5 text-sm text-slate-600 sm:flex-row sm:items-center sm:gap-5">
                                        <span
                                            v-if="getEventDate(event)"
                                            class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-700"
                                        >
                                            <Icon name="calendar" class="h-3 w-3" />
                                            {{ formatDate(getEventDate(event)) }}
                                        </span>

                                        <!-- Événement en retard -->
                                        <span
                                            v-if="shouldShowOverdueBadge(event)"
                                            class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-medium text-red-700 bg-red-50 rounded-md ring-1 ring-red-200 w-max"
                                        >
                                            <Icon name="clock" class="h-3 w-3" />
                                            {{ event.event_type === 'billing' ? "Envoi" : "Exécution" }} en retard ({{ getOverdueDays(event) }}j)
                                        </span>
                                    </div>

                                    <!-- Informations secondaires -->
                                    <div v-if="event.description && event.status !== 'cancelled'" class="flex flex-col gap-1.5 text-sm text-slate-600 sm:flex-row sm:items-center sm:gap-5">
                                        <span class="flex items-center gap-1.5">
                                            <Icon name="file-text" class="h-3.5 w-3.5 text-slate-400" />
                                            {{ truncateText(event.description, 80) }}
                                        </span>
                                        <span v-if="event.type" class="flex items-center gap-1.5">
                                            <Icon name="tag" class="h-3.5 w-3.5 text-slate-400" />
                                            {{ event.type }}
                                        </span>
                                    </div>

                                    <!-- Métriques business -->
                                    <div class="flex flex-wrap items-center gap-3">
                                        <!-- Montant (pour les facturations) -->
                                        <span
                                            v-if="event.event_type === 'billing' && event.amount"
                                            class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-700"
                                        >
                                            <Icon name="banknote" class="h-3 w-3" />
                                            {{ formatCurrency(event.amount) }}
                                        </span>

                                        <!-- Statut de paiement (pour les facturations) -->
                                        <span
                                            v-if="event.event_type === 'billing' && event.payment_status"
                                            class="inline-flex items-center gap-1.5 text-xs font-medium"
                                            :class="getPaymentStatusClasses(event.payment_status)"
                                        >
                                            <Icon name="credit-card" class="h-3 w-3" />
                                            {{ event.payment_status === 'paid' ? 'Payé' : 'À payer' }}
                                        </span>

                                        <!-- Date échéance paiement -->
                                        <span
                                            v-if="event.event_type === 'billing' && (event.paid_at || event.payment_due_date)"
                                            class="inline-flex items-center gap-1.5 text-xs font-medium"
                                            :class="shouldShowPaymentOverdueBadge(event) ? 'text-red-700' : 'text-blue-600'"
                                        >
                                            <Icon name="calendar" class="h-3 w-3" />
                                            {{ formatDate(event.paid_at || event.payment_due_date) }}
                                        </span>
                                    </div>

                                    <!-- Indicateurs d'alerte -->
                                    <div v-if="shouldShowPaymentOverdueBadge(event) || event.status === 'cancelled'" class="flex flex-wrap items-center gap-2">
                                        <!-- Paiement en retard -->
                                        <span
                                            v-if="shouldShowPaymentOverdueBadge(event)"
                                            class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-medium text-red-700 bg-red-50 rounded-md ring-1 ring-red-200"
                                        >
                                            <Icon name="credit-card" class="h-3 w-3" />
                                            Paiement en retard (+{{ getPaymentOverdueDays(event) }}j)
                                        </span>

                                        <!-- Message d'annulation -->
                                        <span
                                            v-if="event.status === 'cancelled'"
                                            class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-medium text-gray-700 bg-gray-50 rounded-md ring-1 ring-gray-200"
                                        >
                                            <Icon name="x-circle" class="h-3 w-3" />
                                            Événement annulé
                                        </span>
                                    </div>
                                </div>

                            </div>
                        </Link>
                    </div>


                    <!-- Événement de début du projet (toujours en dernier) -->
                    <div class="relative">

                        <!-- Cercle de la timeline pour le début -->
                        <div class="absolute left-[-33px] top-8 h-6 w-6 rounded-full border-4 border-white z-10 bg-blue-500"></div>

                        <!-- Contenu de l'événement de début -->
                        <div class="py-4 px-4">
                            <div class="space-y-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="min-w-0 flex-1 space-y-3">
                                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                            <div class="flex items-center gap-2">
                                                <h4 class="text-lg font-semibold text-gray-900">Début du projet</h4>
                                            </div>
                                        </div>

                                        <!-- Informations contextuelles -->
                                        <div class="flex flex-col gap-2 text-sm text-gray-600 sm:flex-row sm:items-center sm:gap-6">
                                            <!-- Date de création -->
                                            <span class="flex items-center gap-1.5 font-medium">
                                                <Icon name="calendar" class="h-4 w-4 text-gray-400" />
                                                {{ formatDate(project.start_date || project.created_at) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </CardContent>
    </Card>
</template>

<script setup lang="ts">
import Icon from '@/components/Icon.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import { toRef } from 'vue'
import type { ProjectDetailData } from '@/types/projects/detail'
import type { Event } from '@/types/projects/events'
import { useProjectDetailTimeline } from '@/composables/projects/detail/useProjectDetailTimeline'

interface Props {
    project: ProjectDetailData['project']
    events: Event[]
}

const props = defineProps<Props>()

// Utiliser le composable pour la logique de la timeline
const {
    timelineEvents,
    getEventTypeLabel,
    getEnhancedStatusLabel,
    getStatusClass,
    getEventTypeClasses,
    getPaymentStatusClasses,
    shouldShowOverdueBadge,
    shouldShowPaymentOverdueBadge,
    getOverdueDays,
    getPaymentOverdueDays,
    formatDate,
    formatCurrency,
    truncateText,
    getEventDate
} = useProjectDetailTimeline(toRef(props, 'events'))
</script>
