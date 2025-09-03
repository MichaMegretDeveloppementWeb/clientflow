import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue';

interface VirtualScrollOptions {
    itemHeight: number;
    containerHeight?: number;
    bufferSize?: number;
    scrollDebounce?: number;
}

interface VirtualScrollItem {
    id: string | number;
    [key: string]: any;
}

export function useVirtualScroll<T extends VirtualScrollItem>(
    items: T[],
    options: VirtualScrollOptions
) {
    const {
        itemHeight,
        containerHeight = 400,
        bufferSize = 5
        // scrollDebounce reserved for future scroll throttling
    } = options;

    // État
    const scrollTop = ref(0);
    const containerRef = ref<HTMLElement | null>(null);
    const isScrolling = ref(false);

    // Variables internes
    let scrollTimeout: number | null = null;
    let resizeObserver: ResizeObserver | null = null;

    // Calculs
    const totalHeight = computed(() => items.length * itemHeight);
    const visibleCount = computed(() => Math.ceil(containerHeight / itemHeight));
    const startIndex = computed(() => {
        const index = Math.floor(scrollTop.value / itemHeight);
        return Math.max(0, index - bufferSize);
    });
    const endIndex = computed(() => {
        const index = startIndex.value + visibleCount.value + bufferSize * 2;
        return Math.min(items.length, index);
    });

    // Items visibles avec position
    const visibleItems = computed(() => {
        return items.slice(startIndex.value, endIndex.value).map((item, index) => ({
            ...item,
            virtualIndex: startIndex.value + index,
            top: (startIndex.value + index) * itemHeight
        }));
    });

    // Offset pour les items virtuels
    const offsetY = computed(() => startIndex.value * itemHeight);

    // Gestionnaire de scroll avec debounce
    const handleScroll = (event: Event) => {
        const target = event.target as HTMLElement;
        scrollTop.value = target.scrollTop;
        
        isScrolling.value = true;
        
        if (scrollTimeout) {
            clearTimeout(scrollTimeout);
        }
        
        scrollTimeout = setTimeout(() => {
            isScrolling.value = false;
        }, 150);
    };

    // Scroll vers un index spécifique
    const scrollToIndex = (index: number) => {
        if (!containerRef.value) return;
        
        const targetScrollTop = Math.max(0, index * itemHeight);
        containerRef.value.scrollTop = targetScrollTop;
        scrollTop.value = targetScrollTop;
    };

    // Scroll vers un item spécifique
    const scrollToItem = (itemId: string | number) => {
        const index = items.findIndex(item => item.id === itemId);
        if (index !== -1) {
            scrollToIndex(index);
        }
    };

    // Observer pour le redimensionnement
    const setupResizeObserver = () => {
        if (!containerRef.value || !ResizeObserver) return;

        resizeObserver = new ResizeObserver(() => {
            // Recalculer si nécessaire
            nextTick(() => {
                if (containerRef.value) {
                    // const rect = containerRef.value.getBoundingClientRect();
                    // Future: ajuster containerHeight dynamiquement
                }
            });
        });

        resizeObserver.observe(containerRef.value);
    };

    // Configuration du container
    const setupContainer = () => {
        if (!containerRef.value) return;

        // Ajouter l'écouteur de scroll avec debounce
        let ticking = false;
        const throttledScroll = (event: Event) => {
            if (!ticking) {
                requestAnimationFrame(() => {
                    handleScroll(event);
                    ticking = false;
                });
                ticking = true;
            }
        };

        containerRef.value.addEventListener('scroll', throttledScroll, { passive: true });
        
        // Nettoyer lors du démontage
        const currentContainer = containerRef.value;
        onUnmounted(() => {
            currentContainer?.removeEventListener('scroll', throttledScroll);
        });
    };

    onMounted(() => {
        nextTick(() => {
            setupContainer();
            setupResizeObserver();
        });
    });

    onUnmounted(() => {
        if (scrollTimeout) {
            clearTimeout(scrollTimeout);
        }
        if (resizeObserver) {
            resizeObserver.disconnect();
        }
    });

    return {
        // Refs
        containerRef,
        
        // État
        isScrolling,
        scrollTop,
        
        // Données calculées
        visibleItems,
        totalHeight,
        offsetY,
        startIndex,
        endIndex,
        visibleCount,
        
        // Actions
        scrollToIndex,
        scrollToItem
    };
}

