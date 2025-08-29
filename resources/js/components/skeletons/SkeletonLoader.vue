<template>
    <div
        class="skeleton-loader animate-pulse"
        :class="[
            shapeClass,
            sizeClass,
            customClass
        ]"
        :style="customStyle"
        :aria-label="ariaLabel"
        role="progressbar"
        aria-valuenow="undefined"
        aria-valuemin="0"
        aria-valuemax="100"
    >
        <span class="sr-only">{{ loadingText }}</span>
    </div>
</template>

<script setup lang="ts">
import { computed, type CSSProperties } from 'vue';

interface Props {
    // Forme du skeleton
    shape?: 'rectangle' | 'circle' | 'text' | 'button' | 'avatar' | 'card' | 'custom';
    
    // Taille prédéfinie
    size?: 'xs' | 'sm' | 'md' | 'lg' | 'xl' | 'custom';
    
    // Dimensions personnalisées
    width?: string | number;
    height?: string | number;
    
    // Couleurs
    baseColor?: string;
    highlightColor?: string;
    
    // Animation
    animated?: boolean;
    animationDuration?: string;
    
    // Classes personnalisées
    class?: string;
    
    // Accessibilité
    ariaLabel?: string;
    loadingText?: string;
}

const props = withDefaults(defineProps<Props>(), {
    shape: 'rectangle',
    size: 'md',
    animated: true,
    animationDuration: '1.5s',
    baseColor: 'rgb(229, 231, 235)', // gray-200
    highlightColor: 'rgb(243, 244, 246)', // gray-100
    ariaLabel: 'Contenu en cours de chargement',
    loadingText: 'Chargement en cours...'
});

// Classes de forme
const shapeClass = computed(() => {
    const shapes = {
        rectangle: 'rounded',
        circle: 'rounded-full',
        text: 'rounded h-4',
        button: 'rounded-md',
        avatar: 'rounded-full',
        card: 'rounded-lg',
        custom: ''
    };
    return shapes[props.shape];
});

// Classes de taille
const sizeClass = computed(() => {
    if (props.size === 'custom') return '';
    
    const sizes = {
        xs: getSizeForShape('xs'),
        sm: getSizeForShape('sm'),
        md: getSizeForShape('md'),
        lg: getSizeForShape('lg'),
        xl: getSizeForShape('xl')
    };
    
    return sizes[props.size];
});

// Obtenir la taille selon la forme
const getSizeForShape = (size: string): string => {
    const sizeMap = {
        rectangle: {
            xs: 'h-4 w-16',
            sm: 'h-6 w-24',
            md: 'h-8 w-32',
            lg: 'h-10 w-40',
            xl: 'h-12 w-48'
        },
        circle: {
            xs: 'h-6 w-6',
            sm: 'h-8 w-8',
            md: 'h-10 w-10',
            lg: 'h-12 w-12',
            xl: 'h-16 w-16'
        },
        text: {
            xs: 'h-3 w-20',
            sm: 'h-4 w-32',
            md: 'h-4 w-48',
            lg: 'h-5 w-64',
            xl: 'h-6 w-80'
        },
        button: {
            xs: 'h-6 w-16',
            sm: 'h-8 w-20',
            md: 'h-10 w-24',
            lg: 'h-12 w-32',
            xl: 'h-14 w-40'
        },
        avatar: {
            xs: 'h-6 w-6',
            sm: 'h-8 w-8',
            md: 'h-10 w-10',
            lg: 'h-12 w-12',
            xl: 'h-16 w-16'
        },
        card: {
            xs: 'h-24 w-32',
            sm: 'h-32 w-48',
            md: 'h-40 w-64',
            lg: 'h-48 w-80',
            xl: 'h-56 w-96'
        }
    };
    
    return sizeMap[props.shape as keyof typeof sizeMap]?.[size as keyof typeof sizeMap.rectangle] || sizeMap.rectangle[size as keyof typeof sizeMap.rectangle];
};

// Classes personnalisées
const customClass = computed(() => props.class || '');

// Styles personnalisés
const customStyle = computed((): CSSProperties => {
    const style: CSSProperties = {
        backgroundColor: props.baseColor
    };
    
    // Dimensions personnalisées
    if (props.width && props.size === 'custom') {
        style.width = typeof props.width === 'number' ? `${props.width}px` : props.width;
    }
    
    if (props.height && props.size === 'custom') {
        style.height = typeof props.height === 'number' ? `${props.height}px` : props.height;
    }
    
    // Animation personnalisée
    if (props.animated) {
        style.animationDuration = props.animationDuration;
        style.backgroundImage = `linear-gradient(90deg, ${props.baseColor} 25%, ${props.highlightColor} 50%, ${props.baseColor} 75%)`;
        style.backgroundSize = '200% 100%';
    }
    
    return style;
});
</script>

<style scoped>
.skeleton-loader {
    /* Base du skeleton */
    background-color: rgb(229, 231, 235); /* gray-200 */
    position: relative;
    overflow: hidden;
}

.skeleton-loader.animate-pulse {
    animation: skeleton-pulse 1.5s ease-in-out infinite;
}

@keyframes skeleton-pulse {
    0% {
        background-position: -200% 0;
    }
    100% {
        background-position: 200% 0;
    }
}

/* Variantes d'animation plus fluides */
.skeleton-loader.animate-shimmer {
    animation: skeleton-shimmer 1.5s ease-in-out infinite;
}

@keyframes skeleton-shimmer {
    0% {
        transform: translateX(-100%);
    }
    100% {
        transform: translateX(100%);
    }
}

/* Mode sombre */
@media (prefers-color-scheme: dark) {
    .skeleton-loader {
        background-color: rgb(55, 65, 81); /* gray-700 */
    }
}
</style>