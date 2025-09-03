<template>
    <span 
        :class="iconClasses"
        :style="iconStyles"
        :aria-label="ariaLabel"
        role="img"
    >
        <!-- Icône chargée -->
        <Suspense v-if="!error">
            <template #default>
                <LucideIcon
                    v-if="iconComponent"
                    :is="iconComponent"
                    :size="computedSize"
                    :stroke-width="strokeWidth"
                    :class="baseClasses"
                />
            </template>
            
            <!-- Fallback pendant le chargement -->
            <template #fallback>
                <div 
                    :class="[baseClasses, 'animate-pulse bg-gray-300 rounded']"
                    :style="{ width: `${computedSize}px`, height: `${computedSize}px` }"
                />
            </template>
        </Suspense>
        
        <!-- Fallback en cas d'erreur -->
        <div 
            v-else
            :class="[baseClasses, 'bg-gray-200 rounded flex items-center justify-center']"
            :style="{ width: `${computedSize}px`, height: `${computedSize}px` }"
            :title="`Icône '${name}' indisponible`"
        >
            <span class="text-xs text-gray-500">?</span>
        </div>
    </span>
</template>

<script setup lang="ts">
import { computed, defineAsyncComponent, ref, watch, onMounted } from 'vue';
import { useIconOptimization } from '@/composables/useIconOptimization';

interface Props {
    name: string;
    size?: number | string;
    class?: string;
    strokeWidth?: number;
    color?: string;
    ariaLabel?: string;
    preload?: boolean;
    fallback?: string;
}

const props = withDefaults(defineProps<Props>(), {
    size: 20,
    strokeWidth: 2,
    preload: false,
    fallback: 'help-circle'
});

// État
const iconComponent = ref<any>(null);
const isLoading = ref(false);
const error = ref<string | null>(null);

// Hook d'optimisation
const { getIcon } = useIconOptimization();

// Calculs
const computedSize = computed(() => {
    if (typeof props.size === 'number') return props.size;
    if (typeof props.size === 'string') {
        // Convertir les tailles textuelles en pixels
        const sizeMap: Record<string, number> = {
            'xs': 12,
            'sm': 16,
            'md': 20,
            'lg': 24,
            'xl': 28,
            '2xl': 32
        };
        return sizeMap[props.size] || 20;
    }
    return 20;
});

const baseClasses = computed(() => [
    'inline-block',
    'shrink-0',
    props.class
]);

const iconClasses = computed(() => [
    'icon-wrapper',
    'inline-flex',
    'items-center',
    'justify-center'
]);

const iconStyles = computed(() => ({
    color: props.color || 'currentColor',
    width: `${computedSize.value}px`,
    height: `${computedSize.value}px`
}));

const ariaLabel = computed(() => props.ariaLabel || `Icône ${props.name}`);

// Conversion des noms d'icônes kebab-case vers PascalCase
const toPascalCase = (str: string): string => {
    return str
        .split('-')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join('');
};

// Charger l'icône de manière asynchrone
const loadIcon = async (iconName: string) => {
    if (isLoading.value) return;
    
    isLoading.value = true;
    error.value = null;
    
    try {
        // Convertir le nom d'icône en format PascalCase pour Lucide
        const pascalName = toPascalCase(iconName);
        
        // Charger dynamiquement l'icône depuis lucide-vue-next
        const iconModule = await import('lucide-vue-next');
        const IconComponent = iconModule[pascalName];
        
        if (!IconComponent) {
            throw new Error(`Icône '${iconName}' introuvable`);
        }
        
        iconComponent.value = IconComponent;
        
        // Marquer l'icône comme utilisée dans le cache
        await getIcon(iconName);
    } catch (err: any) {
        console.warn(`Impossible de charger l'icône '${iconName}':`, err);
        error.value = err.message;
        
        // Essayer de charger l'icône de fallback
        if (iconName !== props.fallback && props.fallback) {
            try {
                const fallbackPascal = toPascalCase(props.fallback);
                const iconModule = await import('lucide-vue-next');
                const FallbackComponent = iconModule[fallbackPascal];
                
                if (FallbackComponent) {
                    iconComponent.value = FallbackComponent;
                    error.value = null;
                }
            } catch (fallbackErr) {
                console.error('Impossible de charger l\'icône de fallback:', fallbackErr);
            }
        }
    } finally {
        isLoading.value = false;
    }
};

// Watchers
watch(
    () => props.name,
    (newName) => {
        iconComponent.value = null;
        loadIcon(newName);
    },
    { immediate: true }
);

// Préchargement si demandé
onMounted(async () => {
    if (props.preload) {
        try {
            await getIcon(props.name);
        } catch (err) {
            console.warn(`Échec du préchargement de l'icône '${props.name}':`, err);
        }
    }
});

// Créer un composant Lucide dynamique avec optimisations
const LucideIcon = defineAsyncComponent({
    loader: () => Promise.resolve(iconComponent.value),
    delay: 0,
    timeout: 3000,
    suspensible: true,
    onError: (error, retry, fail) => {
        console.error('Erreur lors du chargement de l\'icône:', error);
        fail(); // Ne pas retry automatiquement pour éviter les boucles
    }
});
</script>

<style scoped>
.icon-wrapper {
    /* Optimisations de performance */
    contain: layout style;
    will-change: auto;
}

/* Animations fluides pour les changements d'icônes */
.icon-wrapper svg {
    transition: opacity 0.2s ease-in-out;
}

/* Mode sombre */
@media (prefers-color-scheme: dark) {
    .icon-wrapper .bg-gray-200 {
        background-color: rgb(55 65 81);
    }
    
    .icon-wrapper .bg-gray-300 {
        background-color: rgb(75 85 99);
    }
    
    .icon-wrapper .text-gray-500 {
        color: rgb(156 163 175);
    }
}
</style>