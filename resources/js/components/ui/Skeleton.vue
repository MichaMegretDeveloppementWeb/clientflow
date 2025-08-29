<template>
    <div
        :class="[
            'animate-pulse rounded-md bg-gradient-to-r from-gray-200 via-gray-100 to-gray-200 bg-size-200 bg-pos-0',
            'animate-shimmer',
            className
        ]"
        :style="{ width, height }"
    />
</template>

<script setup lang="ts">
/**
 * Composant Skeleton pour les placeholders de chargement
 * Responsabilités :
 * - Animation de shimmer fluide
 * - Tailles personnalisables
 * - Classes CSS additionnelles
 */

interface Props {
    width?: string
    height?: string
    className?: string
}

const props = withDefaults(defineProps<Props>(), {
    width: 'auto',
    height: '1rem',
    className: ''
})
</script>

<style scoped>
.bg-size-200 {
    background-size: 200% 100%;
}

.bg-pos-0 {
    background-position: 0% 0%;
}

@keyframes shimmer {
    0% {
        background-position: -200% 0%;
    }
    100% {
        background-position: 200% 0%;
    }
}

.animate-shimmer {
    animation: shimmer 2s ease-in-out infinite;
}
</style>