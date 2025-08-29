<template>
    <Card class="border border-gray-200 bg-white shadow-sm">
        <CardHeader class="pb-4">
            <CardTitle class="flex items-center gap-2 text-base text-gray-900">
                <Icon name="activity" class="h-4 w-4 text-gray-600" />
                Statut & Timing
            </CardTitle>
        </CardHeader>
        <CardContent class="space-y-4">
            <!-- Skeleton -->
            <div v-if="isLoading" class="space-y-4">
                <!-- Dates skeleton -->
                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <div class="h-4 w-4 bg-gray-200 rounded animate-pulse"></div>
                        <div class="space-y-1 flex-1">
                            <div class="h-4 bg-gray-200 rounded w-32 animate-pulse"></div>
                            <div class="h-3 bg-gray-200 rounded w-24 animate-pulse"></div>
                        </div>
                    </div>
                </div>
                <!-- Alert skeleton -->
                <div class="h-12 bg-gray-200 rounded-lg animate-pulse"></div>
            </div>

            <!-- Données réelles -->
            <template v-else-if="event">
                <!-- Dates importantes -->
                <div class="space-y-3">
                    <!-- Pour les étapes : date d'exécution -->
                    <div v-if="event.event_type === 'step' && event.execution_date" class="flex items-center gap-2">
                        <Icon name="calendar" class="h-4 w-4 text-gray-400" />
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-700">Date d'exécution prévue</div>
                            <div class="text-sm text-gray-600">{{ formatDate(event.execution_date) }}</div>
                        </div>
                    </div>

                    <!-- Pour les facturations : date d'envoi -->
                    <div v-if="event.event_type === 'billing' && event.send_date" class="flex items-center gap-2">
                        <Icon name="send" class="h-4 w-4 text-gray-400" />
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-700">Date d'envoi prévue</div>
                            <div class="text-sm text-gray-600">{{ formatDate(event.send_date) }}</div>
                        </div>
                    </div>

                    <!-- Date de complétion -->
                    <div v-if="event.completed_at" class="flex items-center gap-2">
                        <Icon name="check" class="h-4 w-4 text-emerald-500" />
                        <div class="flex-1">
                            <div class="text-sm font-medium text-gray-700">{{ event.event_type === 'step' ? 'Fait le' : 'Envoyé le' }}</div>
                            <div class="text-sm text-gray-600">{{ formatDate(event.completed_at) }}</div>
                        </div>
                    </div>
                </div>

                <!-- Alerte d'annulation -->
                <div v-if="event.status === 'cancelled'" class="rounded-lg bg-gray-50 border border-gray-200 p-3">
                    <div class="flex items-center gap-2">
                        <Icon name="x-circle" class="h-4 w-4 text-gray-600" />
                        <span class="text-sm font-medium text-gray-800">
                            Événement annulé
                        </span>
                    </div>
                </div>

                <!-- Indicateurs de retard d'exécution/envoi -->
                <div v-else-if="event.is_overdue" class="rounded-lg bg-red-50 border border-red-200 p-3">
                    <div class="flex items-center gap-2">
                        <Icon name="alert-triangle" class="h-4 w-4 text-red-600" />
                        <span class="text-sm font-medium text-red-800">
                            {{ event.event_type === 'step' ? 'Exécution' : 'Envoi' }} en retard de {{ getDaysOverdue(getOverdueDateField()) }} jour{{ getDaysOverdue(getOverdueDateField()) > 1 ? 's' : '' }}
                        </span>
                    </div>
                </div>

                <!-- Indicateurs d'écart par rapport à la date prévue (pour les tâches terminées) -->
                <div v-else-if="event.completed_at && getCompletionDelay() !== 0" class="rounded-lg p-3" :class="getCompletionDelayClass()">
                    <div class="flex items-center gap-2">
                        <Icon :name="getCompletionDelayIcon()" class="h-4 w-4" />
                        <span class="text-sm font-medium">{{ getCompletionDelayText() }}</span>
                    </div>
                </div>

                <!-- Indicateurs d'échéances proches (pour les tâches en cours) -->
                <div v-else-if="getUrgencyLevel()" class="rounded-lg p-3" :class="getUrgencyClass()">
                    <div class="flex items-center gap-2">
                        <Icon :name="getUrgencyIcon()" class="h-4 w-4" />
                        <span class="text-sm font-medium">{{ getUrgencyText() }}</span>
                    </div>
                </div>
            </template>
        </CardContent>
    </Card>
