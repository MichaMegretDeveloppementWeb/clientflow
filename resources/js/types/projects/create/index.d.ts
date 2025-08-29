import type { ComputedRef, Ref } from 'vue'

// Client structure for project creation
export interface ProjectCreateClient {
    id: number
    name: string
    company?: string
}

// Project form data structure
export interface ProjectCreateFormData {
    name: string
    description: string
    status: 'active' | 'completed' | 'on_hold' | 'cancelled'
    budget: string
    start_date: string
    end_date: string
    client_id: string
}

// Extended form errors including general error
export interface ProjectCreateFormErrors {
    name?: string
    description?: string
    status?: string
    budget?: string
    start_date?: string
    end_date?: string
    client_id?: string
    general?: string
}

// Extended Inertia form type with custom errors
export interface ProjectCreateInertiaForm {
    name: string
    description: string
    status: 'active' | 'completed' | 'on_hold' | 'cancelled'
    budget: string
    start_date: string
    end_date: string
    client_id: string
    errors: ProjectCreateFormErrors
    processing: boolean
    hasErrors: boolean
    post: (url: string, options?: any) => void
    clearErrors: (field?: string) => void
    reset: (...fields: string[]) => void
}

// Skeleton data structure for loading states
export interface ProjectCreateSkeletonData {
    clients: never[]
    selectedClientId: null
}

// Complete data structure
export interface ProjectCreateData {
    clients: ProjectCreateClient[]
    selectedClientId: number | null
}

// State management interface
export interface ProjectCreateState {
    isLoading: boolean
    data: ProjectCreateSkeletonData | ProjectCreateData
    error: Record<string, string> | null
}

// Form validation computed properties
export interface ProjectCreateFormValidation {
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