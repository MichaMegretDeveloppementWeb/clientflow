<template>
    <Card class="group relative overflow-hidden rounded-2xl border-0 bg-white shadow-xl shadow-gray-900/5 ring-1 ring-gray-900/5 transition-all duration-300 hover:shadow-2xl hover:shadow-gray-900/10 self-stretch w-full lg:w-[65%]">
        <!-- Gradient overlay -->
        <div class="absolute inset-0 bg-gradient-to-br from-blue-50/20 via-transparent to-purple-50/20 opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>

        <CardHeader class="relative z-10 pb-2">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 shadow-lg shadow-blue-500/25">
                        <OptimizedIcon name="activity" :size="20" class="text-white" preload />
                    </div>
                    <div class="space-y-1">
                        <CardTitle class="text-xl font-bold text-gray-900">
                            Activité récente
                        </CardTitle>
                        <p class="text-sm text-gray-600">
                            Derniers événements et actions
                        </p>
                    </div>
                </div>

                <div class="items-center gap-2 hidden md:flex">
                    <div class="rounded-lg bg-blue-50 px-3 py-1">
                        <span class="text-xs font-medium text-blue-700">
                            {{ recentActivitiesCount }} entrées
                        </span>
                    </div>
                    <div class="h-2 w-2 rounded-full bg-green-500 animate-pulse" v-if="!isLoading"></div>
                </div>
            </div>
        </CardHeader>

        <CardContent class="relative z-10 overflow-hidden px-6 pb-6">
            <!-- Skeleton moderne sans loader -->
            <div v-if="isLoading" class="px-6 pb-6 space-y-3">
                <div v-for="i in 5" :key="i" class="animate-pulse">
                    <div class="flex items-start gap-4 p-4 rounded-xl bg-gradient-to-r from-white/80 to-slate-50">
                        <!-- Avatar/Icon skeleton -->
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-full bg-gradient-to-br from-gray-200 to-gray-300"></div>
                        </div>

                        <!-- Content skeleton -->
                        <div class="flex-1 space-y-3">
                            <!-- Title and timestamp -->
                            <div class="flex items-center justify-between">
                                <div class="h-4 bg-gray-300 rounded-md" :style="{ width: Math.random() * 40 + 50 + '%' }"></div>
                                <div class="h-3 w-20 bg-gray-200 rounded-full"></div>
                            </div>

                            <!-- Description lines -->
                            <div class="space-y-2">
                                <div class="h-3 bg-gray-200 rounded" :style="{ width: Math.random() * 30 + 70 + '%' }"></div>
                                <div class="h-3 bg-gray-200 rounded" :style="{ width: Math.random() * 40 + 40 + '%' }" v-if="Math.random() > 0.3"></div>
                            </div>

                            <!-- Footer with action and meta -->
                            <div class="flex justify-between items-center pt-1">
                                <div class="flex items-center gap-2">
                                    <div class="h-4 w-4 bg-gray-200 rounded"></div>
                                    <div class="h-3 bg-gray-200 rounded w-16"></div>
                                </div>
                                <div class="h-5 w-12 bg-gray-200 rounded-full"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- État vide amélioré -->
            <div v-else-if="!hasRecentActivities" class="py-16 text-center">
                <div class="relative mx-auto mb-6">
                    <div class="absolute inset-0 rounded-full bg-gradient-to-r from-slate-400 to-gray-500 blur-lg opacity-10 animate-pulse"></div>
                    <div class="relative flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-slate-500 to-gray-600 shadow-lg shadow-slate-500/25">
                        <OptimizedIcon name="clock" :size="28" class="text-white" />
                    </div>
                </div>
                <h3 class="mb-2 text-lg font-bold text-gray-900">
                    Premières activités à venir
                </h3>
                <p class="text-sm text-gray-600 max-w-sm mx-auto">
                    Vos prochaines actions et interactions avec les clients apparaîtront ici.
                </p>
                <div class="mt-6 flex justify-center">
                    <div class="rounded-lg bg-blue-50 px-4 py-2">
                        <span class="text-xs font-medium text-blue-700">
                            En attente de nouvelles activités...
                        </span>
                    </div>
                </div>
            </div>

            <!-- Liste des activités moderne avec pagination -->
            <div v-else class="space-y-0">
                <!-- Contrôles de pagination -->
                <div v-if="totalPages > 1" class="flex items-center justify-between mb-4 px-4 py-2 bg-gray-50 rounded-xl max-w-[30em] mx-auto">
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

                <!-- Liste des activités paginées -->
                <div class="p-2 space-y-3 mt-2">
                    <ActivityItem
                        v-for="activity in paginatedActivities"
                        :key="activity.id"
                        :activity="activity"
                        custom-class="rounded-xl bg-white hover:bg-gray-100"
                    />
                </div>
            </div>
        </CardContent>
    </Card>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import OptimizedIcon from '@/components/OptimizedIcon.vue';
import ActivityItem from './ActivityItem.vue';
import { useActivities } from '@/composables/dashboard/useActivities';

// Use the activities composable
const {
  // eslint-disable-next-line @typescript-eslint/no-unused-vars
  activities: _recentActivitiesList,
  isLoading,
  activitiesCount: recentActivitiesCount,
  hasActivities: hasRecentActivities,
  currentPage,
  totalPages,
  paginatedActivities,
  hasPrevPage,
  hasNextPage,
  loadActivities,
  goToPrevPage,
  goToNextPage
} = useActivities();

// Load activities on component mount
onMounted(() => {
  loadActivities();
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

/* Optimisations pour le scroll des activités */
.activity-container {
    contain: layout style paint;
}

/* Animation de glow pour les éléments actifs */
@keyframes glow {
    0%, 100% {
        opacity: 0.2;
    }
    50% {
        opacity: 0.4;
    }
}
</style>
