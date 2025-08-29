import type { ComputedRef, Ref } from 'vue'

export interface ProjectEditClient {
    id: number
    name: string
    company?: string
}

export interface ProjectEditFormData {
    id: number
    name: string
    description?: string
    status: 'active' | 'completed' | 'on_hold' | 'cancelled'
    budget?: number
    start_date?: string
    end_date?: string
    client_id: number
    created_at: string
    updated_at: string
    client: ProjectEditClient
}

export interface ProjectEditSkeletonData {
    project: {
        id: null
        name: string
        description: string
        status: string
        budget: null
        start_date: string
        end_date: string
        client_id: null
        created_at: string
        updated_at: string
        client: {
            id: null
            name: string
            company: string
        }
    }
}

export interface ProjectEditData {
    project: ProjectEditFormData | Record<string, never>
    errors: Record<string, string>
}

export interface ProjectEditState {
    isLoading: boolean
    data: ProjectEditSkeletonData | ProjectEditData
    error: Record<string, string> | null
}

export interface ProjectEditFormValidation {
    isNameValid: ComputedRef<boolean>
    isClientValid: ComputedRef<boolean>
    isStatusValid: ComputedRef<boolean>
    isBudgetValid: ComputedRef<boolean>
    isStartDateValid: ComputedRef<boolean>
    isEndDateValid: ComputedRef<boolean>
    areDatesCoherent: ComputedRef<boolean>
    nameValidationError: Ref<string>
    clientValidationError: Ref<string>
    statusValidationError: Ref<string>
    budgetValidationError: Ref<string>
    startDateValidationError: Ref<string>
    endDateValidationError: Ref<string>
    datesCoherenceError: Ref<string>
}

// Type étendu pour les erreurs du formulaire incluant les erreurs générales
export interface ProjectEditFormErrors {
    name?: string
    description?: string
    status?: string
    budget?: string
    start_date?: string
    end_date?: string
    client_id?: string
    general?: string
}

// Type pour le formulaire Inertia étendu avec erreurs personnalisées
export interface ProjectEditInertiaForm {
    name: string
    description: string
    status: 'active' | 'completed' | 'on_hold' | 'cancelled'
    budget: string
    start_date: string
    end_date: string
    client_id: number | null
    errors: ProjectEditFormErrors
    processing: boolean
    hasErrors: boolean
    put: (url: string, options?: any) => void
    clearErrors: (field?: string) => void
}