export interface ClientCreateFormData {
    id: number | null
    name: string
    company: string
    email: string
    phone: string
    address: string
    notes: string
    created_at: string
}

export interface ClientCreateFormErrors {
    name?: string
    company?: string
    email?: string
    phone?: string
    address?: string
    notes?: string
    general?: string
}

export interface ClientCreateSkeletonData {
    client: ClientCreateFormData
    errors: Record<string, string> | string[]
}

export interface ClientCreateData {
    client: ClientCreateFormData
    errors: Record<string, string> | string[]
}

export interface ClientCreateState {
    isLoading: boolean
    data: ClientCreateSkeletonData | ClientCreateData | null
    error: Record<string, string> | string | null
}

export interface ClientCreateProps {
    skeletonData: ClientCreateSkeletonData
    data?: ClientCreateData
}

// Types pour les composants
export interface ClientCreateHeaderProps {
    isLoading: boolean
    hasError: boolean
}

export interface ClientCreateFormProps {
    isLoading: boolean
    hasError: boolean
    clientData: ClientCreateFormData | null
}
