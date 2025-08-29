import type { ClientDTO } from '@/types/models'

export interface Project {
    id: number
    name: string
    description?: string
    status: string
    start_date?: string
    end_date?: string
    budget?: number
    events_count: number
    billing_total: number
    has_overdue_events: boolean
    has_payment_overdue: boolean
    created_at?: string
}

export interface Event {
    id: number
    name: string
    description?: string
    event_type: string
    event_type_label: string
    status: string
    status_label: string
    amount?: number
    payment_status?: string
    payment_status_label?: string
    due_date?: string
    execution_date?: string
    send_date?: string
    payment_due_date?: string
    paid_at?: string
    is_overdue: boolean
    is_payment_overdue?: boolean
    days_overdue?: number
    project: {
        id: number
        name: string
    }
}

export interface ProjectStats {
    total_projects: number
    active_projects: number
    completed_projects: number
    on_hold_projects: number
}

export interface Client extends ClientDTO {
    project_stats: ProjectStats
}

export interface FinancialStats {
    total_billed: number
    total_paid: number
    total_sent: number
    total_pending: number
    total_upcoming_payment: number
    total_overdue_payment: number
}

export interface ClientDetailData {
    client: Client
    projects: Project[]
    events: Event[]
    financialStats: FinancialStats
}

export interface ClientDetailProps {
    client: Client
    projects: Project[]
    events: Event[]
    financialStats: FinancialStats
}

export interface CustomError {
    type: string
    title: string
    message: string
    code: number
}

export interface ClientDetailState {
    client: Client | null
    projects: Project[]
    events: Event[]
    financialStats: FinancialStats | null
    isLoading: boolean
    error: CustomError | Record<string, string> | null
}

export interface ClientDetailActions {
    fetchData: () => Promise<void>
    clearError: () => void
    loadRealData: () => Promise<void>
}

// Types pour les composants
export interface ClientDetailHeaderProps {
    client: Client | null
    isLoading: boolean
}

export interface ClientDetailInfoProps {
    client: Client | null
    isLoading: boolean
}

export interface ClientDetailStatsProps {
    client: Client | null
    financialStats: FinancialStats | null
    isLoading: boolean
}

export interface ClientDetailProjectsProps {
    client: Client | null
    projects: Project[]
    events: Event[]
    isLoading: boolean
}

export interface ClientDetailErrorProps {
    error: Record<string, string> | null
}
