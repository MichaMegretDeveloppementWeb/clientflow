<template>
    <Card class="border-0 bg-white shadow-sm ring-1 ring-gray-200/60 gap-0">
        <CardHeader class="border-b border-gray-100">
            <CardTitle class="text-lg font-semibold text-gray-900">Détails du projet</CardTitle>
        </CardHeader>
        <CardContent class="p-6">
            <!-- Description -->
            <div v-if="project.description" class="mb-8">
                <h4 class="text-md font-medium text-gray-900 mb-3">Description</h4>
                <div class="bg-gray-50 rounded-md p-4 sm:py-8">
                    <p class="text-gray-700 leading-relaxed whitespace-pre-line">{{ project.description }}</p>
                </div>
            </div>

            <!-- Dates et infos -->
            <div class="grid gap-8 sm:grid-cols-3 p-4">
                <div v-if="project.start_date">
                    <h4 class="text-sm font-medium text-gray-900 mb-2">Date de début</h4>
                    <div class="flex items-center gap-2 text-gray-600">
                        <Icon name="calendar" class="h-4 w-4" />
                        <span class="text-sm">{{ formatDate(project.start_date) }}</span>
                    </div>
                </div>

                <div v-if="project.end_date">
                    <h4 class="text-sm font-medium text-gray-900 mb-2">Date de fin prévue</h4>
                    <div class="flex items-center gap-2 text-gray-600">
                        <Icon name="flag" class="h-4 w-4" />
                        <span class="text-sm">{{ formatDate(project.end_date) }}</span>
                    </div>
                </div>

                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-2">Dernière modification</h4>
                    <div class="flex items-center gap-2 text-gray-600">
                        <Icon name="clock" class="h-4 w-4" />
                        <span class="text-sm">{{ formatDate(project.updated_at) }}</span>
                    </div>
                </div>
            </div>
        </CardContent>
    </Card>
</template>

<script setup lang="ts">
import Icon from '@/components/Icon.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import type { ProjectDetailData } from '@/types/projects/detail'
import { useProjectDetailFormatters } from '@/composables/projects/detail/useProjectDetailFormatters'

interface Props {
    project: ProjectDetailData['project']
}

defineProps<Props>()

const { formatDate } = useProjectDetailFormatters()
</script>
