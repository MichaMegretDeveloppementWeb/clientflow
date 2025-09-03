import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import type { Task, TasksState, TasksActions, TaskApiResponse } from '@/types/dashboard/tasks'

export function useTasks() {
  // State
  const tasks = ref<Task[]>([])
  const isLoading = ref(true)
  const error = ref<string | null>(null)
  const urgentOnly = ref(false)
  const currentPage = ref(1)
  const itemsPerPage = 5

  // Computed
  const taskCount = computed(() => tasks.value.length)
  const hasUrgentTasks = computed(() => taskCount.value > 0)
  const overdueTasksCount = computed(() =>
    tasks.value.filter(task => task.is_overdue).length
  )
  
  // Pagination computeds
  const totalPages = computed(() => Math.ceil(tasks.value.length / itemsPerPage))
  const paginatedTasks = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage
    const end = start + itemsPerPage
    return tasks.value.slice(start, end)
  })
  const hasPrevPage = computed(() => currentPage.value > 1)
  const hasNextPage = computed(() => currentPage.value < totalPages.value)

  // Actions
  const loadTasks = async (): Promise<void> => {
    isLoading.value = true
    error.value = null

    try {
      const response = await fetch(route('dashboard.urgent-tasks', { urgent_only: urgentOnly.value }), {
        method: 'GET',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        },
      })

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`)
      }

      const data: TaskApiResponse = await response.json()
      
      if (data.urgent_tasks) {
        tasks.value = data.urgent_tasks
        
        // Si aucune tâche retournée mais pas d'erreur HTTP, peut indiquer un problème backend
        if (data.urgent_tasks.length === 0 && !error.value) {
          // En mode dev, on peut ajouter un log pour débugger
          if (import.meta.env.DEV) {
            console.log('No tasks returned - could be normal or indicate backend issue')
          }
        }
      }
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Une erreur est survenue'
      console.error('Erreur lors du chargement des tâches urgentes:', err)
      tasks.value = []
    } finally {
      isLoading.value = false
    }
  }

  const toggleUrgentFilter = async (): Promise<void> => {
    urgentOnly.value = !urgentOnly.value
    currentPage.value = 1 // Reset to first page when changing filter
    await loadTasks()
  }

  // Pagination actions
  const goToPrevPage = (): void => {
    if (hasPrevPage.value) {
      currentPage.value--
    }
  }

  const goToNextPage = (): void => {
    if (hasNextPage.value) {
      currentPage.value++
    }
  }

  const markTaskCompleted = async (taskId: number): Promise<void> => {
    try {
      await new Promise<void>((resolve, reject) => {
        router.put(route('events.updateStatus', taskId), { status: 'done' }, {
          preserveState: true,
          onSuccess: () => {
            // Reload tasks after successful completion
            loadTasks().then(resolve)
          },
          onError: (errors) => {
            console.error('Erreur lors de la mise à jour:', errors)
            reject(new Error('Erreur lors de la mise à jour'))
          }
        })
      })
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Erreur lors de la mise à jour'
      throw err
    }
  }

  const markInvoicePaid = async (taskId: number): Promise<void> => {
    try {
      await new Promise<void>((resolve, reject) => {
        router.put(route('events.update', taskId), {
          payment_status: 'paid',
          paid_at: new Date().toISOString().split('T')[0]
        }, {
          preserveState: true,
          onSuccess: () => {
            // Reload tasks after successful payment update
            loadTasks().then(resolve)
          },
          onError: (errors) => {
            console.error('Erreur lors de la mise à jour:', errors)
            reject(new Error('Erreur lors de la mise à jour'))
          }
        })
      })
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Erreur lors de la mise à jour'
      throw err
    }
  }

  const refreshTasks = async (): Promise<void> => {
    await loadTasks()
  }

  // State object for reactive destructuring
  const state: TasksState = {
    tasks: tasks.value,
    isLoading: isLoading.value,
    error: error.value,
  }

  // Actions object
  const actions: TasksActions = {
    loadTasks,
    markTaskCompleted,
    markInvoicePaid,
    refreshTasks,
  }

  return {
    // State
    tasks,
    isLoading,
    error,
    urgentOnly,
    currentPage,

    // Computed
    taskCount,
    hasUrgentTasks,
    overdueTasksCount,
    totalPages,
    paginatedTasks,
    hasPrevPage,
    hasNextPage,

    // Actions
    loadTasks,
    toggleUrgentFilter,
    markTaskCompleted,
    markInvoicePaid,
    refreshTasks,
    goToPrevPage,
    goToNextPage,

    // Grouped exports for convenience
    state,
    actions,
  }
}
