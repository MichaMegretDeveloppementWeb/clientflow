<template>
    <Head :title="pageTitle" />

    <AppLayout>
        <div class="space-y-8 max-w-7xl m-auto">
            <!-- Header -->
            <ProjectEditHeader
                :project="project"
                :is-loading="isLoading"
                :has-error="hasError"
            />


            <!-- Form -->
            <ProjectEditForm
                ref="formRef"
                :project="project"
                :is-loading="isLoading"
                :has-error="hasError"
            />
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import ProjectEditHeader from './Partials/ProjectEditHeader.vue'
import ProjectEditForm from './Partials/ProjectEditForm.vue'
import { useProjectEditManager } from '@/composables/projects/edit/useProjectEditManager'
import type { ProjectEditData, ProjectEditSkeletonData } from '@/types/projects/edit'

interface Props {
    projectId: number
    skeletonData: ProjectEditSkeletonData
    data?: ProjectEditData
}

const props = defineProps<Props>()

// Référence du formulaire
const formRef = ref<InstanceType<typeof ProjectEditForm> | null>(null)

// Composable pour la gestion des données
const {
    project,
    isLoading,
    hasError,
} = useProjectEditManager(props.projectId, props.skeletonData, props.data)

// Titre de la page
const pageTitle = computed(() => {
    if (hasError.value) return 'Erreur - Modification projet'
    if (isLoading.value) return 'Modification projet'
    return `Modifier ${project.value?.name || 'projet'}`
})

</script>
