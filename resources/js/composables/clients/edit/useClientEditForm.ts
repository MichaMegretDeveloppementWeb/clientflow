import { useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import type { ClientEditFormData, ClientEditFormErrors } from '@/types/clients/edit'

export function useClientEditForm(clientData: ClientEditFormData | null) {
    // Initialisation du formulaire avec les données existantes
    const form = useForm<Partial<ClientEditFormData>, ClientEditFormErrors>({
        name: clientData?.name || '',
        company: clientData?.company || '',
        email: clientData?.email || '',
        phone: clientData?.phone || '',
        address: clientData?.address || '',
        notes: clientData?.notes || ''
    })

    // Initialiser le formulaire avec les données existantes
    const initializeForm = (client: ClientEditFormData | null) => {
        if (client) {
            form.name = client.name || ''
            form.company = client.company || ''
            form.email = client.email || ''
            form.phone = client.phone || ''
            form.address = client.address || ''
            form.notes = client.notes || ''
        }
    }

    // Soumission du formulaire
    const submit = () => {
        if (!clientData) return
        form.put(route('clients.update', clientData.id))
    }

    // Reset du formulaire aux valeurs originales
    const resetForm = () => {
        if (clientData) {
            initializeForm(clientData)
            form.clearErrors()
        }
    }

    // Cancel - retour au détail du client
    const cancel = () => {
        if (form.isDirty) {
            if (confirm('Voulez-vous vraiment annuler ? Les modifications seront perdues.')) {
                if (clientData) {
                    globalThis.location.href = route('clients.show', clientData.id)
                } else {
                    globalThis.history.back()
                }
            }
        } else {
            if (clientData) {
                globalThis.location.href = route('clients.show', clientData.id)
            } else {
                globalThis.history.back()
            }
        }
    }

    // Delete client
    const deleteClient = () => {
        if (!clientData) return
        form.delete(route('clients.destroy', clientData.id))
    }

    return {
        form,
        initializeForm,
        submit,
        resetForm,
        cancel,
        deleteClient
    }
}
