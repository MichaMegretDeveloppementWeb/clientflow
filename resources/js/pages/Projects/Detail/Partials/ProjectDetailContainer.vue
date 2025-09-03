<template>
    <div class="space-y-6">
        <!-- Page Header -->
        <ProjectDetailHeader :project="project" :isLoading="isLoading" />

        <!-- Gestion des erreurs -->
        <div v-if="hasError" class="rounded-lg bg-red-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <Icon name="x-circle" class="h-5 w-5 text-red-400" />
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        Une erreur est survenue
                    </h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p>{{ error?.message || error?.general || 'Une erreur est survenue lors du chargement des données.' }}</p>
                    </div>
                    <div class="mt-4">
                        <Button
                            variant="outline"
                            size="sm"
                            @click="$emit('retry')"
                        >
                            Réessayer
                        </Button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading Skeleton -->
        <div v-if="isLoading" class="space-y-6">
            <!-- Actions skeleton -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="animate-pulse h-10 bg-gray-200 rounded-md w-24"></div>
                    <div class="animate-pulse h-10 bg-gray-200 rounded-md w-36"></div>
                </div>
            </div>

            <!-- Project info skeleton -->
            <div class="animate-pulse bg-white shadow-sm ring-1 ring-gray-200/60 rounded-lg">
                <div class="border-b border-gray-100 px-6 py-4">
                    <div class="h-5 bg-gray-200 rounded w-32"></div>
                </div>
                <div class="p-6 space-y-6">
                    <!-- Description skeleton -->
                    <div class="space-y-3">
                        <div class="h-4 bg-gray-200 rounded w-20"></div>
                        <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                            <div class="h-4 bg-gray-200 rounded w-full"></div>
                            <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                            <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                        </div>
                    </div>

                    <!-- Dates skeleton -->
                    <div class="grid gap-4 sm:grid-cols-3">
                        <div v-for="i in 3" :key="i" class="space-y-2">
                            <div class="h-4 bg-gray-200 rounded w-24"></div>
                            <div class="flex items-center gap-2">
                                <div class="h-4 w-4 bg-gray-200 rounded"></div>
                                <div class="h-4 bg-gray-200 rounded w-20"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats cards skeleton -->
            <div class="grid gap-4 grid-cols-1 md:gap-6 xl:grid-cols-2 2xl:grid-cols-4">
                <div v-for="i in 4" :key="i" class="animate-pulse">
                    <div class="bg-white shadow-sm ring-1 ring-gray-200/60 rounded-lg">
                        <div class="p-4 sm:p-6">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0 flex-1 space-y-3">
                                    <div class="h-4 bg-gray-200 rounded w-20"></div>
                                    <div class="h-6 bg-gray-200 rounded w-24"></div>
                                </div>
                                <div class="h-12 w-12 bg-gray-200 rounded-full"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline skeleton -->
            <div class="animate-pulse bg-white shadow-sm ring-1 ring-gray-200/60 rounded-lg">
                <div class="border-b border-gray-100 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 bg-gray-200 rounded-lg"></div>
                            <div class="h-5 bg-gray-200 rounded w-40"></div>
                        </div>
                        <div class="h-6 bg-gray-200 rounded-full w-24"></div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="relative pl-10 space-y-6">
                        <!-- Event skeletons -->
                        <div v-for="i in 3" :key="i" class="relative">
                            <!-- Timeline circle -->
                            <div class="absolute left-[-33px] top-8 h-6 w-6 bg-gray-200 rounded-full"></div>

                            <!-- Event content -->
                            <div class="py-4 px-4 space-y-4">
                                <div class="space-y-3">
                                    <!-- Event header -->
                                    <div class="flex items-center gap-2">
                                        <div class="h-4 w-4 bg-gray-200 rounded"></div>
                                        <div class="h-4 bg-gray-200 rounded w-16"></div>
                                        <div class="h-6 bg-gray-200 rounded-full w-20"></div>
                                    </div>

                                    <!-- Event title -->
                                    <div class="h-6 bg-gray-200 rounded w-48"></div>

                                    <!-- Event meta -->
                                    <div class="flex gap-6">
                                        <div class="flex items-center gap-2">
                                            <div class="h-4 w-4 bg-gray-200 rounded"></div>
                                            <div class="h-4 bg-gray-200 rounded w-20"></div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="h-4 w-4 bg-gray-200 rounded"></div>
                                            <div class="h-4 bg-gray-200 rounded w-16"></div>
                                        </div>
                                    </div>

                                    <!-- Event description -->
                                    <div class="space-y-2">
                                        <div class="h-4 bg-gray-200 rounded w-full"></div>
                                        <div class="h-4 bg-gray-200 rounded w-2/3"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Timeline line (not for last item) -->
                            <div v-if="i < 3" class="absolute left-[-22px] top-12 h-[calc(100%+24px)] w-0.5 bg-gray-200"></div>
                        </div>

                        <!-- Project start skeleton -->
                        <div class="relative">
                            <div class="absolute left-[-33px] top-8 h-6 w-6 bg-gray-200 rounded-full"></div>
                            <div class="py-4 px-4">
                                <div class="h-6 bg-gray-200 rounded w-32"></div>
                                <div class="mt-3 flex items-center gap-2">
                                    <div class="h-4 w-4 bg-gray-200 rounded"></div>
                                    <div class="h-4 bg-gray-200 rounded w-20"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div v-else-if="project" class="space-y-6">
            <!-- Actions principales -->
            <ProjectDetailActions :project="project" />

            <!-- Détails du projet -->
            <ProjectDetailInfo :project="project" />

            <!-- Cards de métriques financières -->
            <ProjectDetailStats :project="project" :financial-stats="props.financialStats" />

            <!-- Timeline des événements -->
            <ProjectDetailTimeline :project="project" :events="events || []" />
        </div>
    </div>
</template>

<script setup lang="ts">
import Icon from '@/components/Icon.vue'
import { Button } from '@/components/ui/button'
// import { Link } from '@inertiajs/vue3' // Réservé pour futurs liens
// import { route } from 'ziggy-js' // Réservé pour futures routes
import type { ProjectDetailData } from '@/types/projects/detail'
import type { Event } from '@/types/projects/events'

// Import des composants détaillés
import ProjectDetailHeader from './ProjectDetailHeader.vue'
import ProjectDetailActions from './ProjectDetailActions.vue'
import ProjectDetailStats from './ProjectDetailStats.vue'
import ProjectDetailInfo from './ProjectDetailInfo.vue'
import ProjectDetailTimeline from './ProjectDetailTimeline.vue'

interface Props {
    project: ProjectDetailData['project'] | null
    events?: Event[]
    financialStats?: any | null
    isLoading: boolean
    hasError: boolean
    error: Record<string, string> | null
}

const props = defineProps<Props>()

defineEmits<{
    'update-status': [status: string]
    retry: []
}>()
</script>
