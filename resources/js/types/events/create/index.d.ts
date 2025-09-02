import type { ComputedRef, Ref } from 'vue'

export interface EventCreateProject {
    id: number
    name: string
    start_date?: string
    client: {
        id: number
        name: string
    }
}

export interface EventCreateFormData {
    id: null
    project_id: number | null
    name: string
    description: string
    type: string
    event_type: 'step' | 'billing'
    status: string
    created_date: string
    execution_date: string
    send_date: string
    payment_due_date: string
    completed_at: string
    amount: number | null
    payment_status: 'pending' | 'paid'
    paid_at: string
}

export interface EventCreateSkeletonData {
    projects: Array<any>
    selectedProject: EventCreateProject | null
    event: {
        id: null
        project_id: number | null
        name: string
        description: string
        type: string
        event_type: string
        status: string
        created_date: string
        execution_date: string
        send_date: string
        payment_due_date: string
        completed_at: string
        amount: null
        payment_status: string
        paid_at: string
    }
}

export interface EventCreateData {
    projects: Array<EventCreateProject>
    selectedProject: EventCreateProject | null
    event: EventCreateFormData
    errors: Record<string, string>
}

export interface EventCreateState {
    isLoading: boolean
    data: EventCreateSkeletonData | EventCreateData
    error: Record<string, string> | null
}

export interface EventCreateFormValidation {
    isProjectIdValid: ComputedRef<boolean>
    isNameValid: ComputedRef<boolean>
    isTypeValid: ComputedRef<boolean>
    isCreatedAtValid: ComputedRef<boolean>
    isExecutionDateValid: ComputedRef<boolean>
    isSendDateValid: ComputedRef<boolean>
    isPaymentDueDateValid: ComputedRef<boolean>
    isPaidAtValid: ComputedRef<boolean>
    isCompletedAtValid: ComputedRef<boolean>
    projectIdValidationError: Ref<string>
    nameValidationError: Ref<string>
    typeValidationError: Ref<string>
    createdAtValidationError: Ref<string>
    executionDateValidationError: Ref<string>
    sendDateValidationError: Ref<string>
    paymentDueDateValidationError: Ref<string>
    paidAtValidationError: Ref<string>
    completedAtValidationError: Ref<string>
}