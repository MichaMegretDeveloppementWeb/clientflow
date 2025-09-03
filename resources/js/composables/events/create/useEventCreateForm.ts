import { computed, ref, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import type { EventCreateFormValidation, EventCreateProject } from '@/types/events/create'

export function useEventCreateForm(
    projects: Array<EventCreateProject>, 
    selectedProject: EventCreateProject | null,
    preselectedProjectId?: number | null
) {
    // Variables pour les messages d'erreur personnalisés
    const projectIdValidationError = ref<string>('')
    const nameValidationError = ref<string>('')
    const typeValidationError = ref<string>('')
    
    // Flags pour savoir si les champs ont été "touchés" par l'utilisateur
    const projectIdTouched = ref(false)
    const nameTouched = ref(false)
    const typeTouched = ref(false)
    const createdAtValidationError = ref<string>('')
    const executionDateValidationError = ref<string>('')
    const sendDateValidationError = ref<string>('')
    const paymentDueDateValidationError = ref<string>('')
    const paidAtValidationError = ref<string>('')
    const completedAtValidationError = ref<string>('')

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
        project_id: preselectedProjectId || selectedProject?.id || null,
        name: '',
        description: '',
        type: '',
        event_type: 'step',
        status: 'todo',
        created_date: getCurrentDate(),
        execution_date: '',
        send_date: '',
        payment_due_date: '',
        completed_at: '',
        amount: null,
        payment_status: 'pending',
        paid_at: '',
    })

    // Computed pour déterminer si on affiche les champs de facturation
    const showBillingFields = computed(() => form.event_type === 'billing')

    // Computed pour déterminer si on affiche le sélecteur de projet
    const showProjectSelector = computed(() => !preselectedProjectId && !selectedProject)

    // Computed pour obtenir le projet sélectionné
    const currentProject = computed(() => {
        if (preselectedProjectId || selectedProject) {
            return selectedProject
        }
        return projects.find(p => p.id === form.project_id) || null
    })

    // Computed pour la validation (sans effets de bord)
    const isProjectIdValid = computed(() => {
        if (!projectIdTouched.value) return true
        return !!form.project_id
    })

    const isNameValid = computed(() => {
        if (!nameTouched.value) return true
        return !!(form.name && form.name.trim() !== '')
    })

    const isTypeValid = computed(() => {
        if (!typeTouched.value) return true
        return !!form.type
    })

    // Watchers pour les messages d'erreur
    watch([() => form.project_id, projectIdTouched], () => {
        if (!projectIdTouched.value) {
            projectIdValidationError.value = ''
        } else if (!form.project_id) {
            projectIdValidationError.value = 'La sélection d\'un projet est obligatoire'
        } else {
            projectIdValidationError.value = ''
        }
    })

    watch([() => form.name, nameTouched], () => {
        if (!nameTouched.value) {
            nameValidationError.value = ''
        } else if (!form.name || form.name.trim() === '') {
            nameValidationError.value = 'Le nom de l\'événement est obligatoire'
        } else {
            nameValidationError.value = ''
        }
    })

    watch([() => form.type, typeTouched], () => {
        if (!typeTouched.value) {
            typeValidationError.value = ''
        } else if (!form.type) {
            typeValidationError.value = 'La catégorie est obligatoire'
        } else {
            typeValidationError.value = ''
        }
    })

    // Validation de created_date
    const isCreatedAtValid = computed(() => {
        if (!form.created_date || !currentProject.value?.start_date) {
            createdAtValidationError.value = ''
            return true
        }

        const createdAt = new Date(form.created_date)
        const projectStartDate = new Date(currentProject.value.start_date)

        if (createdAt < projectStartDate) {
            createdAtValidationError.value = 'La date de création ne peut pas être antérieure au début du projet'
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

    // Auto-remplissage de la date de paiement quand on passe à 'paid'
    watch(() => form.payment_status, (newStatus) => {
        if (newStatus === 'paid' && !form.paid_at) {
            form.paid_at = getCurrentDate()
        }
    })

    // Watcher pour changer le statut par défaut selon le type d'événement
    watch(() => form.event_type, (newType) => {
        form.status = newType === 'billing' ? 'to_send' : 'todo'
    })

    // Watchers pour déclencher les validations en temps réel croisées
    watch(() => form.created_date, () => {
        validateExecutionDate()
        validateSendDate()
        validatePaymentDueDate()
        validatePaidAt()
        validateCompletedAt()
    })

    // Watchers individuels pour chaque champ
    watch(() => form.execution_date, validateExecutionDate)
    watch(() => form.send_date, () => {
        validateSendDate()
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
            const label = form.event_type === 'billing' ? 'envoi' : 'exécution'
            completedAtValidationError.value = `La date d'${label} est obligatoire`
        } else {
            const completedAtDate = new Date(form.completed_at)
            let errorMessage = ''

            // Vérifier par rapport à la date de création
            if (form.created_date) {
                const createdAt = new Date(form.created_date)
                if (completedAtDate < createdAt) {
                    const label = form.event_type === 'billing' ? 'envoi' : 'exécution'
                    errorMessage = `La date d'${label} ne peut pas être antérieure à la date de création`
                }
            }

            // Vérifier par rapport à la date de début du projet si disponible
            if (!errorMessage && currentProject.value?.start_date) {
                const projectStartDate = new Date(currentProject.value.start_date)
                if (completedAtDate < projectStartDate) {
                    const label = form.event_type === 'billing' ? 'envoi' : 'exécution'
                    errorMessage = `La date d'${label} ne peut pas être antérieure à la date de début du projet`
                }
            }

            completedAtValidationError.value = errorMessage
        }
    }

    // Auto-ajustement de completed_at selon le statut
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
            if (!form.completed_at) {
                form.completed_at = currentDate
            }
        } else if (!shouldShowCompletedAt && wasShowingCompletedAt) {
            form.completed_at = ''
        }
    })

    // Affichage conditionnel des champs
    const showPaidAtField = computed(() => form.payment_status === 'paid')

    const showCompletedAtField = computed(() =>
        (form.event_type === 'step' && form.status === 'done') ||
        (form.event_type === 'billing' && form.status === 'sent')
    )

    // Fonction pour obtenir la date minimum pour un événement
    function getMinDateForEvent(): string {
        let minDate = new Date(form.created_date || getCurrentDate())

        if (currentProject.value?.start_date) {
            const projectStartDate = new Date(currentProject.value.start_date)
            if (projectStartDate > minDate) {
                minDate = projectStartDate
            }
        }

        const year = minDate.getFullYear()
        const month = String(minDate.getMonth() + 1).padStart(2, '0')
        const day = String(minDate.getDate()).padStart(2, '0')
        return `${year}-${month}-${day}`
    }

    // Fonction pour obtenir la date de début du projet au format date
    function getProjectStartDateForInput(): string {
        if (currentProject.value?.start_date) {
            const projectStartDate = new Date(currentProject.value.start_date)
            const year = projectStartDate.getFullYear()
            const month = String(projectStartDate.getMonth() + 1).padStart(2, '0')
            const day = String(projectStartDate.getDate()).padStart(2, '0')
            return `${year}-${month}-${day}`
        }
        return ''
    }

    // Validation globale - SEULEMENT pour la soumission finale
    const isFormValid = computed(() => {
        // Vérifier que les champs obligatoires de base sont remplis (sans regarder les touched)
        const basicFieldsValid = form.project_id && 
                               form.name && form.name.trim() !== '' && 
                               form.type
        
        // Vérifier les validations de dates (qui fonctionnent déjà bien)
        const dateFieldsValid = isCreatedAtValid.value &&
                               isExecutionDateValid.value &&
                               isSendDateValid.value &&
                               isPaymentDueDateValid.value &&
                               isPaidAtValid.value &&
                               isCompletedAtValid.value
        
        return basicFieldsValid && dateFieldsValid && !form.processing
    })

    const validation: EventCreateFormValidation = {
        isProjectIdValid,
        isNameValid,
        isTypeValid,
        isCreatedAtValid,
        isExecutionDateValid,
        isSendDateValid,
        isPaymentDueDateValid,
        isPaidAtValid,
        isCompletedAtValid,
        projectIdValidationError,
        nameValidationError,
        typeValidationError,
        createdAtValidationError,
        executionDateValidationError,
        sendDateValidationError,
        paymentDueDateValidationError,
        paidAtValidationError,
        completedAtValidationError
    }

    // Méthodes pour marquer les champs comme touchés
    const markProjectIdAsTouched = () => { projectIdTouched.value = true }
    const markNameAsTouched = () => { nameTouched.value = true }
    const markTypeAsTouched = () => { typeTouched.value = true }

    // Handler pour la soumission du formulaire
    const handleSubmit = (): void => {
        // Marquer tous les champs comme touchés avant soumission pour afficher les erreurs
        projectIdTouched.value = true
        nameTouched.value = true
        typeTouched.value = true
        
        form.post(route('events.store'))
    }

    return {
        form,
        validation,
        isFormValid,
        showBillingFields,
        showProjectSelector,
        showPaidAtField,
        showCompletedAtField,
        currentProject,
        getProjectStartDateForInput,
        getMinDateForEvent,
        getCurrentDate,
        handleSubmit,
        markProjectIdAsTouched,
        markNameAsTouched,
        markTypeAsTouched
    }
}