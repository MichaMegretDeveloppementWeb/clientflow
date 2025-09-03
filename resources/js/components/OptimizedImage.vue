<template>
    <div 
        class="optimized-image-container"
        :class="containerClass"
        :style="containerStyle"
    >
        <!-- Image principale -->
        <img
            ref="imageRef"
            :src="currentSrc"
            :alt="alt"
            :class="imageClasses"
            :style="imageStyles"
            :loading="lazy ? 'lazy' : 'eager'"
            :decoding="decoding"
            @load="handleLoad"
            @error="handleError"
        />

        <!-- Overlay de chargement -->
        <Transition
            enter-active-class="transition-opacity duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-300"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="isLoading && showLoader"
                class="absolute inset-0 flex items-center justify-center bg-gray-100"
            >
                <div class="flex items-center space-x-2">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-500"></div>
                    <span v-if="showLoadingText" class="text-sm text-gray-600">
                        {{ loadingText }}
                    </span>
                </div>
            </div>
        </Transition>

        <!-- Skeleton pendant le chargement initial -->
        <div
            v-if="!isLoaded && !error && showSkeleton"
            class="absolute inset-0 bg-gray-200 animate-pulse rounded"
            :class="skeletonClass"
        />

        <!-- État d'erreur -->
        <Transition
            enter-active-class="transition-opacity duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
        >
            <div
                v-if="error && showErrorState"
                class="absolute inset-0 flex flex-col items-center justify-center bg-gray-100 text-gray-500"
            >
                <Icon name="image-off" class="h-8 w-8 mb-2" />
                <span class="text-sm">{{ errorMessage }}</span>
                <Button
                    v-if="allowRetry"
                    variant="outline"
                    size="sm"
                    @click="retry"
                    class="mt-2"
                >
                    <Icon name="refresh-cw" class="h-4 w-4 mr-1" />
                    Réessayer
                </Button>
            </div>
        </Transition>

        <!-- Badge de format/qualité -->
        <div
            v-if="showFormatBadge && isLoaded && imageInfo"
            class="absolute top-2 right-2 bg-black bg-opacity-75 text-white text-xs px-2 py-1 rounded"
        >
            {{ imageInfo.format?.toUpperCase() }}
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch, type CSSProperties } from 'vue';
import { Button } from '@/components/ui/button';
import Icon from '@/components/Icon.vue';
import { useImageOptimization } from '@/composables/useIconOptimization';

interface ImageInfo {
    width: number;
    height: number;
    format?: string;
    size?: number;
}

interface Props {
    src: string;
    alt: string;
    width?: number | string;
    height?: number | string;
    
    // Responsive
    sizes?: string;
    srcset?: string;
    
    // Lazy loading
    lazy?: boolean;
    threshold?: number;
    rootMargin?: string;
    
    // Placeholder et fallbacks
    placeholder?: string;
    fallback?: string;
    
    // Optimisation
    quality?: number;
    format?: 'auto' | 'webp' | 'avif' | 'jpg' | 'png';
    progressive?: boolean;
    
    // Apparence
    objectFit?: 'contain' | 'cover' | 'fill' | 'none' | 'scale-down';
    objectPosition?: string;
    rounded?: boolean | string;
    
    // Comportement
    decoding?: 'auto' | 'sync' | 'async';
    crossorigin?: 'anonymous' | 'use-credentials';
    
    // UI
    showLoader?: boolean;
    showLoadingText?: boolean;
    loadingText?: string;
    showSkeleton?: boolean;
    showErrorState?: boolean;
    showFormatBadge?: boolean;
    allowRetry?: boolean;
    errorMessage?: string;
    
    // Classes
    class?: string;
    containerClass?: string;
    skeletonClass?: string;
}

const props = withDefaults(defineProps<Props>(), {
    lazy: true,
    threshold: 0.1,
    rootMargin: '50px',
    quality: 80,
    format: 'auto',
    progressive: true,
    objectFit: 'cover',
    objectPosition: 'center',
    rounded: false,
    decoding: 'async',
    showLoader: true,
    showLoadingText: false,
    loadingText: 'Chargement...',
    showSkeleton: true,
    showErrorState: true,
    showFormatBadge: false,
    allowRetry: true,
    errorMessage: 'Impossible de charger l\'image'
});

// État
const imageRef = ref<HTMLImageElement | null>(null);
const isLoading = ref(false);
const isLoaded = ref(false);
const error = ref<string | null>(null);
const currentSrc = ref(props.placeholder || '');
const imageInfo = ref<ImageInfo | null>(null);
const retryCount = ref(0);
const maxRetries = 3;

// Hook d'optimisation d'images
const { getOptimalImageSize } = useImageOptimization();

// Observer pour lazy loading
let intersectionObserver: IntersectionObserver | null = null;

