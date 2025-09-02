export interface Event {
    id: number
    name: string
    description?: string
    type: string
    event_type: string
    status: string
    amount?: number
    payment_status?: string
    execution_date?: string
    send_date?: string
    payment_due_date?: string
    completed_at?: string
    paid_at?: string
    created_date: string
    updated_at: string
}