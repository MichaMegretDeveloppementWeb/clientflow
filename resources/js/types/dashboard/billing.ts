export interface BillingTotals {
    billed: number
    paid: number
    pending: number
    to_send: number
    upcoming_payment: number
    overdue_payment: number
}

export interface BillingMetrics {
    payment_rate: number
    invoices_to_send_count: number
    unpaid_invoices_count: number
    overdue_invoices_count: number
}

export interface BillingCardDetails {
    paid: number
    pending: number
    overdue: number
    payment_rate: number
    unpaid_count: number
    overdue_count: number
}

export interface BillingCard {
    id: string
    title: string
    value: number
    icon: string
    color: string
    link: string
    description: string | null
    details?: BillingCardDetails
}

export interface BillingData {
    totals: BillingTotals
    metrics: BillingMetrics
    cards: BillingCard[]
}

export interface BillingResponse {
    billing: BillingData
}