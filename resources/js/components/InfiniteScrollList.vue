<template>
    <div class="infinite-scroll-container">
        <VirtualScrollList
            :items="items"
            :item-height="itemHeight"
            :height="height"
            :width="width"
            :is-loading="isLoading"
            :error="error"
            :show-loader="isLoading && hasMore"
            :loading-text="loadingText"
            @scroll="handleScroll"
            @load-more="loadMore"
            @retry="retry"
        >
            <!-- Slot pour les items -->
            <template #default="{ item, index, isScrolling }">
                <slot :item="item" :index="index" :is-scrolling="isScrolling">
                    <div class="p-4 border-b border-gray-200">
                        <div class="text-sm text-gray-900">{{ item.id }}</div>
                    </div>
                </slot>
            </template>

            <!-- Slot pour l'état vide -->
            <template #empty>
                <slot name="empty">
                    <div class="text-center py-12">
                        <Icon name="inbox" class="h-12 w-12 text-gray-400 mb-4 mx-auto" />
                        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ emptyTitle }}</h3>
                        <p class="text-sm text-gray-500">{{ emptyMessage }}</p>
                        <Button
                            v-if="showRefreshOnEmpty"
                            variant="outline"
                            @click="refresh"
                            class="mt-4"
                        >
                            <Icon name="refresh-cw" class="h-4 w-4 mr-2" />
                            Actualiser
                        </Button>
                    </div>
                </slot>
            </template>
        </VirtualScrollList>

        <!-- Indicateur de fin de liste -->
        <div
            v-if="!hasMore && items.length > 0 && !isLoading"
            class="text-center py-6 border-t border-gray-200 bg-gray-50"
        >
            <Icon name="check-circle" class="h-5 w-5 text-green-500 mx-auto mb-2" />
            <p class="text-sm text-gray-600">{{ endMessage }}</p>
        </div>

        <!-- Statistiques optionnelles -->
        <div
            v-if="showStats && items.length > 0"
            class="bg-white border-t border-gray-200 px-6 py-3"
        >
            <div class="flex items-center justify-between text-sm text-gray-600">
                <span>{{ items.length }} élément{{ items.length > 1 ? 's' : '' }} affiché{{ items.length > 1 ? 's' : '' }}</span>
                <span v-if="totalCount">{{ items.length }} sur {{ totalCount }}</span>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts" generic="T extends { id: string | number }">
import { ref, computed, onMounted } from 'vue';
import { Button } from '@/components/ui/button';
import Icon from '@/components/Icon.vue';
import VirtualScrollList from './VirtualScrollList.vue';

interface InfiniteScrollResponse<T> {
    data: T[];
    hasMore: boolean;
    total?: number;
    page?: number;
}

interface Props {
    loadFunction: (page: number, limit: number) => Promise<InfiniteScrollResponse<T>>;
    itemHeight: number;
    pageSize?: number;
    height?: number | string;
    width?: number | string;
    loadingText?: string;
    emptyTitle?: string;
    emptyMessage?: string;
    endMessage?: string;
    showRefreshOnEmpty?: boolean;
    showStats?: boolean;
    autoLoad?: boolean;
    threshold?: number; // Nombre d'items avant la fin pour déclencher le chargement
}

const props = withDefaults(defineProps<Props>(), {
    pageSize: 20,
    height: 400,
    width: '100%',
    loadingText: 'Chargement...',
    emptyTitle: 'Aucun élément',
    emptyMessage: 'Il n\'y a aucun élément à afficher.',
    endMessage: 'Tous les éléments ont été chargés',
    showRefreshOnEmpty: true,
    showStats: false,
    autoLoad: true,
    threshold: 5
});

const emit = defineEmits<{
    loaded: [data: T[], page: number];
    error: [error: string];
    scroll: [event: Event];
}>();

// État
const items = ref<T[]>([]);
const isLoading = ref(false);
const hasMore = ref(true);
const error = ref<string | null>(null);
const currentPage = ref(0);
const totalCount = ref<number | null>(null);

// Méthodes
const loadMore = async () => {
    if (isLoading.value || !hasMore.value) return;

    isLoading.value = true;
    error.value = null;

    try {
        const response = await props.loadFunction(currentPage.value + 1, props.pageSize);
        
        items.value.push(...response.data);
        hasMore.value = response.hasMore;
        currentPage.value++;
        
        if (response.total !== undefined) {
            totalCount.value = response.total;
        }

        emit('loaded', response.data, currentPage.value);
    } catch (err: any) {
        error.value = err.message || 'Erreur lors du chargement';
        emit('error', error.value);
        console.error('Erreur lors du chargement:', err);
    } finally {
        isLoading.value = false;
    }
};

const refresh = async () => {
    items.value = [];
    currentPage.value = 0;
    hasMore.value = true;
    error.value = null;
    totalCount.value = null;
    await loadMore();
};

const retry = async () => {
    error.value = null;
    await loadMore();
};

// Gestionnaire de scroll avec seuil
const handleScroll = (event: Event) => {
    emit('scroll', event);
    
    // Vérifier si on doit charger plus d'éléments
    if (!isLoading.value && hasMore.value) {
        const remainingItems = items.value.length - getVisibleEndIndex(event);
        if (remainingItems <= props.threshold) {
            loadMore();
        }
    }
};

// Calculer l'index de fin visible (approximatif)
const getVisibleEndIndex = (event: Event): number => {
    const target = event.target as HTMLElement;
    const scrollTop = target.scrollTop;
    const containerHeight = target.clientHeight;
    const scrollBottom = scrollTop + containerHeight;
    
    return Math.floor(scrollBottom / props.itemHeight);
};

// Auto-chargement initial
onMounted(() => {
    if (props.autoLoad) {
        loadMore();
    }
});

// Exposer les méthodes publiques
defineExpose({
    loadMore,
    refresh,
    retry,
    items: computed(() => items.value),
    isLoading: computed(() => isLoading.value),
    hasMore: computed(() => hasMore.value),
    error: computed(() => error.value),
    totalCount: computed(() => totalCount.value)
});
</script>

<style scoped>
.infinite-scroll-container {
    display: flex;
    flex-direction: column;
    height: 100%;
    background: white;
    border-radius: 0.5rem;
    overflow: hidden;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}
</style>