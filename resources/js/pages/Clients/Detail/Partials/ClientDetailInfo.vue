<template>
    <Card class="border border-gray-200 bg-white shadow-sm">
        <CardHeader class="pb-4">
            <CardTitle class="flex items-center gap-2 text-gray-900">
                <Icon name="user" class="h-5 w-5 text-gray-600" />
                Informations client
            </CardTitle>
        </CardHeader>
        <CardContent class="space-y-8">
            <div v-if="isLoading" class="animate-pulse">
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="space-y-2">
                        <div class="h-4 w-20 bg-gray-200 rounded"></div>
                        <div class="h-5 w-32 bg-gray-200 rounded"></div>
                    </div>
                    <div class="space-y-2">
                        <div class="h-4 w-16 bg-gray-200 rounded"></div>
                        <div class="h-5 w-40 bg-gray-200 rounded"></div>
                    </div>
                    <div class="space-y-2">
                        <div class="h-4 w-24 bg-gray-200 rounded"></div>
                        <div class="h-5 w-28 bg-gray-200 rounded"></div>
                    </div>
                </div>
            </div>
            <div v-else-if="client">
                <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    <div v-if="client.company" class="space-y-2">
                        <label class="flex items-center gap-1.5 text-sm font-medium text-gray-600">
                            <Icon name="building" class="h-4 w-4 text-gray-400" />
                            Entreprise
                        </label>
                        <p class="font-medium text-gray-900">{{ client.company }}</p>
                    </div>
                    <div v-if="client.email" class="space-y-2">
                        <label class="flex items-center gap-1.5 text-sm font-medium text-gray-600">
                            <Icon name="mail" class="h-4 w-4 text-gray-400" />
                            Email
                        </label>
                        <p class="font-medium text-gray-900">{{ client.email }}</p>
                    </div>
                    <div v-if="client.phone" class="space-y-2">
                        <label class="flex items-center gap-1.5 text-sm font-medium text-gray-600">
                            <Icon name="phone" class="h-4 w-4 text-gray-400" />
                            Téléphone
                        </label>
                        <p class="font-medium text-gray-900">{{ client.phone }}</p>
                    </div>
                </div>
                <div v-if="client.address" class="space-y-2 mt-8">
                    <label class="flex items-center gap-1.5 text-sm font-medium text-gray-600">
                        <Icon name="map-pin" class="h-4 w-4 text-gray-400" />
                        Adresse
                    </label>
                    <p class="rounded-lg border border-gray-200 bg-gray-50 p-3 text-sm leading-relaxed whitespace-pre-line">
                        {{ client.address }}
                    </p>
                </div>
                <div v-if="client.notes" class="space-y-2 mt-8">
                    <label class="flex items-center gap-1.5 text-sm font-medium text-gray-600">
                        <Icon name="sticky-note" class="h-4 w-4 text-gray-400" />
                        Notes
                    </label>
                    <p class="rounded-lg border border-gray-200 bg-gray-50 p-3 text-sm leading-relaxed whitespace-pre-line">
                        {{ client.notes }}
                    </p>
                </div>
            </div>
        </CardContent>
    </Card>
</template>

<script setup lang="ts">
import Icon from '@/components/Icon.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import type { ClientDetailInfoProps } from '@/types/clients/detail/index'

defineProps<ClientDetailInfoProps>()
</script>