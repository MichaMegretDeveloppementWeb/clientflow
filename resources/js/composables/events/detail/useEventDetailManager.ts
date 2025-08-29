import { reactive, computed, onMounted } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import type { EventDetailData, EventDetailState } from '@/types/events/detail'

export function useEventDetailManager(eventId: number, skeletonData: EventDetailData) {
    // État réactif
    const state = reactive<EventDetailState>({
        isLoading: true,
        data: skeletonData,
        error: null
    })

    // Computed
    const event = computed(() => state.data?.event ?? null)
    const isLoading = computed(() => state.isLoading)
    const error = computed(() => state.error)
    const hasError = computed(() => !!state.error)

    // Récupération des données
    const fetchData = async () => {
        router.reload({
            only: ['data'],
            preserveUrl: true,
            onStart: () => { state.isLoading = true },
            onFinish: () => {
                const pageData = usePage().props.data as any

                if (pageData) {
                    state.data = pageData
                    state.error = null
                } else {
                    state.error = { general: 'Erreur lors du chargement des données' }
                }

                state.isLoading = false
            },
            onError: (errors) => {
                state.error = errors
                state.isLoading = false
            }
        })
    }

    // Actions de statut
    const toggleStatus = () => {
        if (!event.value) return

        const newStatus = event.value.event_type === 'billing' ? 'sent' : 'done'
        router.patch(route('events.updateStatus', event.value.id), {
            status: newStatus
        }, {
            onSuccess: () => {
                // Recharger les données après succès
                fetchData()
            }
        })
    }

    const markAsPaid = () => {
        if (!event.value) return

        // Utiliser la nouvelle route dédiée au changement de statut de paiement
        router.patch(route('events.updatePaymentStatus', event.value.id), {
            payment_status: 'paid'
        }, {
            onSuccess: () => {
                fetchData()
            },
            onError: (errors) => {
                console.error('Erreur lors de la mise à jour du paiement:', errors)
            }
        })
    }

    // Helpers computed
    const canChangeStatus = computed(() => {
        return event.value && ['todo', 'to_send'].includes(event.value.status)
    })

    // Auto-fetch au montage
    onMounted(() => {
        fetchData()
    })

    return {
        // State
        state,
        event,
        isLoading,
        error,
        hasError,

        // Actions
        fetchData,
        toggleStatus,
        markAsPaid,

        // Computed
        canChangeStatus
    }
}
