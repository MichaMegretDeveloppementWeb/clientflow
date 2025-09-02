<template>
    <Card class="border border-gray-200 bg-white shadow-sm">
        <CardHeader class="pb-4">
            <CardTitle class="flex items-center gap-2 text-gray-900">
                <Icon name="calendar" class="h-5 w-5 text-gray-600" />
                Timeline
            </CardTitle>
        </CardHeader>
        <CardContent>
            <!-- Skeleton -->
            <div v-if="isLoading" class="relative space-y-6">
                <div class="absolute left-4 top-4 bottom-4 w-0.5 bg-gray-200 -ml-px"></div>
                <div v-for="i in 3" :key="i" class="relative flex items-start gap-4">
                    <div class="h-8 w-8 bg-gray-200 rounded-full animate-pulse"></div>
                    <div class="flex-1 min-w-0 pt-1 space-y-1">
                        <div class="h-4 bg-gray-200 rounded w-32 animate-pulse"></div>
                        <div class="h-3 bg-gray-200 rounded w-24 animate-pulse"></div>
                    </div>
                </div>
            </div>

            <!-- Données réelles -->
            <div v-else-if="event" class="relative space-y-6">
                <!-- Ligne continue de timeline -->
                <div class="absolute left-4 top-4 bottom-4 w-0.5 bg-gray-200 -ml-px"></div>

                <!-- Timeline items triés par date chronologique -->
                <template v-for="item in getTimelineItems" :key="item.key">
                    <div class="relative flex items-start gap-4">
                        <div class="relative z-10 flex h-8 w-8 items-center justify-center rounded-full" :class="item.bgClass">
                            <Icon :name="item.icon" class="h-4 w-4" :class="item.iconClass" />
                        </div>
                        <div class="flex-1 min-w-0 pt-1">
                            <div class="font-medium text-gray-900">
                                {{ item.title }}
                                <span v-if="item.delay" class="ml-2 text-red-600 text-sm">({{ item.delay }})</span>
                            </div>
                            <div class="text-sm text-gray-600">{{
                                item.key === 'created' || item.key === 'completed'
                                    ? formatDate(item.date)
                                    : formatDate(item.date)
                            }}</div>
                        </div>
                    </div>
                </template>
            </div>
        </CardContent>
    </Card>
</template>

<script setup>
import { toRef } from 'vue'
import Icon from '@/components/Icon.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { useEventUtils } from '@/composables/events/detail/useEventUtils'
import { useEventTimeline } from '@/composables/events/detail/useEventTimeline'

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

const { formatDateTime, formatDate } = useEventUtils(toRef(props, 'event'))
const { getTimelineItems } = useEventTimeline(toRef(props, 'event'))
</script>
