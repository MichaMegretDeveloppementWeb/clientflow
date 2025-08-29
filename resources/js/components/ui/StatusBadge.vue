<template>
    <span 
        :class="[
            'inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md text-xs font-medium transition-all duration-200',
            badgeClasses
        ]"
    >
        <Icon 
            v-if="icon" 
            :name="icon" 
            class="h-3 w-3" 
        />
        <span v-if="count !== null">{{ count }}</span>
    </span>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import Icon from '@/components/Icon.vue'

interface Props {
    type: 'success' | 'danger' | 'warning' | 'info'
    icon?: string
    count?: number | null
}

const props = withDefaults(defineProps<Props>(), {
    icon: undefined,
    count: null
})

const badgeClasses = computed(() => {
    const classes = {
        success: 'bg-emerald-50 text-emerald-700 border border-emerald-200/60',
        danger: 'bg-red-50 text-red-700 border border-red-200/60',
        warning: 'bg-amber-50 text-amber-700 border border-amber-200/60',
        info: 'bg-blue-50 text-blue-700 border border-blue-200/60'
    }
    return classes[props.type]
})
</script>