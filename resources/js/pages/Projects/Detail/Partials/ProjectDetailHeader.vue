<template>
    <!-- Page Header -->
    <div class="border-b border-gray-100 pb-6">
        <!-- Breadcrumb -->
        <div class="mb-4 flex items-center gap-3 text-sm">
            <Link :href="route('dashboard')" class="flex items-center gap-2 text-gray-500 hover:text-gray-700 transition-colors">
                <Icon name="home" class="h-4 w-4" />
                <span>Tableau de bord</span>
            </Link>
            <div class="h-4 w-px bg-gray-300"></div>
            <Link :href="route('projects.index')" class="text-gray-500 transition-colors hover:text-gray-700">
                Projets
            </Link>
        </div>

        <!-- Header avec Title et Meta -->
        <div class="flex items-start gap-4 pb-6">
            <div class="rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 p-3 shadow-sm">
                <Icon name="folder" class="h-6 w-6 text-white" />
            </div>
            <div class="flex-1 min-w-0">
                <!-- Titre avec badge de statut -->
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-2xl font-bold text-gray-900 min-w-35 min-h-8" :class="{ 'animate-pulse bg-gray-200 rounded': isLoading }">
                        {{ isLoading ? '' : project?.name || 'Projet' }}
                    </h1>
                    <span
                        v-if="!isLoading && project"
                        class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-sm font-medium ring-1 ring-inset"
                        :class="{
                            'bg-emerald-50 text-emerald-700 ring-emerald-600/20': project.status === 'active',
                            'bg-blue-50 text-blue-700 ring-blue-600/20': project.status === 'completed',
                            'bg-red-50 text-red-700 ring-red-600/20': project.status === 'on_hold',
                        }"
                    >
                        <div class="h-2 w-2 rounded-full" :class="{
                            'bg-emerald-400': project.status === 'active',
                            'bg-blue-400': project.status === 'completed',
                            'bg-red-400': project.status === 'on_hold',
                        }"></div>
                        {{ getStatusLabel(project.status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Meta informations -->
        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 py-4 border-y border-gray-100">
            <span class="flex items-center gap-1.5">
                <Icon name="folder" class="h-4 w-4 text-blue-500" />
                Projet
            </span>
            <span v-if="!isLoading && project?.client?.id" class="flex items-center gap-1.5">
                <Icon name="user" class="h-4 w-4 text-gray-400" />
                <Link
                    :href="route('clients.show', project.client.id)"
                    class="font-medium hover:text-blue-600"
                >
                    {{ project.client.name }}
                </Link>
            </span>
            <span v-if="!isLoading && project?.budget" class="flex items-center gap-1.5">
                <Icon name="banknote" class="h-4 w-4 text-gray-400" />
                Budget: {{ formatCurrency(project.budget) }}
            </span>
        </div>
    </div>
</template>

<script setup lang="ts">
import Icon from '@/components/Icon.vue'
import { Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import type { ProjectDetailData } from '@/types/projects/detail'
import { useProjectDetailFormatters } from '@/composables/projects/detail/useProjectDetailFormatters'

interface Props {
    project: ProjectDetailData['project'] | null
    isLoading: boolean
}

defineProps<Props>()

const { formatCurrency } = useProjectDetailFormatters()

function getStatusLabel(status: string): string {
    const labels = {
        active: 'Actif',
        completed: 'Terminé',
        on_hold: 'En pause',
    }
    return labels[status as keyof typeof labels] || status
}
</script>
