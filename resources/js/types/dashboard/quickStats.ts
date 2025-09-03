export interface QuickStatsMetric {
    value: number
    label: string
}

export interface QuickStatsRevenueGrowth {
    rate: number
    formatted: string
    is_positive: boolean
    trend_icon: 'trending-up' | 'trending-down'
    trend_color: 'emerald' | 'red'
}

export interface QuickStats {
    completion_rate: {
        value: number
        formatted: string
    }
    revenue_per_client: {
        value: number
        formatted: string
    }
    revenue_growth: QuickStatsRevenueGrowth
    metrics: {
        active_projects: QuickStatsMetric
        pending_invoices: QuickStatsMetric
        urgent_tasks: QuickStatsMetric
    }
    error?: string | null
}

export interface QuickStatsResponse {
    quick_stats: QuickStats
}