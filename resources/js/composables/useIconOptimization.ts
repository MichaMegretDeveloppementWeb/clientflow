import { ref, computed, onMounted } from 'vue';

interface IconCacheEntry {
    svg: string;
    timestamp: number;
    used: boolean;
}

interface IconMetrics {
    totalIcons: number;
    cachedIcons: number;
    cacheHitRate: number;
    memoryUsage: number;
}

class IconOptimizer {
    private cache = new Map<string, IconCacheEntry>();
    private preloadedIcons = new Set<string>();
    private readonly maxCacheSize = 100;
    private readonly cacheExpiryTime = 3600000; // 1 heure

    // Icônes critiques à précharger
    private readonly criticalIcons = [
        'menu',
        'x',
        'search',
        'user',
        'settings',
        'home',
        'chevron-down',
        'chevron-up',
        'chevron-left',
        'chevron-right',
        'loader-2',
        'check',
        'alert-circle',
        'info'
    ];

    // Icônes par contexte
    private readonly contextualIcons = {
        dashboard: ['chart-bar', 'trending-up', 'calendar', 'dollar-sign'],
        clients: ['users', 'user-plus', 'building', 'mail', 'phone'],
        projects: ['folder', 'folder-plus', 'briefcase', 'clock', 'target'],
        events: ['calendar-days', 'plus', 'edit', 'trash-2', 'check-circle'],
        settings: ['cog', 'user-cog', 'key', 'palette', 'bell']
    };

    async preloadCriticalIcons(): Promise<void> {
        const promises = this.criticalIcons.map(icon => this.preloadIcon(icon));
        await Promise.allSettled(promises);
    }

    async preloadContextualIcons(context: keyof typeof this.contextualIcons): Promise<void> {
        const icons = this.contextualIcons[context] || [];
        const promises = icons.map(icon => this.preloadIcon(icon));
        await Promise.allSettled(promises);
    }

    private async preloadIcon(iconName: string): Promise<void> {
        if (this.preloadedIcons.has(iconName) || this.cache.has(iconName)) {
            return;
        }

        try {
            // Simuler le chargement de l'icône (à adapter selon votre système d'icônes)
            const iconData = await this.loadIconData(iconName);
            
            this.cache.set(iconName, {
                svg: iconData,
                timestamp: Date.now(),
                used: false
            });
            
            this.preloadedIcons.add(iconName);
        } catch (error) {
            console.warn(`Failed to preload icon: ${iconName}`, error);
        }
    }

    private async loadIconData(iconName: string): Promise<string> {
        // Implémentation fictive - à adapter selon votre système d'icônes
        // Cela pourrait être un fetch vers un API d'icônes, ou l'import d'un module
        return new Promise((resolve) => {
            setTimeout(() => {
                resolve(`<svg><!-- ${iconName} icon --></svg>`);
            }, 10);
        });
    }

    getIcon(iconName: string): IconCacheEntry | null {
        const cached = this.cache.get(iconName);
        
        if (cached) {
            // Marquer comme utilisé
            cached.used = true;
            cached.timestamp = Date.now();
            return cached;
        }
        
        return null;
    }

    async ensureIcon(iconName: string): Promise<string> {
        let cached = this.getIcon(iconName);
        
        if (!cached) {
            await this.preloadIcon(iconName);
            cached = this.getIcon(iconName);
        }
        
        return cached?.svg || '';
    }

    cleanupCache(): void {
        const now = Date.now();
        const toDelete: string[] = [];
        
        for (const [iconName, entry] of this.cache.entries()) {
            // Supprimer les icônes expirées et non utilisées
            if (now - entry.timestamp > this.cacheExpiryTime && !entry.used) {
                toDelete.push(iconName);
            }
        }
        
        toDelete.forEach(iconName => {
            this.cache.delete(iconName);
            this.preloadedIcons.delete(iconName);
        });
        
        // Limiter la taille du cache
        if (this.cache.size > this.maxCacheSize) {
            const sortedEntries = Array.from(this.cache.entries())
                .sort(([,a], [,b]) => a.timestamp - b.timestamp);
            
            const toRemove = sortedEntries.slice(0, this.cache.size - this.maxCacheSize);
            toRemove.forEach(([iconName]) => {
                this.cache.delete(iconName);
                this.preloadedIcons.delete(iconName);
            });
        }
    }

    getMetrics(): IconMetrics {
        const totalIcons = this.preloadedIcons.size;
        const cachedIcons = this.cache.size;
        const usedIcons = Array.from(this.cache.values()).filter(entry => entry.used).length;
        
        return {
            totalIcons,
            cachedIcons,
            cacheHitRate: totalIcons > 0 ? (usedIcons / totalIcons) * 100 : 0,
            memoryUsage: this.estimateMemoryUsage()
        };
    }

    private estimateMemoryUsage(): number {
        let totalSize = 0;
        
        for (const entry of this.cache.values()) {
            // Estimation approximative de la taille en bytes
            totalSize += entry.svg.length * 2; // UTF-16 approximation
        }
        
        return totalSize;
    }

    clearCache(): void {
        this.cache.clear();
        this.preloadedIcons.clear();
    }
}

// Instance globale du optimiseur d'icônes
const iconOptimizer = new IconOptimizer();

