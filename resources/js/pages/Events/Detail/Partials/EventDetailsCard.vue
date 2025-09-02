<template>
    <Card v-if="shouldShowDetailsCard" class="border border-gray-200 bg-white shadow-sm">
        <CardHeader class="pb-4">
            <CardTitle class="flex items-center gap-2 text-gray-900">
                <Icon name="file-text" class="h-5 w-5 text-gray-600" />
                Détails
            </CardTitle>
        </CardHeader>
        <CardContent class="space-y-6">
            <!-- Skeleton -->
            <div v-if="isLoading" class="space-y-4">
                <!-- Info cards skeleton -->
                <div class="grid gap-4">
                    <div class="h-16 bg-gray-200 rounded-lg animate-pulse"></div>
                    <div class="h-16 bg-gray-200 rounded-lg animate-pulse"></div>
                </div>
                <!-- Description skeleton -->
                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <div class="h-4 w-4 bg-gray-200 rounded animate-pulse"></div>
                        <div class="h-4 bg-gray-200 rounded w-20 animate-pulse"></div>
                    </div>
                    <div class="space-y-2">
                        <div class="h-4 bg-gray-200 rounded animate-pulse"></div>
                        <div class="h-4 bg-gray-200 rounded w-3/4 animate-pulse"></div>
                        <div class="h-4 bg-gray-200 rounded w-1/2 animate-pulse"></div>
                    </div>
                </div>
            </div>

            <!-- Données réelles -->
            <template v-else-if="event">
                <!-- Informations principales -->
                <div class="grid gap-4">
                    <!-- Catégorie -->
                    <div v-if="event.type" class="flex items-center justify-between p-3 rounded-lg bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100">
                                <Icon name="tag" class="h-4 w-4 text-blue-600" />
                            </div>
                            <span class="text-sm font-medium text-gray-600">Catégorie</span>
                        </div>
                        <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-sm font-medium text-blue-800">
                            {{ event.type }}
                        </span>
                    </div>

                    <!-- Dernière modification -->
                    <div class="flex items-center justify-between p-3 rounded-lg bg-gradient-to-r from-gray-50 to-slate-50 border border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100">
                                <Icon name="clock" class="h-4 w-4 text-gray-600" />
                            </div>
                            <span class="text-sm font-medium text-gray-600">Dernière modification</span>
                        </div>
                        <span class="text-sm font-medium text-gray-800">
                            {{ formatDate(event.updated_at) }}
                        </span>
                    </div>
                </div>

                <!-- Description -->
                <div v-if="event.description" class="space-y-3">
                    <div class="flex items-center gap-2">
                        <Icon name="file-text" class="h-4 w-4 text-gray-400" />
                        <span class="text-sm font-medium text-gray-700">Description</span>
                    </div>
                    <div class="rounded-lg bg-gray-50 p-4 border border-gray-100">
                        <p class="text-gray-700 leading-relaxed">{{ event.description }}</p>
                    </div>
                </div>
            </template>
        </CardContent>
    </Card>
</template>

<script setup>
import { computed, toRef } from 'vue'
import Icon from '@/components/Icon.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { useEventUtils } from '@/composables/events/detail/useEventUtils'

const props = defineProps({
    event: {
        type: Object,
        default: null
    },
    isLoading: {
        type: Boolean,
        default: false
    }
})

const { formatDate } = useEventUtils(toRef(props, 'event'))

// Afficher la carte si il y a des détails à afficher ou en mode skeleton
const shouldShowDetailsCard = computed(() => {
    return props.isLoading || (props.event && (props.event.description || props.event.type))
})
</script>
