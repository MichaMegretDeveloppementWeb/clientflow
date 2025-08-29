export interface ClientEditFormData {
    id: number
    name: string
    company?: string
    email?: string
    phone?: string
    address?: string
    notes?: string
    created_at: string
    updated_at?: string
}

export interface ClientEditFormErrors {
    name?: string
    company?: string
    email?: string
    phone?: string
    address?: string
    notes?: string
    general?: string
}

export interface ClientEditSkeletonData {
    client: {
        id: null
        name: string
        company: string
        email: string
        phone: string
        address: string
        notes: string
        created_at: string
        updated_at: string
    }
}

export interface ClientEditData {
    client: ClientEditFormData | Record<string, never>
    errors: Record<string, string>
}

export interface ClientEditState {
    isLoading: boolean
    data: ClientEditSkeletonData | ClientEditData
    error: Record<string, string> | null
}

export interface ClientEditHeaderProps {
    client?: ClientEditFormData | null
    isLoading: boolean
    hasError: boolean
}

export interface ClientEditFormProps {
    client?: ClientEditFormData | null
    isLoading: boolean
    hasError: boolean
    errors?: Record<string, string> | null
}