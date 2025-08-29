<template>
    <Card class="border border-gray-200 bg-white shadow-sm  self-stretch w-full lg:w-[65%]">
        <CardHeader class="pb-4">
            <CardTitle class="flex items-center gap-2 text-gray-900">
                <OptimizedIcon name="activity" :size="20" class="text-gray-600" preload />
                Activité récente
            </CardTitle>
        </CardHeader>

        <CardContent class="max-h-[40em] mt-4 py-2 space-y-4 overflow-auto px-3">
            <!-- Loading state -->
            <div v-if="isLoading" class="py-12 text-center">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-50">
                    <OptimizedIcon name="loader-2" :size="32" class="animate-spin text-gray-400" />
                </div>
                <h3 class="mb-1 text-sm font-medium text-gray-900">
                    Chargement des activités...
                </h3>
            </div>

            <!-- État vide -->
            <div v-else-if="!hasRecentActivities" class="py-12 text-center">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-gray-50">
                    <OptimizedIcon name="clock" :size="32" class="text-gray-400" />
                </div>
                <h3 class="mb-1 text-sm font-medium text-gray-900">
                    Aucune activité récente
                </h3>
                <p class="text-xs text-gray-500">
                    Les dernières actions apparaitront ici.
                </p>
            </div>

            <!-- Liste des activités -->
            <div v-else class="space-y-4">
                <!-- Scroll virtualisé pour les grandes listes -->
                <VirtualScrollList
                    v-if="recentActivitiesCount > 15"
                    :items="recentActivitiesList"
                    :item-height="80"
                    :height="560"
                >
                    <template #default="{ item }">
                        <ActivityItem :activity="item" />
                    </template>
                </VirtualScrollList>

                <!-- Liste normale pour les petites listes -->
                <template v-else>
                    <ActivityItem
                        v-for="activity in recentActivitiesList"
                        :key="activity.id"
                        :activity="activity"
                    />
                </template>
            </div>
        </CardContent>
    </Card>
</template>

<script setup lang="ts">
import { onMounted } from 'vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import OptimizedIcon from '@/components/OptimizedIcon.vue';
import VirtualScrollList from '@/components/VirtualScrollList.vue';
import ActivityItem from './ActivityItem.vue';
import { useActivities } from '@/composables/dashboard/useActivities';

// Use the activities composable
const {
  activities: recentActivitiesList,
  isLoading,
  activitiesCount: recentActivitiesCount,
  hasActivities: hasRecentActivities,
  loadActivities
} = useActivities();

// Load activities on component mount
onMounted(() => {
  loadActivities();
});
</script>

<style scoped>
/* Optimisations pour le scroll des activités */
.activity-container {
    contain: layout style paint;
}
</style>
