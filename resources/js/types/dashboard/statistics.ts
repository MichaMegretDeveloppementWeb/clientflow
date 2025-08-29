export interface ClientStatistics {
    total: number
    growth_rate: number
    this_month: number
}

export interface ProjectStatistics {
    active: number
    completed: number
    on_hold: number
    cancelled: number
    completed_this_week: number
    completed_this_month: number
    on_hold_rate: number
}

export interface TaskStatistics {
    pending: number
    completion_rate: number
}

export interface RevenueStatistics {
    monthly: number
    total_billed: number
    total_paid: number
    total_pending: number
    upcoming_payment: number
    overdue_payment: number
    overdue_amount: number
    growth: number
}

export interface InvoiceStatistics {
    unpaid: number
}

export interface DashboardStatistics {
    clients: ClientStatistics
    projects: ProjectStatistics
    tasks: TaskStatistics
    revenue: RevenueStatistics
    invoices: InvoiceStatistics
}

export interface StatisticsResponse {
    statistics: DashboardStatistics
}