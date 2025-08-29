import { useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import type { ClientCreateFormData, ClientCreateFormErrors } from '@/types/clients/create'

export function useClientCreateForm() {
    // Formulaire Inertia avec typage des erreurs
    const form = useForm<Partial<ClientCreateFormData>, ClientCreateFormErrors>({
        name: '',
        company: '',
        email: '',
        phone: '',
        address: '',
        notes: ''
    })

    // Initialiser le formulaire avec les données existantes
    const initializeForm = (clientData: ClientCreateFormData | null) => {
        if (clientData) {
            form.name = clientData.name || ''
            form.company = clientData.company || ''
            form.email = clientData.email || ''
            form.phone = clientData.phone || ''
            form.address = clientData.address || ''
            form.notes = clientData.notes || ''
        }
    }

    // Soumission du formulaire
    const submit = () => {
        form.post(route('clients.store'))
    }

    // Reset du formulaire
    const resetForm = () => {
        form.reset()
        form.clearErrors()
    }

    // Cancel - retour à la liste
    const cancel = () => {
        if (form.isDirty) {
            if (confirm('Voulez-vous vraiment annuler ? Les modifications seront perdues.')) {
                window.history.back()
            }
        } else {
            window.history.back()
        }
    }

    return {
        form,
        initializeForm,
        submit,
        resetForm,
        cancel
    }
}