export function useIconOptimization() {
    const isInitialized = ref(false);
    const metrics = ref<IconMetrics>({
        totalIcons: 0,
        cachedIcons: 0,
        cacheHitRate: 0,
        memoryUsage: 0
    });

    // Initialiser l'optimiseur
    const initialize = async () => {
        if (isInitialized.value) return;
        
        try {
            await iconOptimizer.preloadCriticalIcons();
            isInitialized.value = true;
            updateMetrics();
        } catch (error) {
            console.error('Failed to initialize icon optimizer:', error);
        }
    };

    // Précharger les icônes pour un contexte spécifique
    const preloadForContext = async (context: 'dashboard' | 'clients' | 'projects' | 'events' | 'settings') => {
        await iconOptimizer.preloadContextualIcons(context);
        updateMetrics();
    };

    // Obtenir une icône (avec cache)
    const getIcon = async (iconName: string): Promise<string> => {
        const iconData = await iconOptimizer.ensureIcon(iconName);
        updateMetrics();
        return iconData;
    };

    // Nettoyer le cache
    const cleanup = () => {
        iconOptimizer.cleanupCache();
        updateMetrics();
    };

    // Vider le cache complètement
    const clearCache = () => {
        iconOptimizer.clearCache();
        updateMetrics();
    };

    // Mettre à jour les métriques
    const updateMetrics = () => {
        metrics.value = iconOptimizer.getMetrics();
    };

    // Computed
    const cacheStats = computed(() => ({
        hitRate: `${metrics.value.cacheHitRate.toFixed(1)}%`,
        memoryUsage: `${(metrics.value.memoryUsage / 1024).toFixed(1)} KB`,
        efficiency: metrics.value.cacheHitRate > 80 ? 'excellent' : 
                   metrics.value.cacheHitRate > 60 ? 'good' : 
                   metrics.value.cacheHitRate > 40 ? 'fair' : 'poor'
    }));

    // Auto-cleanup périodique
    let cleanupInterval: number | null = null;
    
    const startAutoCleanup = (intervalMs: number = 300000) => { // 5 minutes
        if (cleanupInterval) return;
        
        cleanupInterval = setInterval(() => {
            cleanup();
        }, intervalMs);
    };

    const stopAutoCleanup = () => {
        if (cleanupInterval) {
            clearInterval(cleanupInterval);
            cleanupInterval = null;
        }
    };

    return {
        // État
        isInitialized: computed(() => isInitialized.value),
        metrics: computed(() => metrics.value),
        cacheStats,
        
        // Actions
        initialize,
        preloadForContext,
        getIcon,
        cleanup,
        clearCache,
        startAutoCleanup,
        stopAutoCleanup,
        updateMetrics
    };
}

// Hook spécialisé pour les icônes dans les composants
export function useOptimizedIcon(iconName: string) {
    const iconData = ref<string>('');
    const isLoading = ref(false);
    const error = ref<string | null>(null);

    const loadIcon = async () => {
        if (iconData.value) return; // Déjà chargé
        
        isLoading.value = true;
        error.value = null;
        
        try {
            iconData.value = await iconOptimizer.ensureIcon(iconName);
        } catch (err: any) {
            error.value = err.message || 'Failed to load icon';
            console.error(`Failed to load icon ${iconName}:`, err);
        } finally {
            isLoading.value = false;
        }
    };

    // Auto-charger l'icône
    onMounted(() => {
        loadIcon();
    });

    return {
        iconData: computed(() => iconData.value),
        isLoading: computed(() => isLoading.value),
        error: computed(() => error.value),
        reload: loadIcon
    };
}

// Utilitaires pour l'optimisation des images
export function useImageOptimization() {
    // Lazy loading d'images avec intersection observer
    const createLazyImage = (src: string, options: {
        placeholder?: string;
        threshold?: number;
        rootMargin?: string;
    } = {}) => {
        const {
            placeholder = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 300"%3E%3Crect width="400" height="300" fill="%23f3f4f6"/%3E%3C/svg%3E',
            threshold = 0.1,
            rootMargin = '50px'
        } = options;

        const imageRef = ref<HTMLImageElement | null>(null);
        const isLoaded = ref(false);
        const isLoading = ref(false);
        const error = ref<string | null>(null);

        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting && !isLoaded.value && !isLoading.value) {
                        loadImage();
                    }
                });
            },
            { threshold, rootMargin }
        );

        const loadImage = () => {
            if (!imageRef.value || isLoaded.value || isLoading.value) return;
            
            isLoading.value = true;
            error.value = null;
            
            const img = new Image();
            
            img.onload = () => {
                if (imageRef.value) {
                    imageRef.value.src = src;
                    isLoaded.value = true;
                }
                isLoading.value = false;
            };
            
            img.onerror = () => {
                error.value = 'Failed to load image';
                isLoading.value = false;
            };
            
            img.src = src;
        };

        const setRef = (el: HTMLImageElement | null) => {
            imageRef.value = el;
            if (el) {
                el.src = placeholder;
                observer.observe(el);
            }
        };

        return {
            setRef,
            isLoaded,
            isLoading,
            error,
            observer
        };
    };

    // Générer des URLs d'images responsives
    const createResponsiveImageUrls = (baseSrc: string, sizes: number[]) => {
        return sizes.map(size => ({
            src: `${baseSrc}?w=${size}&q=80`,
            width: size
        }));
    };

    // Calculer la taille optimale d'image
    const getOptimalImageSize = (containerWidth: number, devicePixelRatio: number = window.devicePixelRatio || 1) => {
        const targetWidth = containerWidth * devicePixelRatio;
        const sizes = [320, 640, 768, 1024, 1280, 1600, 1920];
        
        return sizes.find(size => size >= targetWidth) || sizes[sizes.length - 1];
    };

    return {
        createLazyImage,
        createResponsiveImageUrls,
        getOptimalImageSize
    };
}

export default useIconOptimization;