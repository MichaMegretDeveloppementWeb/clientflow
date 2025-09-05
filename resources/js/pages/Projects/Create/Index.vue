<template>
    <Head title="Nouveau projet" />

    <AppLayout>
        <div class="space-y-8">
            <!-- Header -->
            <ProjectCreateHeader
                :is-loading="isLoading"
                :has-error="hasError"
            />

            <!-- Form -->
            <ProjectCreateForm
                ref="formRef"
                :clients="clients"
                :selected-client-id="selectedClientId"
                :selected-client="selectedClient"
                :is-loading="isLoading"
                :has-error="hasError"
            />
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import ProjectCreateHeader from './Partials/ProjectCreateHeader.vue'
import ProjectCreateForm from './Partials/ProjectCreateForm.vue'
import { useProjectCreateManager } from '@/composables/projects/create/useProjectCreateManager'
import type { ProjectCreateData, ProjectCreateSkeletonData } from '@/types/projects/create'

interface Props {
    skeletonData: ProjectCreateSkeletonData
    data?: ProjectCreateData
}

const props = defineProps<Props>()

// Référence du formulaire pour réinitialisation
const formRef = ref<InstanceType<typeof ProjectCreateForm> | null>(null)

// State management
const {
    clients,
    selectedClientId,
    selectedClient,
    isLoading,
    hasError
} = useProjectCreateManager(props.skeletonData, props.data)
</script>