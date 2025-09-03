<template>
    <div v-if="detailManager.hasError.value">
        <ClientDetailErrorPage
            :error="detailManager.globalState.error"
            :show-error-code="false"
            @retry="handleRetry"
        />
    </div>

    <div v-else class="space-y-8">
        <ClientDetailHeader
            :client="detailManager.globalState.client"
            :is-loading="skeletonLoader.showSkeleton(true, detailManager.isLoading.value)"
        />

        <ClientDetailActions
            v-if="!skeletonLoader.showSkeleton(true, detailManager.isLoading.value) && detailManager.globalState.client"
            :client="detailManager.globalState.client"
        />

        <ClientDetailInfo
            :client="detailManager.globalState.client"
            :is-loading="skeletonLoader.showSkeleton(true, detailManager.isLoading.value)"
        />

        <ClientDetailStats
            :client="detailManager.globalState.client"
            :financial-stats="detailManager.globalState.financialStats"
            :is-loading="skeletonLoader.showSkeleton(true, detailManager.isLoading.value)"
        />

        <ClientDetailProjects
            :client="detailManager.globalState.client"
            :projects="detailManager.globalState.projects"
            :events="detailManager.globalState.events"
            :is-loading="skeletonLoader.showSkeleton(true, detailManager.isLoading.value)"
        />
    </div>
</template>

<script setup lang="ts">
import { onMounted, onUnmounted } from 'vue'
import { useClientDetailManager } from '@/composables/clients/detail'
import { useSkeletonLoader } from '@/composables/useSkeletonLoader'
import { useConnectionDetection } from '@/composables/useConnectionDetection'

// Import des sous-composants
import ClientDetailHeader from './ClientDetailHeader.vue'
import ClientDetailActions from './ClientDetailActions.vue'
import ClientDetailInfo from './ClientDetailInfo.vue'
import ClientDetailStats from './ClientDetailStats.vue'
import ClientDetailProjects from './ClientDetailProjects.vue'
import ClientDetailErrorPage from './ClientDetailErrorPage.vue'

import type { ClientDetailProps } from '@/types/clients/detail'

interface Props {
    skeletonData: ClientDetailProps & {
        skeleton_mode?: boolean
    }
    data?: any
}

const props = defineProps<Props>()

// Composables
const detailManager = useClientDetailManager(props.skeletonData.client.id, props.skeletonData)
const connectionDetection = useConnectionDetection()
const skeletonLoader = useSkeletonLoader(
    props.skeletonData.skeleton_mode ?? false,
    connectionDetection.getOptimalSkeletonDelay(),
)

const handleRetry = async () => {
    detailManager.clearError()
    await detailManager.fetchData()
}

onMounted(() => {
    // Auto-load si pas en mode skeleton
})

onUnmounted(() => {
    // Cleanup si nécessaire
})
</script>
