<template>
    <Head :title="skeletonData.client.name || 'Client'" />

    <AppLayout>
        <ClientDetailContainer
            :skeleton-data="skeletonData"
            :data="data"
        />
    </AppLayout>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import ClientDetailContainer from './Partials/ClientDetailContainer.vue'
import type { ClientDetailProps } from '@/types/clients/detail'

interface Props {
    skeletonData: ClientDetailProps & {
        skeleton_mode?: boolean
    }
    data?: any
}

defineProps<Props>()
</script>
