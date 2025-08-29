<template>
    <div ref="wrapperRef" class="lazy-wrapper">
        <!-- Skeleton loader pendant le chargement -->
        <div v-if="isLoading" class="animate-pulse">
            <slot name="loading">
                <div class="space-y-3">
                    <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                    <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                    <div class="h-4 bg-gray-200 rounded w-2/3"></div>
                </div>
            </slot>
        </div>

        <!-- Composant d'erreur -->
        <div v-else-if="hasError" class="p-4 bg-red-50 border border-red-200 rounded-lg">
            <slot name="error" :error="error" :retry="retry">
                <div class="flex items-center space-x-2">
                    <Icon name="alert-circle" class="h-5 w-5 text-red-500" />
                    <div>
                        <p class="text-sm font-medium text-red-800">Erreur de chargement</p>
                        <p class="text-xs text-red-600">{{ error?.message || 'Composant indisponible' }}</p>
                    </div>
                    <Button
                        variant="outline"
                        size="sm"
                        @click="retry"
                        class="ml-auto"
                    >
                        <Icon name="refresh-cw" class="h-4 w-4 mr-1" />
                        Réessayer
                    </Button>
                </div>
            </slot>
        </div>

        <!-- Composant chargé -->
        <Suspense v-else-if="isVisible && component">
            <component :is="component" v-bind="componentProps" />
            <template #fallback>
                <div class="animate-pulse">
                    <slot name="loading">
                        <div class="space-y-3">
                            <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                            <div class="h-4 bg-gray-200 rounded w-1/2"></div>
                            <div class="h-4 bg-gray-200 rounded w-2/3"></div>
                        </div>
                    </slot>
                </div>
            </template>
        </Suspense>

        <!-- Placeholder quand pas encore visible -->
        <div v-else class="min-h-[200px] flex items-center justify-center bg-gray-50 rounded-lg">
            <slot name="placeholder">
                <div class="text-center text-gray-500">
                    <Icon name="eye" class="h-8 w-8 mx-auto mb-2 opacity-50" />
                    <p class="text-sm">Contenu en cours de chargement...</p>
                </div>
            </slot>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, type Component } from 'vue';
import { Button } from '@/components/ui/button';
import Icon from '@/components/Icon.vue';

interface Props {
    loader: () => Promise<any>;
    componentProps?: Record<string, any>;
    threshold?: number;
    rootMargin?: string;
    eager?: boolean; // Charger immédiatement sans attendre la visibilité
}

const props = withDefaults(defineProps<Props>(), {
    componentProps: () => ({}),
    threshold: 0.1,
    rootMargin: '50px',
    eager: false
});

const wrapperRef = ref<HTMLElement>();
const component = ref<Component | null>(null);
const isVisible = ref(props.eager);
const isLoading = ref(false);
const hasError = ref(false);
const error = ref<Error | null>(null);
const observer = ref<IntersectionObserver | null>(null);

const loadComponent = async () => {
    if (component.value || isLoading.value) return;
    
    isLoading.value = true;
    hasError.value = false;
    error.value = null;

    try {
        const loadedComponent = await props.loader();
        component.value = loadedComponent.default || loadedComponent;
    } catch (err) {
        hasError.value = true;
        error.value = err as Error;
        console.error('Failed to load lazy component:', err);
    } finally {
        isLoading.value = false;
    }
};

const retry = () => {
    hasError.value = false;
    error.value = null;
    loadComponent();
};

const setupIntersectionObserver = () => {
    if (!wrapperRef.value || props.eager) return;

    observer.value = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting && !isVisible.value) {
                    isVisible.value = true;
                    loadComponent();
                    // Déconnecter l'observer après le premier chargement
                    observer.value?.disconnect();
                }
            });
        },
        {
            threshold: props.threshold,
            rootMargin: props.rootMargin
        }
    );

    observer.value.observe(wrapperRef.value);
};

onMounted(() => {
    if (props.eager) {
        isVisible.value = true;
        loadComponent();
    } else {
        setupIntersectionObserver();
    }
});

onUnmounted(() => {
    observer.value?.disconnect();
});
</script>

<style scoped>
.lazy-wrapper {
    /* Ensure proper layout during loading */
    min-height: fit-content;
}
</style>