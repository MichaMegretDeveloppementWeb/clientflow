<template>
    <Card class="group relative overflow-hidden rounded-2xl border-0 bg-gradient-to-br from-white to-gray-50/50 shadow-xl shadow-gray-900/5 ring-1 ring-gray-900/5 transition-all duration-300 hover:shadow-2xl hover:shadow-gray-900/10 self-stretch w-full lg:w-[35%] lg:min-w-[20rem]">
        <!-- Subtle gradient overlay -->
        <div class="absolute inset-0 bg-gradient-to-br from-blue-50/30 via-transparent to-orange-50/30 opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>

        <CardHeader class="relative z-10 flex-shrink-0 space-y-0 pb-0">
            <div class="flex items-start justify-between">
                <div class="flex items-start gap-3">
                    <div :class="[
                        'flex h-12 w-12 items-center justify-center rounded-xl transition-all duration-200',
                        urgentOnly ? 'bg-gradient-to-br from-orange-500 to-red-600 shadow-lg shadow-orange-500/25' : 'bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg shadow-blue-500/25'
                    ]">
                        <OptimizedIcon :name="urgentOnly ? 'alert-triangle' : 'list-todo'" :size="20" class="text-white" preload />
                    </div>
                    <div class="space-y-1">
                        <CardTitle class="text-xl font-bold text-gray-900">
                            {{ urgentOnly ? 'Tâches urgentes' : 'Tâches à faire' }}
                        </CardTitle>
                        <p class="text-sm text-gray-600">
                            {{ urgentOnly ? 'Actions requises rapidement' : 'Vos prochaines actions' }}
                        </p>
                    </div>
                </div>

                <div class="flex flex-col items-end gap-2">
                    <Badge
                        class="h-8 min-w-[2.5rem] justify-center text-sm font-semibold shadow-sm"
                        :class="urgentOnly ? 'bg-destructive' : 'bg-blue-600'"
                    >
                        {{ urgentTasksCount }}
                    </Badge>
                    <div class="text-xs text-gray-500">
                        {{ urgentTasksCount === 0 ? 'Aucune' : urgentTasksCount === 1 ? 'tâche' : 'tâches' }}
                    </div>
                </div>
            </div>

            <!-- Toggle moderne -->
            <label for="urgent-filter" class="mt-6 flex items-center justify-start gap-4 cursor-pointer transition-colors duration-200">
                <div class="flex items-center gap-3">
                    <span class="text-sm font-medium text-gray-700">
                        En retard seulement
                    </span>
                </div>
                <div class="relative">
                    <input
                        id="urgent-filter"
                        type="checkbox"
                        :checked="urgentOnly"
                        @change="toggleUrgentFilter"
                        class="peer sr-only"
                    />
                    <div class="h-6 w-11 rounded-full bg-gray-200 peer-checked:bg-orange-500 peer-focus:ring-2 peer-focus:ring-orange-300 transition-all duration-200"></div>
                    <div class="absolute left-1 top-1 h-4 w-4 rounded-full bg-white shadow-sm transition-transform duration-200 peer-checked:translate-x-5"></div>
                </div>
            </label>
        </CardHeader>

        <CardContent class="relative z-10 flex flex-1 flex-col p-0">
            <!-- Loading state avec skeleton amélioré -->
            <div v-if="isLoading" class="px-6 pb-6 space-y-3">
                <div v-for="i in 4" :key="i" class="animate-pulse">
                    <div class="flex items-start gap-4 p-4 rounded-xl bg-gradient-to-r from-gray-50 to-gray-100">
                        <!-- Icon skeleton -->
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-lg bg-gray-300"></div>
                        </div>

                        <!-- Content skeleton -->
                        <div class="flex-1 space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="h-4 bg-gray-300 rounded-md w-2/3"></div>
                                <div class="h-5 w-16 bg-gray-300 rounded-full"></div>
                            </div>
                            <div class="space-y-2">
                                <div class="h-3 bg-gray-200 rounded w-full"></div>
                                <div class="h-3 bg-gray-200 rounded w-4/5"></div>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="h-3 bg-gray-200 rounded w-1/3"></div>
                                <div class="h-6 w-20 bg-gray-200 rounded"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty state amélioré -->
            <div v-else-if="urgentTasksCount === 0" class="flex flex-1 flex-col items-center justify-center p-8 text-center">
                <div class="relative mb-6">
                    <div class="absolute inset-0 rounded-full bg-gradient-to-r from-green-400 to-emerald-500 blur-lg opacity-20 animate-pulse"></div>
                    <div class="relative flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-green-500 to-emerald-600 shadow-lg shadow-green-500/25">
                        <OptimizedIcon name="check-circle" :size="28" class="text-white" preload />
                    </div>
                </div>
                <h3 class="mb-2 text-lg font-bold text-gray-900">Parfait !</h3>
                <p class="text-sm text-gray-600 max-w-xs">
                    {{ urgentOnly ? 'Aucune tâche urgente en attente.' : 'Toutes vos tâches sont à jour.' }}
                </p>
                <p class="text-xs text-gray-500 mt-1">
                    Profitez de ce moment de tranquillité ! 🎉
                </p>
            </div>

            <!-- Tasks list moderne avec pagination -->
            <div v-else class="flex-1 px-6 pb-6">
                <!-- Contrôles de pagination -->
                <div v-if="totalPages > 1" class="flex items-center justify-between mb-4 px-4 py-2 bg-gray-50 rounded-xl">
                    <button
                        @click="goToPrevPage"
                        :disabled="!hasPrevPage"
                        :class="[
                            'flex items-center justify-center w-8 h-8 rounded-lg transition-all duration-200',
                            hasPrevPage
                                ? 'bg-white shadow-sm hover:shadow-md text-gray-700 hover:text-gray-900 border border-gray-200 cursor-pointer'
                                : 'bg-gray-100 text-gray-400 cursor-initial'
                        ]"
                    >
                        <OptimizedIcon name="chevron-left" :size="16" />
                    </button>

                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-gray-700">
                            {{ currentPage }} / {{ totalPages }}
                        </span>
                    </div>

                    <button
                        @click="goToNextPage"
                        :disabled="!hasNextPage"
                        :class="[
                            'flex items-center justify-center w-8 h-8 rounded-lg transition-all duration-200',
                            hasNextPage
                                ? 'bg-white shadow-sm hover:shadow-md text-gray-700 hover:text-gray-900 border border-gray-200 cursor-pointer'
                                : 'bg-gray-100 text-gray-400 cursor-initial'
                        ]"
                    >
                        <OptimizedIcon name="chevron-right" :size="16" />
                    </button>
                </div>

                <!-- Liste des tâches paginées -->
                <div class="space-y-3">
                    <UrgentTaskItem
                        v-for="task in paginatedTasks"
                        :key="task.id"
                        :task="task"
                        @task-completed="handleTaskCompleted"
                        @invoice-paid="handleInvoicePaid"
                    />
                </div>
            </div>

            <!-- Footer moderne -->
            <div v-if="urgentTasksCount > 0" class="relative z-10 p-6 pt-0">
                <div class="h-px bg-gradient-to-r from-transparent via-gray-200 to-transparent mb-6"></div>

                <Link
                    v-if="urgentOnly"
                    :href="route('events.index', { status: 'overdue' })"
                    class="group/button flex w-full items-center justify-center gap-3 rounded-xl bg-gradient-to-r from-orange-500 to-red-600 px-6 py-3 text-sm font-semibold text-white transition-colors duration-200 hover:from-orange-600 hover:to-red-700"
                >
                    <OptimizedIcon name="eye" :size="16" preload />
                    <span>Voir toutes les tâches en retard</span>
                    <div class="transform transition-transform duration-300 ease-out group-hover/button:translate-x-1 flex items-center justify-center">
                        <OptimizedIcon name="arrow-right" :size="16" preload />
                    </div>
                </Link>

                <Link
                    v-else
                    :href="route('events.index', { status: 'todo' })"
                    class="group/button flex w-full items-center justify-center gap-3 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-3 text-sm font-semibold text-white transition-colors duration-200 hover:from-blue-600 hover:to-indigo-700"
                >
                    <OptimizedIcon name="eye" :size="16" preload />
                    <span>Voir toutes les tâches à faire</span>
                    <div class="transform transition-transform duration-300 ease-out group-hover/button:translate-x-1 flex items-center justify-center">
                        <OptimizedIcon name="arrow-right" :size="16" preload />
                    </div>
                </Link>
            </div>
        </CardContent>
    </Card>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import { Link } from '@inertiajs/vue3';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import OptimizedIcon from '@/components/OptimizedIcon.vue';
