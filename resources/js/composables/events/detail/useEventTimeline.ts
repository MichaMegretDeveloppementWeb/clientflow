import { computed, unref, type MaybeRef } from 'vue'
import type { EventDTO } from '@/types/models'
import type { TimelineItem } from '@/types/events/detail'
import { useEventUtils } from './useEventUtils'

export function useEventTimeline(event: MaybeRef<EventDTO | null>) {
    const { getDaysOverdue } = useEventUtils(event)

    const getTimelineItems = computed((): TimelineItem[] => {
        const eventValue = unref(event)
        if (!eventValue) return []

        const items: TimelineItem[] = []

        // 1. Création de l'événement
        items.push({
            key: 'created',
            date: eventValue.created_date,
            title: 'Événement créé',
            icon: 'plus',
            bgClass: 'bg-gray-100',
            iconClass: 'text-gray-600'
        })

        // 2. Date d'exécution (pour les étapes)
        if (eventValue.event_type === 'step' && eventValue.execution_date) {
            items.push({
                key: 'execution',
                date: eventValue.execution_date,
                title: 'Date d\'exécution',
                icon: 'calendar',
                bgClass: eventValue.is_overdue ? 'bg-red-100' : 'bg-blue-100',
                iconClass: eventValue.is_overdue ? 'text-red-600' : 'text-blue-600',
                delay: (eventValue.is_overdue && eventValue.status !== 'cancelled') 
                    ? `${getDaysOverdue(eventValue.execution_date)}j de retard` 
                    : null
            })
        }

        // 3. Date d'envoi (pour les facturations)
        if (eventValue.event_type === 'billing' && eventValue.send_date) {
            items.push({
                key: 'send',
                date: eventValue.send_date,
                title: 'Date d\'envoi prévue',
                icon: 'send',
                bgClass: eventValue.is_overdue ? 'bg-red-100' : 'bg-blue-100',
                iconClass: eventValue.is_overdue ? 'text-red-600' : 'text-blue-600',
                delay: (eventValue.is_overdue && eventValue.status !== 'cancelled') 
                    ? `${getDaysOverdue(eventValue.send_date)}j de retard` 
                    : null
            })
        }

        // 4. Échéance de paiement (pour les facturations)
        if (eventValue.event_type === 'billing' && eventValue.payment_due_date) {
            items.push({
                key: 'payment_due',
                date: eventValue.payment_due_date,
                title: 'Échéance de paiement',
                icon: 'clock',
                bgClass: eventValue.is_payment_overdue ? 'bg-red-100' : 'bg-amber-100',
                iconClass: eventValue.is_payment_overdue ? 'text-red-600' : 'text-amber-600',
                delay: (eventValue.is_payment_overdue && eventValue.status !== 'cancelled') 
                    ? `${getDaysOverdue(eventValue.payment_due_date)}j de retard` 
                    : null
            })
        }

        // 5. Complétion (fait/envoyé)
        if (eventValue.completed_at) {
            items.push({
                key: 'completed',
                date: eventValue.completed_at,
                title: eventValue.event_type === 'step' ? 'Fait' : 'Envoyé',
                icon: 'check',
                bgClass: 'bg-emerald-100',
                iconClass: 'text-emerald-600'
            })
        }

        // 6. Paiement (pour les facturations payées)
        if (eventValue.event_type === 'billing' && eventValue.payment_status === 'paid' && eventValue.paid_at) {
            items.push({
                key: 'paid',
                date: eventValue.paid_at,
                title: 'Payé',
                icon: 'banknote',
                bgClass: 'bg-emerald-100',
                iconClass: 'text-emerald-600'
            })
        }

        // 7. Annulation (si applicable)
        if (eventValue.status === 'cancelled') {
            items.push({
                key: 'cancelled',
                date: eventValue.updated_at,
                title: 'Événement annulé',
                icon: 'x',
                bgClass: 'bg-red-100',
                iconClass: 'text-red-600'
            })
        }

        // Trier avec la création toujours en premier, puis par ordre chronologique
        return items.sort((a, b) => {
            // La création est toujours en premier
            if (a.key === 'created') return -1
            if (b.key === 'created') return 1

            const dateA = new Date(a.date)
            const dateB = new Date(b.date)

            // Si c'est le même jour, appliquer les règles de priorité logiques
            if (dateA.toDateString() === dateB.toDateString()) {
                const priorityOrder = {
                    'send': 1,
                    'execution': 1,
                    'completed': 2,
                    'payment_due': 3,
                    'paid': 4,
                    'cancelled': 5
                }

                const priorityA = priorityOrder[a.key as keyof typeof priorityOrder] || 999
                const priorityB = priorityOrder[b.key as keyof typeof priorityOrder] || 999

                if (priorityA !== priorityB) {
                    return priorityA - priorityB
                }
            }

            // Pour tous les autres cas, trier par date chronologique
            return dateA.getTime() - dateB.getTime()
        })
    })

    return {
        getTimelineItems
    }
}