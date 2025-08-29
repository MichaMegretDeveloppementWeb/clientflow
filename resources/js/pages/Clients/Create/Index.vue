<template>
    <Head title="Créer un client" />

    <AppLayout>
        <div class="space-y-8">
            <!-- Header -->
            <ClientCreateHeader
                :is-loading="isLoading"
                :has-error="hasError"
            />

            <!-- Form -->
            <ClientCreateForm
                :is-loading="isLoading"
                :has-error="hasError"
                :client-data="client"
            />
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import ClientCreateHeader from './Partials/ClientCreateHeader.vue'
import ClientCreateForm from './Partials/ClientCreateForm.vue'
import { useClientCreateManager } from '@/composables/clients/create/useClientCreateManager'
import type { ClientCreateSkeletonData, ClientCreateData } from '@/types/clients/create'

interface Props {
    skeletonData: ClientCreateSkeletonData
    data?: ClientCreateData
}

const props = defineProps<Props>()

// State management
const {
    client,
    isLoading,
    hasError
} = useClientCreateManager(props.skeletonData, props.data)
</script>