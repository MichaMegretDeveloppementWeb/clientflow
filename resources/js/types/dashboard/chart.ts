export interface ChartDataset {
    label: string
    data: number[]
    borderColor: string
    backgroundColor: string
    tension: number
    fill?: boolean
}

export interface RevenueChartData {
    labels: string[]
    datasets: ChartDataset[]
    granularity?: string
    period?: string
    start_date?: string | null
    end_date?: string | null
    chart_options?: Record<string, any>
    error?: string | null
}

export interface RevenueChartResponse {
    revenue_chart: RevenueChartData
}

export interface ChartOptions {
    responsive: boolean
    maintainAspectRatio: boolean
    interaction: {
        intersect: boolean
        mode: string
    }
    plugins: {
        legend: {
            position: string
        }
        tooltip: {
            callbacks: Record<string, string>
        }
    }
    scales: {
        y: {
            beginAtZero: boolean
            ticks: {
                callback: string
            }
        }
    }
}

export interface ChartPeriod {
    value: string
    label: string
}