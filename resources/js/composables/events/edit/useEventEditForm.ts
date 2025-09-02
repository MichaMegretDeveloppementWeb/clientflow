import { computed, ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import type { EventEditFormData, EventEditFormValidation } from '@/types/events/edit'

export function useEventEditForm(eventData: EventEditFormData | null) {
    // Variables pour les messages d'erreur personnalisés
    const createdAtValidationError = ref<string>('')
    const executionDateValidationError = ref<string>('')
    const sendDateValidationError = ref<string>('')
    const paymentDueDateValidationError = ref<string>('')
    const paidAtValidationError = ref<string>('')
    const completedAtValidationError = ref<string>('')


    // Fonction pour formater une date pour l'input
    function formatForInput(date: string | null | undefined): string {
        if (!date) return ''
        const d = new Date(date)
        const year = d.getFullYear()
        const month = String(d.getMonth() + 1).padStart(2, '0')
        const day = String(d.getDate()).padStart(2, '0')
        return `${year}-${month}-${day}`
    }

    // Fonction pour obtenir la date actuelle au format date (YYYY-MM-DD)
    function getCurrentDate(): string {
        const now = new Date()
        const year = now.getFullYear()
        const month = String(now.getMonth() + 1).padStart(2, '0')
        const day = String(now.getDate()).padStart(2, '0')
        return `${year}-${month}-${day}`
    }

    // Initialisation du formulaire
    const form = useForm({
        project_id: eventData?.project_id || eventData?.project?.id || null,
        name: eventData?.name || '',
        description: eventData?.description || '',
        type: eventData?.type || '',
        event_type: eventData?.event_type || 'step',
        status: eventData?.status || 'todo',
        amount: eventData?.amount || '',
        payment_status: eventData?.payment_status || 'pending',
        paid_at: formatForInput(eventData?.paid_at),
        created_date: formatForInput(eventData?.created_date),
        execution_date: formatForInput(eventData?.execution_date),
        send_date: formatForInput(eventData?.send_date),
        payment_due_date: formatForInput(eventData?.payment_due_date),
        completed_at: formatForInput(eventData?.completed_at),
    })


    // Validation de created_date (toujours en computed car elle ne dépend que de ses propres données)
    const isCreatedAtValid = computed(() => {
        if (!form.created_date || !eventData?.project.start_date) {
            createdAtValidationError.value = ''
            return true
        }

        const createdAt = new Date(form.created_date)
        const projectStartDate = new Date(eventData.project.start_date)

        if (createdAt < projectStartDate) {
            createdAtValidationError.value = 'La date de création de l\'événement ne peut pas être antérieure au début du projet'
            return false
        }

        createdAtValidationError.value = ''
        return true
    })

    // Computed properties simplifiées basées sur les refs des erreurs
    const isExecutionDateValid = computed(() => !executionDateValidationError.value)
    const isSendDateValid = computed(() => !sendDateValidationError.value)
    const isPaymentDueDateValid = computed(() => !paymentDueDateValidationError.value)
    const isPaidAtValid = computed(() => !paidAtValidationError.value)
    const isCompletedAtValid = computed(() => !completedAtValidationError.value)

    // Le type d'événement ne peut plus être modifié après création
    // Cette logique n'est plus nécessaire

    // Auto-remplissage de la date de paiement quand on passe à 'paid'
    // On ne supprime plus la date de paiement quand on passe à 'pending'
    // (gestion côté backend : si pending alors paid_at = null)
    watch(() => form.payment_status, (newStatus) => {
        if (newStatus === 'paid' && !form.paid_at) {
            form.paid_at = getCurrentDate()
        }
        // Suppression de la logique qui vidait paid_at quand pending
    })

    // Auto-ajustement supprimé : on préfère afficher des messages d'erreur
    // plutôt que de modifier automatiquement les dates

    // Validation croisée : si on change la date de création, recalculer les validations des autres dates
    watch(() => form.created_date, () => {
        // Recalcul automatique des computed properties
        // Force la réévaluation des validations
    })

    // Fonction pour obtenir la date de début du projet au format date
    function getProjectStartDateForInput(): string {
        if (eventData?.project.start_date) {
            const projectStartDate = new Date(eventData.project.start_date)
            const year = projectStartDate.getFullYear()
            const month = String(projectStartDate.getMonth() + 1).padStart(2, '0')
            const day = String(projectStartDate.getDate()).padStart(2, '0')
            return `${year}-${month}-${day}`
        }
        return ''
    }

    // Fonction pour obtenir la date minimum pour un événement
    function getMinDateForEvent(): string {
        let minDate = new Date(form.created_date || eventData?.created_date || new Date())

        if (eventData?.project.start_date) {
            const projectStartDate = new Date(eventData.project.start_date)
            if (projectStartDate > minDate) {
                minDate = projectStartDate
            }
        }

        const year = minDate.getFullYear()
        const month = String(minDate.getMonth() + 1).padStart(2, '0')
        const day = String(minDate.getDate()).padStart(2, '0')
        return `${year}-${month}-${day}`
    }

    // Affichage conditionnel des champs
    // showBillingFields est maintenant déterminé statiquement dans le composant
    // car le type d'événement ne peut plus changer
    const showPaidAtField = computed(() => form.payment_status === 'paid')

    // Champ completed_at conditionnel selon le statut (pour les deux types)
    const showCompletedAtField = computed(() =>
        (form.event_type === 'step' && form.status === 'done') ||
        (form.event_type === 'billing' && form.status === 'sent')
    )

    // Watcher pour définir automatiquement completed_at selon le statut
    watch(() => form.status, (newStatus, oldStatus) => {
        const currentDate = getCurrentDate()

        // Logique unifiée pour completed_at
        const shouldShowCompletedAt = (
            (form.event_type === 'step' && newStatus === 'done') ||
            (form.event_type === 'billing' && newStatus === 'sent')
        )

        const wasShowingCompletedAt = (
            (form.event_type === 'step' && oldStatus === 'done') ||
            (form.event_type === 'billing' && oldStatus === 'sent')
        )

        if (shouldShowCompletedAt && !wasShowingCompletedAt) {
            // Passage à "fait"/"envoyé" : définir completed_at à aujourd'hui
            if (!form.completed_at) {
                form.completed_at = currentDate
            }
        } else if (!shouldShowCompletedAt && wasShowingCompletedAt) {
            // Sortie de "fait"/"envoyé" : vider completed_at
            form.completed_at = ''
        }
    })

    // Watchers pour déclencher les validations en temps réel croisées
    // Quand created_date change, valider tous les autres champs qui en dépendent
    watch(() => form.created_date, () => {
        // Forcer la réévaluation des validations qui dépendent de created_date
        validateExecutionDate()
        validateSendDate()
        validatePaymentDueDate()
        validatePaidAt()
        validateCompletedAt()
    })

    // Validation initiale seulement pour les champs qui ont des valeurs
    // (pas besoin de valider immédiatement les champs vides au chargement dans un formulaire d'édition)

    // Watchers individuels pour chaque champ
    watch(() => form.execution_date, validateExecutionDate)
    watch(() => form.send_date, () => {
        validateSendDate()
        // Quand send_date change, revalider payment_due_date qui en dépend
        validatePaymentDueDate()
    })
    watch(() => form.payment_due_date, validatePaymentDueDate)
    watch(() => form.paid_at, validatePaidAt)
    watch(() => [form.completed_at, form.status, form.event_type], validateCompletedAt)




    // Fonctions de validation individuelles
    function validateExecutionDate() {
        if (!form.execution_date || !form.created_date) {
            executionDateValidationError.value = ''
        } else {
            const createdAt = new Date(form.created_date)
            const executionDate = new Date(form.execution_date)

            if (executionDate < createdAt) {
                executionDateValidationError.value = 'La date d\'exécution ne peut pas être antérieure à la date de création'
            } else {
                executionDateValidationError.value = ''
            }
        }
    }

    function validateSendDate() {
        if (!form.send_date || !form.created_date) {
            sendDateValidationError.value = ''
        } else {
            const createdAt = new Date(form.created_date)
            const sendDate = new Date(form.send_date)

            if (sendDate < createdAt) {
                sendDateValidationError.value = 'La date d\'envoi ne peut pas être antérieure à la date de création'
            } else {
                sendDateValidationError.value = ''
            }
        }
    }

    function validatePaymentDueDate() {
        if (!form.payment_due_date && form.event_type === 'billing') {
            paymentDueDateValidationError.value = 'La date d\'échéance est obligatoire'
            return
        }

        if (!form.payment_due_date || form.event_type !== 'billing') {
            paymentDueDateValidationError.value = ''
        } else {
            const paymentDueDate = new Date(form.payment_due_date)
            let errorMessage = ''

            // Vérifier par rapport à la date de création
            if (form.created_date) {
                const createdAt = new Date(form.created_date)
                if (paymentDueDate < createdAt) {
                    errorMessage = 'L\'échéance de paiement ne peut pas être antérieure à la date de création'
                }
            }

            // Vérifier par rapport à la date d'envoi (si pas d'erreur précédente)
            if (!errorMessage && form.send_date) {
                const sendDate = new Date(form.send_date)
                if (paymentDueDate < sendDate) {
                    errorMessage = 'L\'échéance de paiement ne peut pas être antérieure à la date d\'envoi'
                }
            }

            paymentDueDateValidationError.value = errorMessage
        }
    }

    function validatePaidAt() {
        if (!form.paid_at && form.event_type === 'billing' && form.payment_status === 'paid') {
            paidAtValidationError.value = 'La date de paiement est obligatoire'
            return
        }
        if (!form.paid_at || form.event_type !== 'billing') {
            paidAtValidationError.value = ''
        } else if (form.created_date) {
            const paidAtDate = new Date(form.paid_at)
            const createdAt = new Date(form.created_date)

            if (paidAtDate < createdAt) {
                paidAtValidationError.value = 'La date de paiement ne peut pas être antérieure à la date de création'
            } else {
                paidAtValidationError.value = ''
            }
        }
    }

    function validateCompletedAt() {

        const shouldRequireCompletedAt = (
            (form.event_type === 'step' && form.status === 'done') ||
            (form.event_type === 'billing' && form.status === 'sent')
        )

        if (!shouldRequireCompletedAt) {
            completedAtValidationError.value = ''
        } else if (!form.completed_at) {
            const label = form.event_type === 'billing' ? 'envoi' : 'éxécution'
            completedAtValidationError.value = `La date d'${label} est obligatoire`
        } else {
            const completedAtDate = new Date(form.completed_at)
            let errorMessage = ''

            // Vérifier par rapport à la date de création
            if (form.created_date) {
                const createdAt = new Date(form.created_date)
                if (completedAtDate < createdAt) {
                    const label = form.event_type === 'billing' ? 'envoi' : 'éxécution'
                    errorMessage = `La date d'${label} ne peut pas être antérieure à la date de création`
                }
            }

            // Vérifier par rapport à la date de début du projet si disponible (si pas d'erreur précédente)
            if (!errorMessage && eventData?.project?.start_date) {
                const projectStartDate = new Date(eventData.project.start_date)
                if (completedAtDate < projectStartDate) {
                    const label = form.event_type === 'billing' ? 'envoi' : 'éxécution'
                    errorMessage = `La date d'${label} ne peut pas être antérieure à la date de début du projet`
                }
            }

            completedAtValidationError.value = errorMessage
        }
    }

    // Validation globale
    const isFormValid = computed(() => {
        return isCreatedAtValid.value &&
               isExecutionDateValid.value &&
               isSendDateValid.value &&
               isPaymentDueDateValid.value &&
               isPaidAtValid.value &&
               isCompletedAtValid.value &&
               !form.processing
    })

    const validation: EventEditFormValidation = {
        isCreatedAtValid,
        isExecutionDateValid,
        isSendDateValid,
        isPaymentDueDateValid,
        isPaidAtValid,
        isCompletedAtValid,
        createdAtValidationError,
        executionDateValidationError,
        sendDateValidationError,
        paymentDueDateValidationError,
        paidAtValidationError,
        completedAtValidationError
    }

    // Handlers pour les actions du formulaire
    const handleSubmit = (): void => {
        if (!eventData) return
        form.put(route('events.update', eventData.id))
    }

    const handleDelete = (): void => {
        if (!eventData) return
        if (confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')) {
            form.delete(route('events.destroy', eventData.id))
        }
    }

    return {
        form,
        validation,
        isFormValid,
        getProjectStartDateForInput,
        getMinDateForEvent,
        getCurrentDate,
        showPaidAtField,
        showCompletedAtField,
        handleSubmit,
        handleDelete
    }
}
