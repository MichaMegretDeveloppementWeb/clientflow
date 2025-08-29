<template>
    <Card
        :class="[
            'h-full transition-all duration-200 border-none',
            isClickable && !isActive && 'cursor-pointer hover:-translate-y-0.5 hover:shadow-md',
            isActive && 'ring-2 cursor-default',
            colorClasses.ring
        ]"
        @click="handleClick"
    >
        <CardContent class="flex h-full flex-col p-2.5 sm:p-3">
            <div class="flex flex-1 items-start justify-between">
                <div class="flex h-full flex-1 flex-col justify-between">
                    <div class="mb-1.5 flex items-center gap-2">
                        <div
                            :class="[
                                'rounded-md p-1.5 transition-transform',
                                colorClasses.bg,
                                isClickable && 'group-hover:scale-110'
                            ]"
                        >
                            <Icon
                                :name="icon"
                                :class="['h-3.5 w-3.5', colorClasses.icon]"
                            />
                        </div>
                        <span class="text-xs font-medium text-gray-600 sm:text-sm">
                            {{ title }}
                        </span>
                    </div>
                    <div class="mt-auto">
                        <div v-if="isLoading" class="flex items-center gap-2">
                            <div class="h-6 w-16 animate-pulse rounded bg-gray-200 sm:h-7 sm:w-20"></div>
                        </div>
                        <div v-else class="text-xl font-bold text-gray-900 sm:text-2xl">
                            {{ formattedValue }}
                        </div>
                        <div v-if="subtitle" class="text-xs text-gray-500">
                            {{ subtitle }}
                        </div>
                    </div>
                </div>
                <div v-if="trend" class="flex items-center gap-1">
                    <Icon
                        :name="trend > 0 ? 'trending-up' : 'trending-down'"
                        :class="[
                            'h-4 w-4',
                            trend > 0 ? 'text-green-500' : 'text-red-500'
                        ]"
                    />
                    <span
                        :class="[
                            'text-sm font-medium',
                            trend > 0 ? 'text-green-600' : 'text-red-600'
                        ]"
                    >
                        {{ Math.abs(trend) }}%
                    </span>
                </div>
            </div>
        </CardContent>
    </Card>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { Card, CardContent } from '@/components/ui/card'
import Icon from '@/components/Icon.vue'

/**
 * Composant de carte statistique réutilisable
 * Responsabilités :
 * - Affichage uniforme des statistiques
 * - Support des états (loading, active, clickable)
 * - Formatage automatique des valeurs
 */

interface Props {
    title: string
    value: number | string
    icon: string
    color?: 'blue' | 'green' | 'amber' | 'purple' | 'red' | 'gray'
    isActive?: boolean
    isLoading?: boolean
    isClickable?: boolean
    subtitle?: string
    trend?: number
    format?: 'number' | 'currency' | 'percentage'
}

const props = withDefaults(defineProps<Props>(), {
    color: 'blue',
    isActive: false,
    isLoading: false,
    isClickable: false,
    format: 'number'
})

const emit = defineEmits<{
    click: []
}>()

const colorClasses = computed(() => {
    const colorMap = {
        blue: {
            bg: 'bg-blue-50',
            icon: 'text-blue-600',
            ring: 'ring-blue-400'
        },
        green: {
            bg: 'bg-green-50',
            icon: 'text-green-600',
            ring: 'ring-green-400'
        },
        amber: {
            bg: 'bg-amber-50',
            icon: 'text-amber-600',
            ring: 'ring-amber-400'
        },
        purple: {
            bg: 'bg-purple-50',
            icon: 'text-purple-600',
            ring: 'ring-purple-400'
        },
        red: {
            bg: 'bg-red-50',
            icon: 'text-red-600',
            ring: 'ring-red-400'
        },
        gray: {
            bg: 'bg-gray-50',
            icon: 'text-gray-600',
            ring: 'ring-gray-400'
        }
    }

    return colorMap[props.color] || colorMap.blue
})

const formattedValue = computed(() => {
    if (typeof props.value === 'string') return props.value

    switch (props.format) {
        case 'currency':
            return new Intl.NumberFormat('fr-FR', {
                style: 'currency',
                currency: 'EUR',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(props.value)

        case 'percentage':
            return `${props.value}%`

        default:
            return new Intl.NumberFormat('fr-FR').format(props.value)
    }
})

const handleClick = () => {
    if (props.isClickable && !props.isLoading && !props.isActive) {
        emit('click')
    }
}
</script>