// Calculs
const containerStyle = computed<CSSProperties>(() => ({
    position: 'relative',
    width: typeof props.width === 'number' ? `${props.width}px` : props.width,
    height: typeof props.height === 'number' ? `${props.height}px` : props.height,
    overflow: 'hidden'
}));

const imageClasses = computed(() => [
    'w-full h-full object-cover transition-opacity duration-300',
    {
        'opacity-0': !isLoaded.value && !error.value,
        'opacity-100': isLoaded.value || error.value,
        'rounded': props.rounded === true,
        [`rounded-${props.rounded}`]: typeof props.rounded === 'string'
    },
    props.class
]);

const imageStyles = computed<CSSProperties>(() => ({
    objectFit: props.objectFit,
    objectPosition: props.objectPosition
}));

// Générer l'URL optimisée
const generateOptimizedUrl = (baseSrc: string): string => {
    if (!baseSrc || baseSrc.startsWith('data:')) return baseSrc;
    
    const url = new URL(baseSrc, window.location.origin);
    
    // Ajouter les paramètres d'optimisation
    if (props.quality && props.quality !== 80) {
        url.searchParams.set('q', props.quality.toString());
    }
    
    if (props.format && props.format !== 'auto') {
        url.searchParams.set('f', props.format);
    }
    
    if (props.progressive) {
        url.searchParams.set('progressive', 'true');
    }
    
    // Optimisation de taille basée sur le container
    if (imageRef.value && props.width) {
        const containerWidth = typeof props.width === 'number' ? props.width : imageRef.value.clientWidth;
        const optimalSize = getOptimalImageSize(containerWidth);
        url.searchParams.set('w', optimalSize.toString());
    }
    
    return url.toString();
};

// Gestionnaires d'événements
const handleLoad = (event: Event) => {
    const img = event.target as HTMLImageElement;
    
    isLoading.value = false;
    isLoaded.value = true;
    error.value = null;
    
    // Extraire les informations de l'image
    imageInfo.value = {
        width: img.naturalWidth,
        height: img.naturalHeight,
        format: getImageFormat(img.src)
    };
    
    // Précharger la prochaine image si nécessaire
    prefetchNextImage();
};

const handleError = (event: Event) => {
    const img = event.target as HTMLImageElement;
    
    isLoading.value = false;
    error.value = `Erreur de chargement: ${img.src}`;
    
    // Essayer le fallback si disponible
    if (props.fallback && currentSrc.value !== props.fallback && retryCount.value === 0) {
        currentSrc.value = props.fallback;
        retryCount.value++;
        loadImage();
    }
};

const retry = () => {
    if (retryCount.value >= maxRetries) return;
    
    retryCount.value++;
    error.value = null;
    isLoaded.value = false;
    loadImage();
};

// Fonctions utilitaires
const getImageFormat = (src: string): string => {
    const url = new URL(src, window.location.origin);
    const format = url.searchParams.get('f') || url.pathname.split('.').pop();
    return format || 'unknown';
};

const prefetchNextImage = () => {
    // Logique pour précharger la prochaine image dans une séquence
    // À implémenter selon les besoins spécifiques
};

// Charger l'image
const loadImage = () => {
    if (!props.src || isLoaded.value) return;
    
    isLoading.value = true;
    currentSrc.value = generateOptimizedUrl(props.src);
};

// Configuration du lazy loading
const setupLazyLoading = () => {
    if (!props.lazy || !imageRef.value) return;
    
    intersectionObserver = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting && !isLoaded.value && !isLoading.value) {
                    loadImage();
                    intersectionObserver?.disconnect();
                }
            });
        },
        {
            threshold: props.threshold,
            rootMargin: props.rootMargin
        }
    );
    
    intersectionObserver.observe(imageRef.value);
};

// Watchers
watch(() => props.src, () => {
    isLoaded.value = false;
    error.value = null;
    retryCount.value = 0;
    
    if (props.lazy) {
        setupLazyLoading();
    } else {
        loadImage();
    }
});

// Lifecycle
onMounted(() => {
    // Définir le placeholder initial
    if (props.placeholder) {
        currentSrc.value = props.placeholder;
    }
    
    if (props.lazy) {
        setupLazyLoading();
    } else {
        loadImage();
    }
});

onUnmounted(() => {
    intersectionObserver?.disconnect();
});
</script>

<style scoped>
.optimized-image-container {
    /* Optimisations de performance */
    contain: layout style paint;
}

/* Support pour les images responsives */
.optimized-image-container img {
    max-width: 100%;
    height: auto;
}

/* Animation de fondu pour les changements d'image */
.optimized-image-container img {
    transition: opacity 0.3s ease-in-out;
}

/* Styles pour les différents états */
.optimized-image-container .animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}
</style>