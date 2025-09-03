import { ref, computed, watch, type Ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import type {
    ProjectCreateClient,
    ProjectCreateInertiaForm
} from '@/types/projects/create'

export function useProjectCreateForm(
    clients: Ref<ProjectCreateClient[]>,
    selectedClientId: Ref<number | null>
) {
    // Variables pour les messages d'erreur personnalisés
    const startDateError = ref<string>('')
    const endDateError = ref<string>('')
    
    // Tracking des champs touchés pour éviter les erreurs prématurées
    const startDateTouched = ref(false)
    const endDateTouched = ref(false)


    // Initialisation du formulaire avec le client pré-sélectionné
    const form = useForm({
        client_id: selectedClientId.value ? selectedClientId.value.toString() : '',
        name: '',
        description: '',
        status: 'active' as 'active' | 'completed' | 'on_hold' | 'cancelled',
        start_date: '',
        end_date: '',
        budget: '',
    }) as ProjectCreateInertiaForm

    // Validation des dates séparément
    const validateStartDate = () => {
        if (!startDateTouched.value) {
            startDateError.value = ''
            return true
        }
        
        if (!form.start_date) {
            startDateError.value = 'La date de début est obligatoire'
            return false
        }
        
        startDateError.value = ''
        return true
    }

    const validateEndDate = () => {
        if (!endDateTouched.value || !form.end_date) {
            endDateError.value = ''
            return true
        }
        
        if (!form.start_date) {
            endDateError.value = ''
            return true
        }

        const startDate = new Date(form.start_date)
        const endDate = new Date(form.end_date)

        if (endDate < startDate) {
            endDateError.value = 'La date de fin ne peut pas être antérieure à la date de début'
            return false
        }

        endDateError.value = ''
        return true
    }

    // Validation globale pour la soumission
    const isDateRangeValid = computed(() => {
        // Pour la soumission, on valide tout même si pas touché
        const isStartValid = form.start_date !== ''
        const isEndValid = !form.end_date || !form.start_date || new Date(form.end_date) >= new Date(form.start_date)
        
        return isStartValid && isEndValid
    })

    // Watchers pour validation en temps réel
    watch(() => form.start_date, () => {
        if (startDateTouched.value) {
            validateStartDate()
            validateEndDate() // Re-valider end_date si start_date change
        }
    })

    watch(() => form.end_date, () => {
        if (endDateTouched.value) {
            validateEndDate()
        }
    })

    // Watch pour mettre à jour client_id si selectedClientId change
    watch(selectedClientId, (newClientId) => {
        if (newClientId && newClientId !== parseInt(form.client_id || '0')) {
            form.client_id = newClientId.toString()
        }
    }, { immediate: true })

    // Soumission du formulaire
    const submit = () => {
        // Validation avant soumission
        if (!isDateRangeValid.value) {
            return
        }

        form.post(route('projects.store'))
    }

    // Navigation vers la liste des projets
    const goBack = () => {
        window.location.href = route('projects.index')
    }

    // Fonctions pour marquer les champs comme touchés
    const markStartDateAsTouched = () => {
        startDateTouched.value = true
        validateStartDate()
    }

    const markEndDateAsTouched = () => {
        endDateTouched.value = true
        validateEndDate()
    }

    // Réinitialiser le formulaire
    const resetForm = () => {
        form.reset()
        form.clearErrors()
        if (selectedClientId.value) {
            form.client_id = selectedClientId.value.toString()
        }
        startDateError.value = ''
        endDateError.value = ''
        startDateTouched.value = false
        endDateTouched.value = false
    }

    return {
        // Form
        form,

        // Validation
        isDateRangeValid,
        startDateError,
        endDateError,

        // Actions
        submit,
        goBack,
        resetForm,
        markStartDateAsTouched,
        markEndDateAsTouched,
    }
}