// Hook spécialisé pour les tableaux virtualisés
export function useVirtualTable<T extends VirtualScrollItem>(
    items: T[],
    options: Omit<VirtualScrollOptions, 'itemHeight'> & { 
        rowHeight?: number;
        headerHeight?: number;
        stickyHeader?: boolean;
    }
) {
    const {
        rowHeight = 60,
        headerHeight = 50,
        stickyHeader = true,
        ...virtualOptions
    } = options;

    const virtualScroll = useVirtualScroll(items, {
        ...virtualOptions,
        itemHeight: rowHeight
    });

    // Hauteur totale incluant l'en-tête
    const totalTableHeight = computed(() => {
        const contentHeight = virtualScroll.totalHeight.value;
        return stickyHeader ? contentHeight + headerHeight : contentHeight;
    });

    // Offset ajusté pour l'en-tête
    const tableOffsetY = computed(() => {
        return stickyHeader ? virtualScroll.offsetY.value : virtualScroll.offsetY.value + headerHeight;
    });

    return {
        ...virtualScroll,
        
        // Propriétés spécifiques au tableau
        rowHeight,
        headerHeight,
        stickyHeader,
        totalTableHeight,
        tableOffsetY
    };
}

// Hook pour la pagination virtualisée avec chargement dynamique
export function useVirtualPagination<T extends VirtualScrollItem>(
    options: VirtualScrollOptions & {
        loadMore: (page: number, limit: number) => Promise<{ data: T[]; hasMore: boolean }>;
        pageSize?: number;
        initialLoad?: boolean;
    }
) {
    const {
        loadMore,
        pageSize = 50,
        initialLoad = true,
        ...virtualOptions
    } = options;

    // État
    const items = ref<T[]>([]);
    const isLoading = ref(false);
    const hasMore = ref(true);
    const currentPage = ref(0);
    const error = ref<string | null>(null);

    // Virtual scroll avec les items actuels
    const virtualScroll = useVirtualScroll(items.value, virtualOptions);

    // Charger plus d'éléments
    const loadMoreItems = async () => {
        if (isLoading.value || !hasMore.value) return;

        isLoading.value = true;
        error.value = null;

        try {
            const response = await loadMore(currentPage.value + 1, pageSize);
            
            items.value.push(...response.data);
            hasMore.value = response.hasMore;
            currentPage.value++;
        } catch (err: any) {
            error.value = err.message || 'Erreur lors du chargement';
            console.error('Erreur lors du chargement des items:', err);
        } finally {
            isLoading.value = false;
        }
    };

    // Détection du scroll vers le bas
    const { endIndex } = virtualScroll;
    // visibleItems available but not used in this context
    
    // Observer si on approche de la fin
    const shouldLoadMore = computed(() => {
        const threshold = Math.min(10, pageSize * 0.8); // 80% de la page ou 10 items
        return endIndex.value >= items.value.length - threshold;
    });

    // Auto-charger quand on approche de la fin
    let loadMoreTimeout: number | null = null;
    const checkLoadMore = () => {
        if (shouldLoadMore.value && hasMore.value && !isLoading.value) {
            if (loadMoreTimeout) clearTimeout(loadMoreTimeout);
            loadMoreTimeout = setTimeout(loadMoreItems, 100);
        }
    };

    // Watcher pour le chargement automatique
    onMounted(() => {
        if (initialLoad) {
            loadMoreItems();
        }
    });

    onUnmounted(() => {
        if (loadMoreTimeout) {
            clearTimeout(loadMoreTimeout);
        }
    });

    // Refresh complet
    const refresh = async () => {
        items.value = [];
        currentPage.value = 0;
        hasMore.value = true;
        error.value = null;
        await loadMoreItems();
    };

    return {
        ...virtualScroll,
        
        // État du chargement
        items,
        isLoading,
        hasMore,
        error,
        currentPage,
        
        // Actions
        loadMoreItems,
        refresh,
        checkLoadMore,
        
        // Computed
        shouldLoadMore
    };
}