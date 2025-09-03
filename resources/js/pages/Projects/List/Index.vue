<template>
    <Head title="Projets" />

    <AppLayout>
        <ProjectsListContainer
            :skeleton-data="skeletonData"
            :projects-data="projectsData"
            @project-deleted="handleProjectDeleted"
            @filters-changed="handleFiltersChanged"
        />
    </AppLayout>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import ProjectsListContainer from './Partials/ProjectsListContainer.vue'
import type { ProjectListProps } from '@/types/projects/list'

interface Props {
    skeletonData: ProjectListProps
    projectsData?: any
}

// eslint-disable-next-line @typescript-eslint/no-unused-vars
const _props = defineProps<Props>()

const handleProjectDeleted = (projectId: number) => {
    console.log('Project deleted:', projectId)
}

const handleFiltersChanged = (filters: any) => {
    console.log('Filters changed:', filters)
}
</script>
