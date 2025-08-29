<template>
    <div 
        :class="[
            'flex items-center gap-2 text-sm',
            align === 'right' ? 'justify-end text-right' : 'justify-center text-center',
            compact ? 'min-w-0' : 'min-w-[80px]'
        ]"
    >
        <Icon 
            :name="icon" 
            class="h-4 w-4 text-slate-400 flex-shrink-0" 
        />
        <div class="min-w-0">
            <div class="font-semibold text-slate-900 tabular-nums">{{ value }}</div>
            <div 
                v-if="secondary"
                :class="[
                    'text-xs mt-0.5 truncate',
                    secondaryClass || 'text-slate-400'
                ]"
            >
                {{ secondary }}
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import Icon from '@/components/Icon.vue'

interface Props {
    icon: string
    value: string | number
    secondary?: string
    secondaryClass?: string
    align?: 'center' | 'right'
    compact?: boolean
}

withDefaults(defineProps<Props>(), {
    secondary: undefined,
    secondaryClass: undefined,
    align: 'center',
    compact: false
})
</script>