<template>
    <button
        :class="[
            'inline-flex items-center justify-center p-1.5 rounded-md transition-all duration-200 opacity-0 group-hover:opacity-100',
            variantClasses
        ]"
        :title="tooltip"
        v-bind="$attrs"
    >
        <Icon :name="icon" class="h-4 w-4" />
    </button>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import Icon from '@/components/Icon.vue'

interface Props {
    icon: string
    variant?: 'ghost' | 'primary'
    tooltip?: string
}

const props = withDefaults(defineProps<Props>(), {
    variant: 'ghost',
    tooltip: undefined
})

const variantClasses = computed(() => {
    const classes = {
        ghost: 'text-slate-400 hover:text-slate-600 hover:bg-slate-100',
        primary: 'text-blue-600 hover:text-blue-700 hover:bg-blue-50'
    }
    return classes[props.variant]
})
</script>