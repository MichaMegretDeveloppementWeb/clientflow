<template>
    <div 
        ref="containerRef"
        class="virtual-list-container"
        :style="{ height: containerHeight }"
        @scroll="handleScroll"
    >
        <!-- Spacer pour maintenir la hauteur totale -->
        <div :style="{ height: `${totalHeight}px` }">
            <!-- Items visibles uniquement -->
            <div
                :style="{
                    transform: `translateY(${offsetY}px)`
                }"
            >
                <div
                    v-for="item in visibleItems"
                    :key="getItemKey(item)"
                    :style="{ height: `${itemHeight}px` }"
                >
                    <slot :item="item" :index="getItemIndex(item)" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts" generic="T">
import { ref, computed, onMounted, onUnmounted } from 'vue'

/**
 * Composant de virtual scrolling pour optimiser les grandes listes
 * Affiche seulement les items visibles dans le viewport
 */

interface Props {
    items: T[]
    itemHeight: number
    containerHeight?: string
    buffer?: number // Nombre d'items à rendre en dehors du viewport
    keyField?: string
}

const props = withDefaults(defineProps<Props>(), {
    containerHeight: '600px',
    buffer: 5,
    keyField: 'id'
})

const emit = defineEmits<{
    'scroll-end': []
    'scroll-start': []
}>()

// Refs
const containerRef = ref<HTMLElement>()
const scrollTop = ref(0)
const containerHeightPx = ref(600)

// Computed
const totalHeight = computed(() => props.items.length * props.itemHeight)

const visibleRange = computed(() => {
    const start = Math.max(0, Math.floor(scrollTop.value / props.itemHeight) - props.buffer)
    const visibleCount = Math.ceil(containerHeightPx.value / props.itemHeight)
    const end = Math.min(props.items.length, start + visibleCount + props.buffer * 2)
    
    return { start, end }
})

const visibleItems = computed(() => {
    const { start, end } = visibleRange.value
    return props.items.slice(start, end)
})

const offsetY = computed(() => {
    return visibleRange.value.start * props.itemHeight
})

// Methods
const handleScroll = () => {
    if (!containerRef.value) return
    
    scrollTop.value = containerRef.value.scrollTop
    
    // Emit events for infinite scroll
    const scrollHeight = containerRef.value.scrollHeight
    const scrollPosition = scrollTop.value + containerHeightPx.value
    
    if (scrollPosition >= scrollHeight - 100) {
        emit('scroll-end')
    }
    
    if (scrollTop.value <= 100) {
        emit('scroll-start')
    }
}

const getItemKey = (item: T): string | number => {
    if (typeof item === 'object' && item !== null && props.keyField in item) {
        return (item as any)[props.keyField]
    }
    return getItemIndex(item)
}

const getItemIndex = (item: T): number => {
    return props.items.indexOf(item)
}

const scrollToIndex = (index: number) => {
    if (!containerRef.value) return
    
    const targetScrollTop = index * props.itemHeight
    containerRef.value.scrollTop = targetScrollTop
}

const scrollToTop = () => {
    if (!containerRef.value) return
    containerRef.value.scrollTop = 0
}

const scrollToBottom = () => {
    if (!containerRef.value) return
    containerRef.value.scrollTop = totalHeight.value
}

// Resize observer pour ajuster la hauteur du container
const resizeObserver = ref<ResizeObserver>()

onMounted(() => {
    if (!containerRef.value) return
    
    // Mesurer la hauteur initiale
    containerHeightPx.value = containerRef.value.clientHeight
    
    // Observer les changements de taille
    resizeObserver.value = new ResizeObserver((entries) => {
        for (const entry of entries) {
            containerHeightPx.value = entry.contentRect.height
        }
    })
    
    resizeObserver.value.observe(containerRef.value)
})

onUnmounted(() => {
    resizeObserver.value?.disconnect()
})

// Exposer les méthodes pour le parent
defineExpose({
    scrollToIndex,
    scrollToTop,
    scrollToBottom
})
</script>

<style scoped>
.virtual-list-container {
    overflow-y: auto;
    position: relative;
    
    /* Optimisation du scrolling */
    -webkit-overflow-scrolling: touch;
    will-change: scroll-position;
}

/* Scrollbar personnalisée */
.virtual-list-container::-webkit-scrollbar {
    width: 8px;
}

.virtual-list-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.virtual-list-container::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.virtual-list-container::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>