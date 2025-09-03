import type { ProjectDTO } from '@/types/models'
import type { Event } from '@/types/projects/events'

export interface ProjectDetailProps {
    projectId: number
    skeletonData?: ProjectDetailData
}

export interface ProjectDetailData {
    project: ProjectDTO & {
        client: {
            id: number
            name: string
            company?: string
            email?: string
        }
        has_overdue_events: boolean
        has_overdue_payments: boolean
    }
    // NOUVEAU: Statistiques financières au même niveau que project
    financialStats: FinancialStats
    events?: Event[]
    related_projects?: ProjectDTO[]
    statistics?: ProjectStatistics
    timeline?: TimelineItem[]
}

export interface ProjectDetailState {
    isLoading: boolean
    data: ProjectDetailData | null
    error: Record<string, string> | null
}

export interface ProjectStatistics {
    total_client_projects?: number
    active_projects?: number
    completed_projects?: number
    total_client_budget?: number
}

export interface TimelineItem {
    key: string
    date: string
    title: string
    icon: string
    bgClass: string
    iconClass: string
    delay?: string | null
}

export interface ProjectActions {
    canChangeStatus: boolean
    canEdit: boolean
    canDelete: boolean
}

export interface FinancialStats {
    budget: number
    totalBilled: number
    totalPaid: number
    totalUnpaid: number
    billsToSend: number
    upcomingPayments: number
    overdueUnpaid: number
    remainingBudget: number | null
    budgetUsage: {
        percentage: number
        isExceeded: boolean
    }
}