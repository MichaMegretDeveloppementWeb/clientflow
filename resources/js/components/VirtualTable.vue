<template>
    <div class="virtual-table-container" :style="containerStyle">
        <!-- En-tête fixe -->
        <div
            v-if="stickyHeader"
            class="virtual-table-header sticky top-0 z-10 bg-white border-b border-gray-200"
            :style="headerStyle"
        >
            <div class="grid items-center gap-4 px-6 py-3" :style="{ gridTemplateColumns }">
                <div
                    v-for="column in columns"
                    :key="column.key"
                    class="font-medium text-gray-900"
                    :class="column.headerClass"
                >
                    <div class="flex items-center space-x-2">
                        <span>{{ column.title }}</span>
                        <button
                            v-if="column.sortable"
                            @click="handleSort(column.key)"
                            class="text-gray-400 hover:text-gray-600"
                        >
                            <Icon
                                :name="getSortIcon(column.key)"
                                class="h-4 w-4"
                            />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenu virtualisé -->
        <div
            ref="containerRef"
            class="virtual-table-body"
            :style="bodyStyle"
            @scroll="handleScroll"
        >
            <!-- Spacer supérieur -->
            <div :style="{ height: `${offsetY}px` }" />

            <!-- Lignes visibles -->
            <div
                v-for="item in visibleItems"
                :key="getRowKey(item)"
                class="virtual-table-row border-b border-gray-100 hover:bg-gray-50 transition-colors"
                :style="getRowStyle(item)"
                :class="getRowClass(item)"
                @click="handleRowClick(item, item.virtualIndex)"
            >
                <div class="grid items-center gap-4 px-6 py-3" :style="{ gridTemplateColumns }">
                    <div
                        v-for="column in columns"
                        :key="column.key"
                        :class="column.cellClass"
                    >
                        <slot
                            :name="`cell-${column.key}`"
                            :item="item"
                            :value="getColumnValue(item, column.key)"
                            :column="column"
                            :index="item.virtualIndex"
                        >
                            <!-- Contenu par défaut -->
                            <span class="text-sm text-gray-900">
                                {{ getColumnValue(item, column.key) }}
                            </span>
                        </slot>
                    </div>
                </div>
            </div>

            <!-- Spacer inférieur -->
            <div :style="{ height: `${remainingHeight}px` }" />

            <!-- Indicateur de chargement -->
            <div v-if="isLoading" class="flex justify-center py-6">
                <div class="flex items-center space-x-2">
                    <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-500"></div>
                    <span class="text-sm text-gray-500">{{ loadingText }}</span>
                </div>
            </div>

            <!-- Message d'erreur -->
            <div v-if="error" class="p-6 bg-red-50 border border-red-200 rounded-lg m-6">
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
                    <Icon name="table" class="h-12 w-12 text-gray-400 mb-4" />
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune donnée</h3>
                    <p class="text-sm text-gray-500">Il n'y a aucune donnée à afficher dans ce tableau.</p>
                </slot>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts" generic="T extends { id: string | number }">
import { computed, type CSSProperties } from 'vue';
import { Button } from '@/components/ui/button';
import Icon from '@/components/Icon.vue';
import { useVirtualTable } from '@/composables/useVirtualScroll';

interface TableColumn {
    key: string;
    title: string;
    width?: string;
    sortable?: boolean;
    headerClass?: string;
    cellClass?: string;
}

interface Props {
    items: T[];
    columns: TableColumn[];
    rowHeight?: number;
    headerHeight?: number;
    height?: number | string;
    width?: number | string;
    stickyHeader?: boolean;
    sortBy?: string;
    sortOrder?: 'asc' | 'desc';
    isLoading?: boolean;
    error?: string | null;
    loadingText?: string;
    selectable?: boolean;
    rowKey?: string | ((item: T) => string | number);
    rowClass?: string | ((item: T) => string);
    bufferSize?: number;
}

const props = withDefaults(defineProps<Props>(), {
    rowHeight: 60,
    headerHeight: 50,
    height: 400,
    width: '100%',
    stickyHeader: true,
    isLoading: false,
    error: null,
    loadingText: 'Chargement...',
    selectable: false,
    rowKey: 'id',
    bufferSize: 5
});

