export interface ClientStatistics {
    total: number
    this_month: number
}

export interface ProjectStatistics {
    active: number
    completed: number
    on_hold: number
}

export interface DashboardStatistics {
    clients: ClientStatistics
    projects: ProjectStatistics
}

export interface StatisticsResponse {
    statistics: DashboardStatistics
}