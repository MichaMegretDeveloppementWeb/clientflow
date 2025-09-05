// Activity-related types for dashboard components

export interface Activity {
    id: string;
    type: 'client' | 'project' | 'step' | 'billing';
    entity_type: string;
    name: string;
    company?: string;
    status: 'created' | 'done' | 'sent' | 'paid';
    status_label: string;
    timestamp: string;
    time_ago: string;
    link: string;
    parent_project?: {
        id: number;
        name: string;
    } | null;
    parent_client?: {
        id: number;
        name: string;
    } | null;
    amount?: number | null;
    formatted_amount?: string | null;
    icon: string;
    icon_color: string;
}

export interface ActivitiesState {
  activities: Activity[]
  isLoading: boolean
  error: string | null
}

export interface ActivitiesActions {
  loadActivities: () => Promise<void>
  refreshActivities: () => Promise<void>
}

export interface ActivitiesApiResponse {
  recent_activities: Activity[]
}

// Activity types enum
export enum ActivityType {
  CLIENT = 'client',
  PROJECT = 'project',
  STEP = 'step',
  BILLING = 'billing'
}

// Activity status enum
export enum ActivityStatus {
  CREATED = 'created',
  DONE = 'done',
  SENT = 'sent',
  PAID = 'paid'
}

// Type guards
export function hasAmount(activity: Activity): boolean {
  return activity.amount !== undefined && activity.amount !== null
}

export function hasProjectInfo(activity: Activity): boolean {
  return activity.parent_project !== undefined && activity.parent_project !== null
}

// Activity formatting helpers
export interface ActivityDisplayOptions {
  showAmount?: boolean
  showProject?: boolean
  showClient?: boolean
  maxDescriptionLength?: number
}

// Activity grouping types
export interface ActivityGroup {
  date: string
  activities: Activity[]
}

export interface GroupedActivities {
  today: Activity[]
  yesterday: Activity[]
  thisWeek: Activity[]
  older: Activity[]
}