const emit = defineEmits<{
    sort: [key: string, order: 'asc' | 'desc'];
    rowClick: [item: T, index: number];
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
    return 400;
});

// Hook de table virtualisée
const {
    containerRef,
    isScrolling,
    visibleItems,
    totalHeight,
    offsetY,
    scrollToIndex,
    scrollToItem
} = useVirtualTable(props.items, {
    rowHeight: props.rowHeight,
    headerHeight: props.headerHeight,
    containerHeight: containerHeight.value,
    stickyHeader: props.stickyHeader,
    bufferSize: props.bufferSize
});

// Styles calculés
const containerStyle = computed<CSSProperties>(() => ({
    height: typeof props.height === 'number' ? `${props.height}px` : props.height,
    width: typeof props.width === 'number' ? `${props.width}px` : props.width,
    position: 'relative',
    overflow: 'hidden',
    border: '1px solid rgb(229, 231, 235)' // border-gray-200
}));

const headerStyle = computed<CSSProperties>(() => ({
    height: `${props.headerHeight}px`,
    minHeight: `${props.headerHeight}px`
}));

const bodyStyle = computed<CSSProperties>(() => ({
    height: props.stickyHeader 
        ? `${containerHeight.value - props.headerHeight}px`
        : `${containerHeight.value}px`,
    overflow: 'auto',
    position: 'relative'
}));

const gridTemplateColumns = computed(() => {
    return props.columns
        .map(col => col.width || '1fr')
        .join(' ');
});

const remainingHeight = computed(() => {
    const visibleHeight = visibleItems.value.length * props.rowHeight;
    return Math.max(0, totalHeight.value - offsetY.value - visibleHeight);
});

// Méthodes
const getRowKey = (item: T): string | number => {
    if (typeof props.rowKey === 'function') {
        return props.rowKey(item);
    }
    return (item as any)[props.rowKey];
};

const getRowClass = (item: T): string => {
    let classes = '';
    
    if (props.selectable) {
        classes += ' cursor-pointer';
    }
    
    if (typeof props.rowClass === 'function') {
        classes += ' ' + props.rowClass(item);
    } else if (props.rowClass) {
        classes += ' ' + props.rowClass;
    }
    
    return classes;
};

const getRowStyle = (item: any): CSSProperties => ({
    position: 'absolute',
    top: `${item.top}px`,
    left: 0,
    right: 0,
    height: `${props.rowHeight}px`,
    transform: 'translate3d(0, 0, 0)'
});

const getColumnValue = (item: T, key: string): any => {
    return (item as any)[key];
};

const getSortIcon = (columnKey: string): string => {
    if (props.sortBy !== columnKey) {
        return 'chevrons-up-down';
    }
    return props.sortOrder === 'asc' ? 'chevron-up' : 'chevron-down';
};

// Gestionnaires d'événements
const handleSort = (columnKey: string) => {
    const newOrder = props.sortBy === columnKey && props.sortOrder === 'asc' ? 'desc' : 'asc';
    emit('sort', columnKey, newOrder);
};

const handleRowClick = (item: T, index: number) => {
    if (props.selectable) {
        emit('rowClick', item, index);
    }
};

const handleScroll = (event: Event) => {
    emit('scroll', event);
    
    // Vérifier si on doit charger plus d'éléments
    const target = event.target as HTMLElement;
    const scrollBottom = target.scrollTop + target.clientHeight;
    const threshold = totalHeight.value - (props.rowHeight * 3);
    
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
.virtual-table-container {
    /* Améliorer les performances */
    contain: layout style paint;
}

.virtual-table-header {
    /* S'assurer que l'en-tête reste au-dessus */
    contain: layout style paint;
}

.virtual-table-body {
    /* Optimisation du scroll */
    will-change: scroll-position;
    contain: layout style paint;
}

.virtual-table-row {
    /* Optimisation pour les lignes virtuelles */
    contain: layout style paint;
    will-change: transform;
}

/* Masquer la scrollbar sur Webkit si souhaité */
.virtual-table-body::-webkit-scrollbar {
    width: 8px;
}

.virtual-table-body::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.virtual-table-body::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

.virtual-table-body::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>