// import SkeletonLoader from '@/components/skeletons/SkeletonLoader.vue'; // Skeleton intégré dans le template
import UrgentTaskItem from './UrgentTaskItem.vue';
import { useTasks } from '@/composables/dashboard/useTasks';

// Use the tasks composable
const {
  // eslint-disable-next-line @typescript-eslint/no-unused-vars
  tasks: _urgentTasks,
  isLoading,
  taskCount: urgentTasksCount,
  urgentOnly,
  currentPage,
  totalPages,
  paginatedTasks,
  hasPrevPage,
  hasNextPage,
  loadTasks,
  toggleUrgentFilter,
  markTaskCompleted,
  markInvoicePaid,
  goToPrevPage,
  goToNextPage
} = useTasks();

// Event handlers that use the composable actions
const handleTaskCompleted = (taskId: number) => {
    markTaskCompleted(taskId);
};

const handleInvoicePaid = (taskId: number) => {
    markInvoicePaid(taskId);
};

// Load tasks on component mount
onMounted(() => {
    loadTasks();
});
</script>

<style scoped>
/* Custom scrollbar styles */
.scrollbar-thin {
    scrollbar-width: thin;
}

.scrollbar-thumb-gray-300::-webkit-scrollbar-thumb {
    background-color: rgb(209 213 219);
    border-radius: 0.5rem;
}

.scrollbar-track-gray-100::-webkit-scrollbar-track {
    background-color: rgb(243 244 246);
}

::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: rgb(243 244 246);
    border-radius: 0.5rem;
}

::-webkit-scrollbar-thumb {
    background: rgb(209 213 219);
    border-radius: 0.5rem;
}

::-webkit-scrollbar-thumb:hover {
    background: rgb(156 163 175);
}
</style>