</template>

<script setup>
import { computed, toRef } from 'vue'
import Icon from '@/components/Icon.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { useEventUtils } from '@/composables/events/detail/useEventUtils'

const props = defineProps({
    event: {
        type: Object,
        default: null
    },
    isLoading: {
        type: Boolean,
        default: false
    }
})

const { 
    formatDate, 
    getDaysOverdue, 
    getDaysUntil
} = useEventUtils(toRef(props, 'event'))

// Logique d'urgence et de retard (copiée de Show.vue original)
const getOverdueDateField = () => {
    if (props.event?.event_type === 'step') {
        return props.event.execution_date
    } else if (props.event?.event_type === 'billing') {
        return props.event.send_date
    }
    return null
}

const getUrgencyLevel = () => {
    if (!props.event || props.event.completed_at) return null

    const referenceDate = getOverdueDateField()
    if (!referenceDate) return null

    const daysUntil = getDaysUntil(referenceDate)

    if (daysUntil < 0) return 'overdue'
    if (daysUntil === 0) return 'today'
    if (daysUntil === 1) return 'high'
    if (daysUntil <= 3) return 'medium'

    return null
}

const getUrgencyClass = () => {
    const level = getUrgencyLevel()
    switch (level) {
        case 'overdue': return 'bg-red-50 border border-red-200'
        case 'high': return 'bg-red-50 border border-red-200'
        case 'today': return 'bg-amber-50 border border-amber-200'
        case 'medium': return 'bg-amber-50 border border-amber-200'
        default: return ''
    }
}

const getUrgencyIcon = () => {
    const level = getUrgencyLevel()
    switch (level) {
        case 'overdue': return 'alert-triangle'
        case 'high': return 'alert-triangle'
        case 'today': return 'clock'
        case 'medium': return 'clock'
        default: return 'info'
    }
}

const getUrgencyText = () => {
    const referenceDate = getOverdueDateField()
    if (!referenceDate) return ''

    const daysUntil = getDaysUntil(referenceDate)
    const type = props.event?.event_type === 'step' ? 'exécution' : 'envoi'
    const typeCapitalized = type.charAt(0).toUpperCase() + type.slice(1)

    if (daysUntil < 0) {
        const daysOverdue = Math.abs(daysUntil)
        return `${typeCapitalized} en retard de ${daysOverdue} jour${daysOverdue > 1 ? 's' : ''}`
    }

    if (daysUntil === 0) return `${typeCapitalized} prévue aujourd'hui`
    if (daysUntil === 1) return `${typeCapitalized} prévue demain`
    if (daysUntil <= 3) return `${typeCapitalized} prévue dans ${daysUntil} jours`

    return ''
}

// Fonctions pour l'écart par rapport à la date prévue (tâches terminées)
const getCompletionDelay = () => {
    if (!props.event?.completed_at) return 0

    const referenceDate = getOverdueDateField()
    if (!referenceDate) return 0

    const completedDate = new Date(props.event.completed_at)
    completedDate.setHours(0, 0, 0, 0)

    const plannedDate = new Date(referenceDate)
    plannedDate.setHours(0, 0, 0, 0)

    const diffTime = completedDate - plannedDate
    return Math.floor(diffTime / (1000 * 60 * 60 * 24))
}

const getCompletionDelayClass = () => {
    const delay = getCompletionDelay()
    if (delay > 0) {
        return 'bg-orange-50 border border-orange-200'
    } else {
        return 'bg-emerald-50 border border-emerald-200'
    }
}

const getCompletionDelayIcon = () => {
    const delay = getCompletionDelay()
    return delay > 0 ? 'clock' : 'check-circle'
}

const getCompletionDelayText = () => {
    const delay = getCompletionDelay()
    const absDelay = Math.abs(delay)
    const type = props.event?.event_type === 'step' ? 'Fait' : 'Envoyé'

    if (delay > 0) {
        return `${type} ${absDelay} jour${absDelay > 1 ? 's' : ''} après la date prévue`
    } else {
        return `${type} ${absDelay} jour${absDelay > 1 ? 's' : ''} avant la date prévue`
    }
}
</script>