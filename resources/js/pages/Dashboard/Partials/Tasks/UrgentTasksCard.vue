<template>
    <Card class="flex h-full flex-col rounded-xl border border-gray-200/60 bg-white shadow-sm w-full lg:w-[35%] lg:min-w-[20rem]">
        <CardHeader class="flex-shrink-0 space-y-0 pb-3">
            <div class="flex items-center justify-between">
                <CardTitle class="flex items-center gap-2 text-lg font-semibold text-gray-900">
                    <OptimizedIcon :name="urgentOnly ? 'alert-triangle' : 'list-todo'" :size="20" :class="urgentOnly ? 'text-orange-600' : 'text-blue-600'" preload />
                    {{ urgentOnly ? 'Tâches urgentes' : 'Tâches à faire' }}
                </CardTitle>
                <Badge
                    :variant="urgentTasksCount > 0 ? (urgentOnly ? 'destructive' : 'default') : 'secondary'"
                    class="min-w-[2rem] justify-center"
                >
                    {{ urgentTasksCount }}
                </Badge>
            </div>

            <!-- Checkbox pour filtrer les tâches urgentes -->
            <div class="flex items-center gap-2 pt-2">
                <input
                    id="urgent-filter"
                    type="checkbox"
                    :checked="urgentOnly"
                    @change="toggleUrgentFilter"
                    class="h-4 w-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500"
                />
                <label for="urgent-filter" class="text-sm text-gray-600 cursor-pointer">
                    Afficher uniquement les tâches urgentes
                </label>
            </div>
        </CardHeader>

        <CardContent class="flex flex-1 flex-col p-0">
            <!-- Loading state -->
            <div v-if="isLoading" class="p-6">
                <SkeletonLoader
                    v-for="i in 3"
                    :key="i"
                    shape="text"
                    size="md"
                    class="mb-3 last:mb-0"
                />
            </div>

            <!-- Empty state -->
            <div v-else-if="urgentTasksCount === 0" class="flex flex-1 flex-col items-center justify-center p-6 text-center">
                <div class="mb-3 rounded-full bg-green-50 p-3">
                    <OptimizedIcon name="check-circle" :size="24" class="text-green-600" preload />
                </div>
                <h3 class="mb-1 text-sm font-medium text-gray-900">Aucune tâche urgente</h3>
                <p class="text-xs text-gray-500">Tout est à jour !</p>
            </div>

            <!-- Tasks list -->
            <div v-else class="flex-1 space-y-3 p-6 max-h-[35rem] overflow-auto px-3">
                <UrgentTaskItem
                    v-for="task in urgentTasks"
                    :key="task.id"
                    :task="task"
                    @task-completed="handleTaskCompleted"
                    @invoice-paid="handleInvoicePaid"
                />
            </div>

            <!-- Footer avec lien vers tous les événements -->
            <div v-if="urgentTasksCount > 0" class="flex-shrink-0 border-t border-gray-100 p-4 mt-6">

                <Link
                    v-if="urgentOnly"
                    :href="route('events.index', { overdue: 'true' })"
                    class="group inline-flex w-full items-center justify-center rounded-lg bg-orange-50 px-4 py-2 text-sm font-medium text-orange-700 transition-colors hover:bg-orange-100"
                >
                    <OptimizedIcon name="eye" :size="16" class="mr-2" preload />
                    Voir toutes les tâches urgentes
                    <OptimizedIcon name="arrow-right" :size="16" class="ml-2 transition-transform group-hover:translate-x-0.5" preload />
                </Link>

                <Link
                    v-else
                    :href="route('events.index')"
                    class="group inline-flex w-full items-center justify-center rounded-lg bg-blue-50 px-4 py-2 text-sm font-medium text-blue-700 transition-colors hover:bg-blue-100"
                >
                    <OptimizedIcon name="eye" :size="16" class="mr-2" preload />
                    Voir toutes les tâches à faire
                    <OptimizedIcon name="arrow-right" :size="16" class="ml-2 transition-transform group-hover:translate-x-0.5" preload />
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
import SkeletonLoader from '@/components/skeletons/SkeletonLoader.vue';
import UrgentTaskItem from './UrgentTaskItem.vue';
import { useTasks } from '@/composables/dashboard/useTasks';

// Use the tasks composable
const {
  tasks: urgentTasks,
  isLoading,
  taskCount: urgentTasksCount,
  urgentOnly,
  loadTasks,
  toggleUrgentFilter,
  markTaskCompleted,
  markInvoicePaid
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
