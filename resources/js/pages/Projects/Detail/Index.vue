<template>
    <Head :title="pageTitle" />

    <AppLayout>
        <ProjectDetailContainer
            :project="project"
            :events="events"
            :financial-stats="financialStats"
            :isLoading="isLoading"
            :hasError="hasError"
            :error="error"
            @update-status="updateStatus"
            @retry="fetchData"
        />
    </AppLayout>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import ProjectDetailContainer from './Partials/ProjectDetailContainer.vue'
import { useProjectDetailManager } from '@/composables/projects/detail/useProjectDetailManager'
import type { ProjectDetailProps } from '@/types/projects/detail'

const props = defineProps<ProjectDetailProps>()

// Utiliser le composable principal pour gérer l'état
const {
    project,
    events,
    financialStats,
    isLoading,
    error,
    hasError,
    fetchData,
    updateStatus,
} = useProjectDetailManager(props.projectId, props.skeletonData)

// Titre de page dynamique avec fallback pour skeleton
const pageTitle = computed(() => {
    if (isLoading.value || !project.value) {
        return 'Détails du projet'
    }
    return project.value.name
})

// Gestion des erreurs (si besoin d'afficher quelque chose de spécial)
// Pour l'instant on laisse les composants gérer leur propre état de chargement
</script>