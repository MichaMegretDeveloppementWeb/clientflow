<template>
    <div
        class="group cursor-pointer transition-all duration-200 hover:bg-slate-50/40 hover:shadow-sm relative min-h-[140px]"
        @click="handleEventClick"
    >
        <!-- Trait fin gauche élégant -->
        <div class="absolute left-0 top-0 h-full w-px bg-transparent group-hover:bg-slate-300/60 transition-all duration-200"></div>

        <div class="flex flex-col gap-4 p-6 py-8 lg:flex-row lg:items-center lg:justify-between relative">
            <!-- Contenu principal -->
            <div class="flex-1 space-y-2.5">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div class="space-y-1.5">
                        <!-- Nom de l'événement -->
                        <div class="flex items-center gap-4">
                            <h3 class="text-base font-medium transition-colors group-hover:text-emerald-700 text-slate-900">
                                {{ event.name }}
                            </h3>
                            <!-- Badge de type -->
                            <span
                                class="inline-flex w-fit items-center rounded-full text-sm font-medium gap-1"
                                :class="getEventTypeClasses(event.event_type)"
                            >
                                <Icon v-if="event.event_type === 'billing'" name="banknote" class="h-3.5 w-3.5 text-emerald-600" />
                                <Icon v-else name="flag" class="h-3.5 w-3.5 text-blue-600" />
                                {{ event.event_type_label }}
                            </span>
                            <!-- Badge de statut -->
                            <span
                                class="inline-flex w-fit items-center rounded-full px-2.5 py-0.5 text-[0.8em] font-medium ring-1 ring-inset"
                                :class="getStatusClasses(event.status)"
                            >
                                {{ event.status_label }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-1.5 text-sm text-slate-600 sm:flex-row sm:items-center sm:gap-5 mt-3">
                    <!-- Date d'éxécution -->
                    <span
                        v-if="event.execution_date? event.execution_date : event.send_date"
                        class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-700"
                    >
                        <Icon name="calendar" class="h-3 w-3" />
                        {{ formatDate(event.execution_date? event.execution_date : event.send_date) }}
                    </span>
                    <!-- Événement en retard -->
                    <span
                        v-if="event.is_overdue"
                        class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-medium text-red-700 bg-red-50 rounded-md ring-1 ring-red-200"
                    >
                        <Icon name="clock" class="h-3 w-3" />
                        {{ event.event_type === 'billing' ? "Envoi" : "Exécution" }} en retard
                    </span>
                </div>

                <!-- Informations secondaires -->
                <div class="flex flex-col gap-1.5 text-sm text-slate-600 sm:flex-row sm:items-center sm:gap-5 mt-3">
                    <span v-if="event.project?.name" class="flex items-center gap-1.5">
                        <Icon name="folder" class="h-3.5 w-3.5 text-slate-400" />
                        {{ event.project.name }}
                    </span>
                    <span v-if="event.project?.client?.name" class="flex items-center gap-1.5">
                        <Icon name="user" class="h-3.5 w-3.5 text-slate-400" />
                        {{ event.project.client.name }}
                    </span>
                    <span v-if="event.description" class="flex items-center gap-1.5">
                        <Icon name="file-text" class="h-3.5 w-3.5 text-slate-400" />
                        {{ truncateText(event.description, 50) }}
                    </span>
                </div>

                <!-- Métriques business -->
                <div class="flex flex-wrap items-center gap-3 mt-3">
                    <!-- Montant (pour les facturations) -->
                    <span
                        v-if="event.event_type === 'billing' && event.amount"
                        class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-700"
                    >
                        <Icon name="banknote" class="h-3 w-3" />
                        {{ formatAmount(event.amount) }}
                    </span>

                    <!-- Statut de paiement (pour les facturations) -->
                    <span
                        v-if="event.event_type === 'billing'"
                        class="inline-flex items-center gap-1.5 text-xs font-medium"
                        :class="getPaymentStatusClasses(event.payment_status)"
                    >
                        <Icon name="credit-card" class="h-3 w-3" />
                        {{ getPaymentStatusLabel(event.payment_status) }}
                    </span>
                    <!--Date échéance-->
                    <span
                        v-if="event.event_type === 'billing'"
                        class="inline-flex items-center gap-1.5 text-xs font-medium"
                        :class="event.is_payment_overdue ? 'text-orange-700' : 'text-blue-600'"
                    >
                        <Icon name="calendar" class="h-3 w-3" />
                        {{ formatDate(event.paid_at ? event.paid_at : event.payment_due_date) }}
                    </span>
                </div>

                <!-- Indicateurs d'alerte -->
                <div v-if="hasAnyAlerts(event)" class="flex flex-wrap items-center gap-2 mt-2">
                    <!-- Paiement en retard -->
                    <span
                        v-if="event.is_payment_overdue"
                        class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-medium text-red-700 bg-red-50 rounded-md ring-1 ring-red-200"
                    >
                        <Icon name="credit-card" class="h-3 w-3" />
                        Paiement en retard
                    </span>
                </div>
            </div>

            <!-- Menu contextuel discret -->
            <div class="transition-opacity absolute right-3 top-3">
                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <button
                            class="inline-flex items-center justify-center p-1 rounded-md text-slate-600 bg-slate-200 hover:bg-slate-300 transition-colors cursor-pointer"
                            @click.stop
                            title="Actions"
                        >
                            <Icon name="more-horizontal" class="h-3.5 w-3.5" />
                        </button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end" class="w-40">
                        <DropdownMenuItem @click.stop="handleView">
                            <Icon name="external-link" class="mr-2 h-3.5 w-3.5" />
                            Voir l'événement
                        </DropdownMenuItem>
                        <DropdownMenuItem @click.stop="handleEdit">
                            <Icon name="edit" class="mr-2 h-3.5 w-3.5" />
                            Modifier
                        </DropdownMenuItem>
                        <DropdownMenuSeparator />
                        <DropdownMenuItem
                            class="text-red-600 focus:text-red-600 focus:bg-red-50"
                            @click.stop="handleDelete"
                        >
                            <Icon name="trash-2" class="mr-2 h-3.5 w-3.5" />
                            Supprimer
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import Icon from '@/components/Icon.vue'
import type { EventDTO } from '@/types/models'

/**
 * Composant de carte événement réutilisable
 * Responsabilités :
 * - Affichage des informations de l'événement
 * - Interactions de navigation et actions
 * - Menu contextuel avec actions
 * - Support dual type step/billing
 */

interface Props {
    event: EventDTO
}

const props = defineProps<Props>()

const emit = defineEmits<{
    'event-click': [event: EventDTO]
    'view': [event: EventDTO]
    'edit': [event: EventDTO]
    'delete': [eventId: number]
}>()

// Gestion du clic sur l'événement
const handleEventClick = () => {
    emit('event-click', props.event)
}

// Actions du menu
const handleView = () => {
    emit('view', props.event)
}

const handleEdit = () => {
    emit('edit', props.event)
}

const handleDelete = () => {
    emit('delete', props.event.id)
}

// Utilitaires de formatage
const truncateText = (text: string, maxLength: number): string => {
    return text.length > maxLength ? `${text.slice(0, maxLength)}...` : text
}

// Logique des statuts
const getStatusClasses = (status: string): string => {
    const classes: Record<string, string> = {
        'todo': 'bg-orange-50 text-orange-700 ring-orange-600/20',
        'done': 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'to_send': 'bg-orange-50 text-orange-700 ring-orange-600/20',
        'sent': 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'cancelled': 'bg-red-50 text-red-700 ring-red-600/20'
    }
    return classes[status] || 'bg-gray-50 text-gray-700 ring-gray-600/20'
}

const getEventTypeClasses = (eventType: string): string => {
    const classes: Record<string, string> = {
        'step': 'text-blue-700',
        'billing': 'text-emerald-700'
    }
    return classes[eventType] || 'text-gray-700'
}

const getPaymentStatusClasses = (paymentStatus: string): string => {
    if (paymentStatus === 'paid') {
        return 'text-emerald-700'
    } else {
        // Tous les autres cas (pending, null, undefined) = "À payer" = orange
        return 'text-orange-700'
    }
}

const hasAnyAlerts = (event: EventDTO): boolean => {
    return event.is_overdue || event.is_payment_overdue
}

// Formatage des dates au format d/m/Y
const formatDate = (dateString: string): string => {
    if (!dateString) return ''
    const date = new Date(dateString)
    return date.toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'numeric',
        year: 'numeric'
    })
}

// Formatage des montants avec k/M
const formatAmount = (amount: number): string => {
    if (!amount) return '0€'

    if (amount >= 1000000) {
        return `${(amount / 1000000).toFixed(1).replace('.0', '')}M€`
    } else if (amount >= 1000) {
        return `${(amount / 1000).toFixed(1).replace('.0', '')}k€`
    } else {
        return `${amount}€`
    }
}

// Labels de paiement avec "À payer" par défaut
const getPaymentStatusLabel = (paymentStatus: string): string => {
    if (paymentStatus === 'paid') {
        return 'Payé'
    } else {
        return 'À payer'
    }
}

</script>
