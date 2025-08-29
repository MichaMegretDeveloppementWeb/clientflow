// Activity-related types for dashboard components

export interface Activity {
  id: string
  type: 'urgent_task' | 'completed_task' | 'project_update' | 'client_update'
  title: string
  description: string
  time: string
  icon: string
  color: 'red' | 'green' | 'blue' | 'yellow' | 'purple' | 'orange'
  link?: string
  amount?: number
  project_name?: string
  client_name?: string
  is_urgent: boolean
  metadata?: Record<string, any>
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
  URGENT_TASK = 'urgent_task',
  COMPLETED_TASK = 'completed_task',
  PROJECT_UPDATE = 'project_update',
  CLIENT_UPDATE = 'client_update'
}

// Activity colors enum
export enum ActivityColor {
  RED = 'red',
  GREEN = 'green',
  BLUE = 'blue',
  YELLOW = 'yellow',
  PURPLE = 'purple',
  ORANGE = 'orange'
}

// Type guards
export function isUrgentActivity(activity: Activity): boolean {
  return activity.is_urgent === true || activity.type === ActivityType.URGENT_TASK
}

export function hasAmount(activity: Activity): boolean {
  return activity.amount !== undefined && activity.amount !== null
}

export function hasProjectInfo(activity: Activity): boolean {
  return activity.project_name !== undefined && activity.project_name !== null
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