import { ref, computed } from 'vue'
import type { Activity, ActivitiesState, ActivitiesActions, ActivitiesApiResponse, GroupedActivities } from '@/types/dashboard/activities'

export function useActivities() {
  // State
  const activities = ref<Activity[]>([])
  const isLoading = ref(true)
  const error = ref<string | null>(null)
  const currentPage = ref(1)
  const itemsPerPage = 6

  // Computed
  const activitiesCount = computed(() => activities.value.length)
  const hasActivities = computed(() => activitiesCount.value > 0)
  const billingActivitiesCount = computed(() =>
    activities.value.filter(activity => activity.type === 'billing').length
  )

  // Pagination computeds
  const totalPages = computed(() => Math.ceil(activities.value.length / itemsPerPage))
  const paginatedActivities = computed(() => {
    const start = (currentPage.value - 1) * itemsPerPage
    const end = start + itemsPerPage
    return activities.value.slice(start, end)
  })
  const hasPrevPage = computed(() => currentPage.value > 1)
  const hasNextPage = computed(() => currentPage.value < totalPages.value)

  // Group activities by time periods
  const groupedActivities = computed((): GroupedActivities => {
    const now = new Date()
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate())
    const yesterday = new Date(today)
    yesterday.setDate(yesterday.getDate() - 1)
    const weekStart = new Date(today)
    weekStart.setDate(weekStart.getDate() - 7)

    const grouped: GroupedActivities = {
      today: [],
      yesterday: [],
      thisWeek: [],
      older: []
    }

    activities.value.forEach(activity => {
      const activityDate = new Date(activity.timestamp)
      const activityDay = new Date(activityDate.getFullYear(), activityDate.getMonth(), activityDate.getDate())

      if (activityDay.getTime() === today.getTime()) {
        grouped.today.push(activity)
      } else if (activityDay.getTime() === yesterday.getTime()) {
        grouped.yesterday.push(activity)
      } else if (activityDay >= weekStart) {
        grouped.thisWeek.push(activity)
      } else {
        grouped.older.push(activity)
      }
    })

    return grouped
  })

  // Actions
  const loadActivities = async (): Promise<void> => {
    isLoading.value = true
    error.value = null

    try {
      const response = await fetch(route('dashboard.recent-activities'), {
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

      const data: ActivitiesApiResponse = await response.json()
      if (data.recent_activities) {
        activities.value = data.recent_activities
      }
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Une erreur est survenue'
      console.error('Erreur lors du chargement des activités récentes:', err)
    } finally {
      isLoading.value = false
    }
  }

  const refreshActivities = async (): Promise<void> => {
    await loadActivities()
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

  // Utility functions
  const formatTime = (time: string): string => {
    const activityDate = new Date(time)
    const now = new Date()
    const diffMs = now.getTime() - activityDate.getTime()
    const diffMinutes = Math.floor(diffMs / (1000 * 60))
    const diffHours = Math.floor(diffMs / (1000 * 60 * 60))
    const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24))

    if (diffMinutes < 1) {
      return 'À l\'instant'
    } else if (diffMinutes < 60) {
      return `Il y a ${diffMinutes}min`
    } else if (diffHours < 24) {
      return `Il y a ${diffHours}h`
    } else if (diffDays === 1) {
      return 'Hier'
    } else if (diffDays < 7) {
      return `Il y a ${diffDays}j`
    } else {
      return activityDate.toLocaleDateString('fr-FR')
    }
  }

  const formatCurrency = (amount: number): string => {
    // Formatage personnalisé avec séparateurs très visibles
    const rounded = Math.round(amount)
    const numberStr = rounded.toString()

    // Utiliser des espaces insécables pour qu'ils soient tous visibles
    const formatted = numberStr.replace(/\B(?=(\d{3})+(?!\d))/g, '\u00A0\u00A0\u00A0') // 3 espaces insécables

    return `${formatted}\u00A0€` // Espace insécable avant €
  }

  const getActivityIconColor = (activity: Activity): string => {
    return activity.icon_color || 'text-gray-600'
  }

  const getActivityBgColor = (activity: Activity): string => {
    switch (activity.type) {
      case 'client': return 'bg-blue-50 ring-blue-200'
      case 'project': return 'bg-purple-50 ring-purple-200'
      case 'step': return 'bg-green-50 ring-green-200'
      case 'billing': return 'bg-emerald-50 ring-emerald-200'
      default: return 'bg-gray-50 ring-gray-200'
    }
  }

  // State object for reactive destructuring
  const state: ActivitiesState = {
    activities: activities.value,
    isLoading: isLoading.value,
    error: error.value,
  }

  // Actions object
  const actions: ActivitiesActions = {
    loadActivities,
    refreshActivities,
  }

  return {
    // State
    activities,
    isLoading,
    error,
    currentPage,

    // Computed
    activitiesCount,
    hasActivities,
    billingActivitiesCount,
    groupedActivities,
    totalPages,
    paginatedActivities,
    hasPrevPage,
    hasNextPage,

    // Actions
    loadActivities,
    refreshActivities,
    goToPrevPage,
    goToNextPage,

    // Utilities
    formatTime,
    formatCurrency,
    getActivityIconColor,
    getActivityBgColor,

    // Grouped exports for convenience
    state,
    actions,
  }
}
