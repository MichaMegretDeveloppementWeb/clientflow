<template>
    <div class="flex min-h-[60vh] flex-col items-center justify-center px-6 py-12">
        <div class="w-full max-w-md text-center">
            <!-- Icône d'erreur -->
            <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-full" :class="getIconClasses()">
                <Icon :name="getIconName()" class="h-8 w-8" :class="getIconColorClasses()" />
            </div>

            <!-- Titre et message -->
            <h1 class="mb-2 text-2xl font-bold text-gray-900">
                {{ error.title }}
            </h1>
            <p class="mb-8 text-gray-600">
                {{ error.message }}
            </p>

            <!-- Actions -->
            <div class="flex flex-col gap-3 sm:flex-row sm:justify-center">
                <Button 
                    variant="default" 
                    @click="handleRetry"
                    class="inline-flex items-center justify-center"
                >
                    <Icon name="refresh-cw" class="mr-2 h-4 w-4" />
                    Réessayer
                </Button>
                
                <Button 
                    variant="outline" 
                    as-child
                >
                    <Link :href="route('clients.index')" class="inline-flex items-center justify-center">
                        <Icon name="arrow-left" class="mr-2 h-4 w-4" />
                        Retour aux clients
                    </Link>
                </Button>
            </div>

            <!-- Code d'erreur (en mode développement) -->
            <p v-if="showErrorCode" class="mt-6 text-sm text-gray-400">
                Code d'erreur: {{ error.code }}
            </p>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import Icon from '@/components/Icon.vue'
import { Button } from '@/components/ui/button'
import { Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

interface ErrorData {
    type: string
    title: string
    message: string
    code: number
}

interface Props {
    error: ErrorData
    showErrorCode?: boolean
}

const props = withDefaults(defineProps<Props>(), {
    showErrorCode: false
})

const emit = defineEmits<{
    retry: []
}>()

const getIconName = () => {
    switch (props.error.type) {
        case 'access_denied':
            return 'shield-x'
        case 'not_found':
            return 'search-x'
        default:
            return 'alert-triangle'
    }
}

const getIconClasses = () => {
    switch (props.error.type) {
        case 'access_denied':
            return 'bg-red-50'
        case 'not_found':
            return 'bg-blue-50'
        default:
            return 'bg-orange-50'
    }
}

const getIconColorClasses = () => {
    switch (props.error.type) {
        case 'access_denied':
            return 'text-red-600'
        case 'not_found':
            return 'text-blue-600'
        default:
            return 'text-orange-600'
    }
}

const handleRetry = () => {
    emit('retry')
}
</script>