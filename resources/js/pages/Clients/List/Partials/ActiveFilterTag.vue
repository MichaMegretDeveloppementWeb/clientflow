<template>
    <Badge
        variant="outline"
        class="group cursor-pointer border-gray-300 px-2 py-1 text-xs hover:border-red-300 hover:bg-red-50"
        @click="$emit('remove')"
    >
        <span class="mr-1">{{ label }}</span>
        <Icon 
            name="x" 
            class="h-3 w-3 text-gray-500 transition-colors group-hover:text-red-600"
        />
    </Badge>
</template>

<script setup lang="ts">
import { Badge } from '@/components/ui/badge'
import Icon from '@/components/Icon.vue'

/**
 * Tag représentant un filtre actif
 * Responsabilité : Affichage et suppression d'un filtre
 */

interface Props {
    label: string
}

defineProps<Props>()

defineEmits<{
    remove: []
}>()
</script>