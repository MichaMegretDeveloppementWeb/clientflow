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
        }
    }
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