<template>
    <div class="rounded-lg bg-red-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <Icon name="x-circle" class="h-5 w-5 text-red-400" />
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">
                    Une erreur est survenue
                </h3>
                <div class="mt-2 text-sm text-red-700">
                    <p v-if="typeof error === 'string'">{{ error }}</p>
                    <p v-else-if="error && typeof error === 'object'">
                        {{ error.general || Object.values(error)[0] || 'Erreur inconnue' }}
                    </p>
                    <p v-else>Erreur lors du chargement des données</p>
                </div>
                <div class="mt-4">
                    <Button
                        variant="outline"
                        size="sm"
                        @click="$emit('retry')"
                    >
                        Réessayer
                    </Button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import Icon from '@/components/Icon.vue'
import { Button } from '@/components/ui/button'
import type { ClientDetailErrorProps } from '@/types/clients/detail/index'

defineProps<ClientDetailErrorProps>()
defineEmits<{
    retry: []
}>()
</script>