<template>
    <Head :title="pageTitle" />

    <AppLayout>
        <div class="space-y-8 max-w-7xl m-auto">
            <!-- Header -->
            <ClientEditHeader
                :client="client"
                :is-loading="isLoading"
                :has-error="hasError"
                @reset-form="handleResetForm"
            />

            <!-- Form -->
            <ClientEditForm
                ref="formRef"
                :client="client"
                :is-loading="isLoading"
                :has-error="hasError"
                :errors="error"
            />
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import AppLayout from '@/layouts/AppLayout.vue'
import ClientEditHeader from './Partials/ClientEditHeader.vue'
import ClientEditForm from './Partials/ClientEditForm.vue'
import { useClientEditManager } from '@/composables/clients/edit/useClientEditManager'
import type { ClientEditData, ClientEditSkeletonData } from '@/types/clients/edit'

interface Props {
    clientId: number
    skeletonData: ClientEditSkeletonData
    data?: ClientEditData
}

const props = defineProps<Props>()

// Composable pour la gestion des données
const {
    client,
    isLoading,
    hasError,
    error
} = useClientEditManager(props.clientId, props.skeletonData, props.data)

// Référence vers le formulaire pour pouvoir appeler resetForm
const formRef = ref<InstanceType<typeof ClientEditForm> | null>(null)

// Fonction pour réinitialiser le formulaire depuis le header
const handleResetForm = () => {
    formRef.value?.resetForm?.()
}

// Titre de la page
const pageTitle = computed(() => {
    if (hasError.value) return 'Erreur - Modification client'
    if (isLoading.value) return 'Modification client'
    return `Modifier ${client.value?.name || 'client'}`
})
</script>
