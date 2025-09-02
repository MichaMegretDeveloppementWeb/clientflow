import type { ComputedRef, Ref } from 'vue'

export interface EventEditProject {
    id: number
    name: string
    start_date?: string
    client: {
        id: number
        name: string
    }
}

export interface EventEditFormData {
    id: number
    project_id: number
    name: string
    description?: string
    type: string
    event_type: 'step' | 'billing'
    status: string
    created_date: string
    execution_date?: string
    send_date?: string
    payment_due_date?: string
    completed_at?: string
    amount?: number
    payment_status?: 'pending' | 'paid'
    paid_at?: string
    updated_at?: string
    project: EventEditProject
}

export interface EventEditSkeletonData {
    event: {
        id: null
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
        project: {
            id: null
            name: string
            start_date: string
            client: {
                id: null
                name: string
            }
        }
    }
}

export interface EventEditData {
    event: EventEditFormData | Record<string, never>
    errors: Record<string, string>
}

export interface EventEditState {
    isLoading: boolean
    data: EventEditSkeletonData | EventEditData
    error: Record<string, string> | null
}

export interface EventEditFormValidation {
    isCreatedAtValid: ComputedRef<boolean>
    isExecutionDateValid: ComputedRef<boolean>
    isSendDateValid: ComputedRef<boolean>
    isPaymentDueDateValid: ComputedRef<boolean>
    isPaidAtValid: ComputedRef<boolean>
    isCompletedAtValid: ComputedRef<boolean>
    createdAtValidationError: Ref<string>
    executionDateValidationError: Ref<string>
    sendDateValidationError: Ref<string>
    paymentDueDateValidationError: Ref<string>
    paidAtValidationError: Ref<string>
    completedAtValidationError: Ref<string>
}