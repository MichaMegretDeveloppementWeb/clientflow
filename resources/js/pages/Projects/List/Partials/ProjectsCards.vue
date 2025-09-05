<template>
    <Card class="border border-slate-200 bg-white shadow-sm p-0">
        <CardContent class="p-0">
            <div v-if="isLoading" class="divide-y divide-slate-100">
                <ProjectCardSkeleton
                    v-for="i in 5"
                    :key="i"
                />
            </div>

            <div v-else-if="projects.length > 0" class="divide-y divide-slate-100">
                <ProjectCard
                    v-for="project in projects"
                    :key="project.id"
                    :project="project"
                    @project-click="handleProjectClick"
                    @view="handleView"
                    @edit="handleEdit"
                    @add-event="handleAddEvent"
                    @delete="handleDelete"
                />
            </div>

        </CardContent>
    </Card>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import { Card, CardContent } from '@/components/ui/card'
// import Icon from '@/components/Icon.vue' // Réservé pour états vides
import { route } from 'ziggy-js'
import type { ProjectDTO } from '@/types/models'
import ProjectCardSkeleton from './ProjectCardSkeleton.vue'
import ProjectCard from './ProjectCard.vue'

interface Props {
    projects: ProjectDTO[]
    isLoading: boolean
    hasActiveFilters: boolean
}

defineProps<Props>()

const emit = defineEmits<{
    'project-click': [project: ProjectDTO]
    'project-delete': [projectId: number]
}>()

// Handlers qui délèguent aux événements du parent
const handleProjectClick = (project: ProjectDTO) => {
    emit('project-click', project)
    router.visit(route('projects.show', { project: project.id }))
}

const handleView = (project: ProjectDTO) => {
    router.visit(route('projects.show', { project: project.id }))
}

const handleEdit = (project: ProjectDTO) => {
    router.visit(route('projects.edit', { project: project.id }))
}

const handleAddEvent = (project: ProjectDTO) => {
    router.visit(route('events.create', { project_id: project.id }))
}

const handleDelete = (projectId: number) => {
    emit('project-delete', projectId)
}
</script>
