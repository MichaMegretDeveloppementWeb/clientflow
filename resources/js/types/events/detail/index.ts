import type { EventDTO } from '@/types/models'

export interface EventDetailProps {
    eventId: number
    skeletonData?: EventDetailData
}

export interface EventDetailData {
    event: EventDTO & {
        project: {
            id: number
            name: string
            client: {
                id: number
                name: string
            }
        }
    }
    related_events?: EventDTO[]
    statistics?: EventStatistics
    timeline?: TimelineItem[]
}

export interface EventDetailState {
    isLoading: boolean
    data: EventDetailData | null
    error: Record<string, string> | null
}

export interface EventStatistics {
    total_project_events?: number
    completed_events?: number
    pending_events?: number
    total_project_amount?: number
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

export interface EventActions {
    canChangeStatus: boolean
    canMarkAsPaid: boolean
    canEdit: boolean
    canDelete: boolean
}
