import { ref, watch, type Ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import type { ProjectEditFormData, ProjectEditInertiaForm } from '@/types/projects/edit'

export function useProjectEditForm(projectRef: Ref<ProjectEditFormData | null>) {
    // Modal state
    const showDeleteModal = ref(false)

    // Form state
    const form = useForm({
        name: '',
        description: '',
        status: 'active' as 'active' | 'completed' | 'on_hold' | 'cancelled',
        budget: '' as string,
        start_date: '',
        end_date: '',
        client_id: null as number | null,
    }) as ProjectEditInertiaForm

    // Watch for project data changes and update form
    watch(projectRef, (newProject) => {
        if (newProject) {
            form.name = newProject.name
            form.description = newProject.description || ''
            form.status = newProject.status
            form.budget = newProject.budget ? String(newProject.budget) : ''
            form.start_date = newProject.start_date ? newProject.start_date.split('T')[0] : ''
            form.end_date = newProject.end_date ? newProject.end_date.split('T')[0] : ''
            form.client_id = newProject.client_id
        }
    }, { immediate: true })

    // Form submission
    const submit = () => {
        if (!projectRef.value) return

        form.put(route('projects.update', projectRef.value.id), {
            preserveScroll: true,
        })
    }

    // Navigation
    const goBack = () => {
        if (projectRef.value) {
            window.location.href = route('projects.show', projectRef.value.id)
        } else {
            window.location.href = route('projects.index')
        }
    }

    // Delete project
    const confirmDelete = () => {
        if (!projectRef.value) return

        router.delete(route('projects.destroy', projectRef.value.id))
    }

    // Reset form to original values
    const resetForm = () => {
        if (projectRef.value) {
            form.name = projectRef.value.name
            form.description = projectRef.value.description || ''
            form.status = projectRef.value.status
            form.budget = projectRef.value.budget ? String(projectRef.value.budget) : ''
            form.start_date = projectRef.value.start_date ? projectRef.value.start_date.split('T')[0] : ''
            form.end_date = projectRef.value.end_date ? projectRef.value.end_date.split('T')[0] : ''
            form.client_id = projectRef.value.client_id
            form.clearErrors()
        }
    }

    return {
        // State
        form,
        showDeleteModal,

        // Actions
        submit,
        goBack,
        confirmDelete,
        resetForm,
    }
}
