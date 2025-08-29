// Task-related types for dashboard components

export interface TaskProject {
  id: number
  name: string
  client: {
    id: number
    name: string
  }
}

export interface BaseTask {
  id: number
  name: string
  type: string
  event_type: 'step' | 'billing'
  event_type_label: string
  status: string
  status_label: string
  status_color: string
  project: TaskProject
  is_overdue: boolean
  created_at: string
  updated_at: string
}

export interface StepTask extends BaseTask {
  event_type: 'step'
  execution_date: string
  days_overdue: number | null
}

export interface BillingTask extends BaseTask {
  event_type: 'billing'
  send_date: string
  payment_due_date: string
  amount: number
  formatted_amount: string
  payment_status: 'pending' | 'paid'
  is_payment_overdue: boolean
}

export type Task = StepTask | BillingTask

export interface TasksState {
  tasks: Task[]
  isLoading: boolean
  error: string | null
}

export interface TasksActions {
  loadTasks: () => Promise<void>
  markTaskCompleted: (taskId: number) => Promise<void>
  markInvoicePaid: (taskId: number) => Promise<void>
  refreshTasks: () => Promise<void>
}

export interface TaskApiResponse {
  urgent_tasks: Task[]
}

// Type guards for discriminating between task types
export function isStepTask(task: Task): task is StepTask {
  return task.event_type === 'step'
}

export function isBillingTask(task: Task): task is BillingTask {
  return task.event_type === 'billing'
}

// Task status enums (matching backend)
export enum TaskStatus {
  TODO = 'todo',
  DONE = 'done',
  CANCELLED = 'cancelled',
  TO_SEND = 'to_send',
  SENT = 'sent'
}

export enum PaymentStatus {
  PENDING = 'pending',
  PAID = 'paid'
}

export enum EventType {
  STEP = 'step',
  BILLING = 'billing'
}