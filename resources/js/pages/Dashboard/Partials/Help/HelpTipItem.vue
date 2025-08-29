<template>
    <div 
        class="flex items-center gap-3 p-2 rounded-lg transition-colors cursor-pointer"
        :class="tipClasses"
        @click="$emit('click')"
    >
        <!-- Icône de statut -->
        <div 
            class="flex-shrink-0 rounded-full p-1"
            :class="statusClasses"
        >
            <OptimizedIcon 
                :name="completed ? 'check' : icon" 
                :size="12"
                :class="iconClasses"
                preload
            />
        </div>

        <!-- Texte du conseil -->
        <span 
            class="flex-1 text-xs"
            :class="textClasses"
        >
            {{ text }}
        </span>

        <!-- Flèche de navigation -->
        <OptimizedIcon 
            name="arrow-right" 
            :size="12"
            class="flex-shrink-0 text-blue-400 transition-transform group-hover:translate-x-0.5"
        />
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import OptimizedIcon from '@/components/OptimizedIcon.vue'

interface Props {
    icon: string
    text: string
    completed: boolean
}

const props = defineProps<Props>()

defineEmits<{
    click: []
}>()

const tipClasses = computed(() => {
    const baseClasses = 'group hover:bg-blue-100/50'
    
    if (props.completed) {
        return `${baseClasses} bg-blue-100/30`
    }
    
    return `${baseClasses} hover:bg-blue-100/50`
})

const statusClasses = computed(() => {
    if (props.completed) {
        return 'bg-emerald-100'
    }
    
    return 'bg-blue-100'
})

const iconClasses = computed(() => {
    if (props.completed) {
        return 'text-emerald-600'
    }
    
    return 'text-blue-600'
})

const textClasses = computed(() => {
    if (props.completed) {
        return 'text-blue-700 line-through opacity-75'
    }
    
    return 'text-blue-800'
})
</script>

<style scoped>
.group:hover .group-hover\:translate-x-0\.5 {
    transform: translateX(2px);
}
</style>