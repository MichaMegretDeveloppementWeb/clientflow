<template>
    <div
        ref="containerRef"
        class="virtual-scroll-container"
        :style="containerStyle"
        @scroll="handleScroll"
    >
        <!-- Spacer supérieur -->
        <div :style="{ height: `${offsetY}px` }" />
        
        <!-- Items visibles -->
        <div
            v-for="item in visibleItems"
            :key="item.id"
            class="virtual-scroll-item"
            :style="getItemStyle(item)"
        >
            <slot :item="item" :index="item.virtualIndex" :is-scrolling="isScrolling">
                <!-- Contenu par défaut -->
                <div class="p-4 border-b border-gray-200">
                    {{ item.id }}
                </div>
            </slot>
        </div>
        
        <!-- Spacer inférieur -->
        <div :style="{ height: `${totalHeight - offsetY - visibleItemsHeight}px` }" />
        
        <!-- Loader pour le chargement automatique -->
        <div v-if="showLoader" class="flex justify-center py-4">
            <div class="flex items-center space-x-2">
                <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-500"></div>
                <span class="text-sm text-gray-500">{{ loadingText }}</span>
            </div>
        </div>
        
        <!-- Message d'erreur -->
        <div v-if="error" class="p-4 bg-red-50 border border-red-200 rounded-lg m-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <Icon name="alert-circle" class="h-5 w-5 text-red-500" />
                    <span class="text-sm text-red-800">{{ error }}</span>
                </div>
                <Button
                    variant="outline"
                    size="sm"
                    @click="$emit('retry')"
                    class="text-red-700 hover:text-red-800"
                >
                    <Icon name="refresh-cw" class="h-4 w-4 mr-1" />
                    Réessayer
                </Button>
            </div>
        </div>
        
        <!-- État vide -->
        <div
            v-if="!isLoading && items.length === 0 && !error"
            class="flex flex-col items-center justify-center py-12"
        >
            <slot name="empty">
                <Icon name="inbox" class="h-12 w-12 text-gray-400 mb-4" />
                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun élément</h3>
                <p class="text-sm text-gray-500">Il n'y a aucun élément à afficher.</p>
            </slot>
        </div>
    </div>
</template>

<script setup lang="ts" generic="T extends { id: string | number }">
import { computed, type CSSProperties } from 'vue';
import { Button } from '@/components/ui/button';
import Icon from '@/components/Icon.vue';
import { useVirtualScroll } from '@/composables/useVirtualScroll';

interface Props {
    items: T[];
    itemHeight: number;
    height?: number | string;
    width?: number | string;
    bufferSize?: number;
    isLoading?: boolean;
    error?: string | null;
    loadingText?: string;
    showLoader?: boolean;
    overscan?: number;
}

const props = withDefaults(defineProps<Props>(), {
    height: 400,
    width: '100%',
    bufferSize: 5,
    isLoading: false,
    error: null,
    loadingText: 'Chargement...',
    showLoader: false,
    overscan: 3
});

const emit = defineEmits<{
    scroll: [event: Event];
    retry: [];
    loadMore: [];
}>();

// Normaliser la hauteur du container
const containerHeight = computed(() => {
    if (typeof props.height === 'number') {
        return props.height;
    }
    if (typeof props.height === 'string' && props.height.endsWith('px')) {
        return parseInt(props.height.replace('px', ''));
    }
    return 400; // Valeur par défaut
});

// Hook de virtual scroll
const {
    containerRef,
    isScrolling,
    visibleItems,
    totalHeight,
    offsetY,
    scrollToIndex,
    scrollToItem
} = useVirtualScroll(props.items, {
    itemHeight: props.itemHeight,
    containerHeight: containerHeight.value,
    bufferSize: props.bufferSize
});

// Styles calculés
const containerStyle = computed<CSSProperties>(() => ({
    height: typeof props.height === 'number' ? `${props.height}px` : props.height,
    width: typeof props.width === 'number' ? `${props.width}px` : props.width,
    overflow: 'auto',
    position: 'relative'
}));

const visibleItemsHeight = computed(() => visibleItems.value.length * props.itemHeight);

const getItemStyle = (item: any) => ({
    position: 'absolute' as const,
    top: `${item.top}px`,
    left: 0,
    right: 0,
    height: `${props.itemHeight}px`,
    // Optimisation: utiliser transform3d pour la performance
    transform: `translate3d(0, 0, 0)`
});

// Gestionnaire de scroll avec émission d'événement
const handleScroll = (event: Event) => {
    emit('scroll', event);
    
    // Vérifier si on doit charger plus d'éléments
    const target = event.target as HTMLElement;
    const scrollBottom = target.scrollTop + target.clientHeight;
    const threshold = totalHeight.value - (props.itemHeight * 3); // 3 items avant la fin
    
    if (scrollBottom >= threshold && !props.isLoading) {
        emit('loadMore');
    }
};

// Exposer les méthodes publiques
defineExpose({
    scrollToIndex,
    scrollToItem,
    containerRef
});
</script>

<style scoped>
.virtual-scroll-container {
    /* Améliorer les performances de scroll */
    will-change: scroll-position;
    contain: layout style paint;
}

.virtual-scroll-item {
    /* Optimisation pour les éléments virtuels */
    contain: layout style paint;
    will-change: transform;
}

/* Smooth scrolling pour les navigateurs qui le supportent */
@supports (scroll-behavior: smooth) {
    .virtual-scroll-container {
        scroll-behavior: smooth;
    }
}
</style>