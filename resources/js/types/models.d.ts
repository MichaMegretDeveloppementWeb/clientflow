// DTO Types for Frontend
// These types match the DTOs created in the backend

export interface ClientDTO {
    id: number;
    name: string;
    company?: string;
    email?: string;
    phone?: string;
    address?: string;
    notes?: string;
    projects_count: number;
    active_projects_count: number;
    total_revenue: number;
    pending_amount: number;
    has_overdue_payments: boolean;
    latest_project?: {
        id: number;
        name: string;
        status: string;
        created_at: string;
    };
    created_at: string;
    updated_at: string;
}

export interface ProjectDTO {
    id: number;
    client_id: number;
    name: string;
    description?: string;
    status: ProjectStatus;
    budget?: number;
    start_date?: string;
    end_date?: string;
    client: {
        id: number;
        name: string;
        company?: string;
    };
    events_count: number;
    completed_tasks_count: number;
    pending_tasks_count: number;
    total_billed: number;
    total_paid: number;
    total_unpaid: number;
    budget_progress: number;
    budget_exceeded: boolean;
    is_overdue: boolean;
    has_overdue_events: boolean;
    has_payment_overdue: boolean;
    created_at: string;
    updated_at: string;
}

export interface EventDTO {
    id: number;
    project_id: number;
    name: string;
    description?: string;
    type: string;
    event_type: EventType;
    status: EventStatus;
    amount?: number;
    payment_status?: PaymentStatus;
    created_date: string;
    execution_date?: string;
    send_date?: string;
    payment_due_date?: string;
    completed_at?: string;
    paid_at?: string;
    project: {
        id: number;
        name: string;
        client: {
            id: number;
            name: string;
            company?: string;
        };
    };
    event_type_label: string;
    status_label: string;
    payment_status_label?: string;
    is_overdue: boolean;
    is_payment_overdue: boolean;
    formatted_amount?: string;
    updated_at: string;
}

export interface DashboardDTO {
    stats: {
        totalClients: number;
        activeProjects: number;
        completedProjects: number;
        onHoldProjects: number;
        monthlyRevenue: number;
        revenueGrowth: number;
        clientsGrowth: number;
        projectsCompletedThisWeek: number;
    };
    urgent_tasks: EventDTO[];
    recent_activities: Array<{
        id: string;
        type: string;
        title: string;
        description: string;
        time: string;
        icon: string;
        color: string;
        link?: string;
    }>;
    upcoming_events: EventDTO[];
    unpaid_invoices: EventDTO[];
}

// Enums
export type ProjectStatus = 'active' | 'completed' | 'on_hold' | 'cancelled';
export type EventType = 'step' | 'billing';
export type EventStatus = 'todo' | 'done' | 'to_send' | 'sent' | 'cancelled';
export type PaymentStatus = 'pending' | 'paid';

// Form Data Types
export interface ClientFormData {
    name: string;
    company?: string;
    email?: string;
    phone?: string;
    address?: string;
    notes?: string;
}

export interface ProjectFormData {
    client_id: number;
    name: string;
    description?: string;
    status: ProjectStatus;
    budget?: number;
    start_date?: string;
    end_date?: string;
}

export interface EventFormData {
    project_id: number;
    name: string;
    description?: string;
    type: string;
    event_type: EventType;
    status: EventStatus;
    amount?: number;
    payment_status?: PaymentStatus;
    created_date: string;
    execution_date?: string;
    send_date?: string;
    payment_due_date?: string;
    paid_at?: string;
}

// API Response Types
export interface PaginatedResponse<T> {
    data: T[];
    links: any;
    meta: {
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
}

// Filter Types
// Note: ClientFilters moved to types/clients/list/filters.d.ts

export interface ProjectFilters {
    status?: ProjectStatus;
    client_id?: number;
    search?: string;
    sort_by?: string;
    sort_order?: 'asc' | 'desc';
}

export interface EventFilters {
    event_type?: EventType;
    status?: EventStatus;
    payment_status?: PaymentStatus;
    project_id?: number;
    client_id?: number;
    overdue?: boolean;
    search?: string;
    sort?: string;
    direction?: 'asc' | 'desc';
}

// Dashboard specific types
export interface DashboardStats {
    totalClients: number;
    activeProjects: number;
    completedProjects: number;
    onHoldProjects: number;
    monthlyRevenue: number;
    clientsGrowth: number;
    projectsCompletedThisWeek: number;
}

export interface DashboardData {
    stats: {
        totalClients: number;
        activeProjects: number;
        completedProjects: number;
        onHoldProjects: number;
        monthlyRevenue: number;
        revenueGrowth: number;
        clientsGrowth: number;
        projectsCompletedThisWeek: number;
    };
    urgent_tasks: EventDTO[];
    upcoming_events: EventDTO[];
    recent_projects: ProjectDTO[];
    recent_activities: {
        id: string | number;
        type: string;
        title: string;
        description: string;
        icon: string;
        color: string;
        time: string;
        link?: string;
    }[];
    financial_stats: {
        total_billed: number;
        total_paid: number;
        total_pending: number;
        total_upcoming_payment: number;
        total_overdue_payment: number;
    };
    revenue_data: any;
    summary: {
        urgent_tasks_count: number;
        upcoming_events_count: number;
        recent_projects_count: number;
        overdue_projects_count: number;
        high_priority_events: number;
        needs_attention: boolean;
    };
}

export interface ChartData {
    data: Array<{
        date: string;
        label: string;
        amount: number;
        tooltip_date?: string;
    }>;
    groupBy: string;
    totalAmount: number;
}