<template>
    <div 
        class="skeleton-card bg-white rounded-lg shadow border border-gray-200 overflow-hidden"
        :class="[
            sizeClass,
            { 'animate-pulse': animated }
        ]"
    >
        <!-- Image/Media en haut -->
        <div v-if="hasImage" class="skeleton-image">
            <SkeletonLoader
                shape="rectangle"
                :height="imageHeight"
                width="100%"
                :animated="animated"
                class="rounded-none"
            />
        </div>

        <!-- Contenu principal -->
        <div :class="paddingClass">
            <!-- En-tête avec avatar optionnel -->
            <div v-if="hasHeader" class="flex items-center space-x-3 mb-4">
                <SkeletonLoader
                    v-if="hasAvatar"
                    shape="circle"
                    :size="avatarSize"
                    :animated="animated"
                />
                <div class="flex-1 space-y-2">
                    <SkeletonLoader
                        shape="text"
                        size="sm"
                        :width="titleWidth"
                        :animated="animated"
                    />
                    <SkeletonLoader
                        v-if="hasSubtitle"
                        shape="text"
                        size="xs"
                        :width="subtitleWidth"
                        :animated="animated"
                    />
                </div>
            </div>

            <!-- Titre principal (si pas d'en-tête) -->
            <div v-else-if="hasTitle" class="mb-4">
                <SkeletonLoader
                    shape="text"
                    :size="titleSize"
                    :width="titleWidth"
                    :animated="animated"
                    class="mb-2"
                />
                <SkeletonLoader
                    v-if="hasSubtitle"
                    shape="text"
                    size="sm"
                    :width="subtitleWidth"
                    :animated="animated"
                />
            </div>

            <!-- Contenu texte -->
            <div v-if="textLines > 0" class="space-y-2 mb-4">
                <SkeletonLoader
                    v-for="line in textLines"
                    :key="line"
                    shape="text"
                    size="sm"
                    :width="getTextLineWidth(line)"
                    :animated="animated"
                />
            </div>

            <!-- Métriques/Statistiques -->
            <div v-if="hasStats" class="grid grid-cols-3 gap-4 mb-4">
                <div
                    v-for="stat in statsCount"
                    :key="stat"
                    class="text-center"
                >
                    <SkeletonLoader
                        shape="text"
                        size="lg"
                        width="60%"
                        :animated="animated"
                        class="mb-1"
                    />
                    <SkeletonLoader
                        shape="text"
                        size="xs"
                        width="80%"
                        :animated="animated"
                    />
                </div>
            </div>

            <!-- Tags/Badges -->
            <div v-if="hasTags" class="flex flex-wrap gap-2 mb-4">
                <SkeletonLoader
                    v-for="tag in tagCount"
                    :key="tag"
                    shape="button"
                    size="xs"
                    :width="getTagWidth()"
                    :animated="animated"
                    class="rounded-full"
                />
            </div>

            <!-- Actions en bas -->
            <div v-if="hasActions" class="flex items-center justify-between pt-2 border-t border-gray-100">
                <div class="flex space-x-2">
                    <SkeletonLoader
                        v-for="action in actionCount"
                        :key="action"
                        shape="button"
                        size="sm"
                        :width="getActionWidth()"
                        :animated="animated"
                    />
                </div>
                <SkeletonLoader
                    v-if="hasMenuAction"
                    shape="circle"
                    size="sm"
                    :animated="animated"
                />
            </div>
        </div>

        <!-- Footer optionnel -->
        <div v-if="hasFooter" class="bg-gray-50 px-6 py-3 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <SkeletonLoader
                    shape="text"
                    size="xs"
                    width="120px"
                    :animated="animated"
                />
                <SkeletonLoader
                    shape="text"
                    size="xs"
                    width="80px"
                    :animated="animated"
                />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import SkeletonLoader from './SkeletonLoader.vue';

interface Props {
    // Structure de la carte
    hasImage?: boolean;
    hasHeader?: boolean;
    hasTitle?: boolean;
    hasSubtitle?: boolean;
    hasAvatar?: boolean;
    hasStats?: boolean;
    hasTags?: boolean;
    hasActions?: boolean;
    hasMenuAction?: boolean;
    hasFooter?: boolean;
    
    // Taille et apparence
    size?: 'sm' | 'md' | 'lg';
    imageHeight?: string;
    titleSize?: 'xs' | 'sm' | 'md' | 'lg' | 'xl';
    avatarSize?: 'xs' | 'sm' | 'md' | 'lg' | 'xl';
    
    // Contenu
    textLines?: number;
    statsCount?: number;
    tagCount?: number;
    actionCount?: number;
    
    // Largeurs personnalisées
    titleWidth?: string;
    subtitleWidth?: string;
    
    // Animation
    animated?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    hasImage: false,
    hasHeader: false,
    hasTitle: true,
    hasSubtitle: false,
    hasAvatar: false,
    hasStats: false,
    hasTags: false,
    hasActions: false,
    hasMenuAction: false,
    hasFooter: false,
    size: 'md',
    imageHeight: '160px',
    titleSize: 'md',
    avatarSize: 'md',
    textLines: 3,
    statsCount: 3,
    tagCount: 3,
    actionCount: 2,
    titleWidth: '80%',
    subtitleWidth: '60%',
    animated: true
});

// Classes de taille
const sizeClass = computed(() => {
    const sizes = {
        sm: 'max-w-sm',
        md: 'max-w-md',
        lg: 'max-w-lg'
    };
    return sizes[props.size];
});

// Classes de padding
const paddingClass = computed(() => {
    const padding = {
        sm: 'p-4',
        md: 'p-6',
        lg: 'p-8'
    };
    return padding[props.size];
});

// Générateurs de largeurs aléatoires
const getTextLineWidth = (lineNumber: number): string => {
    // Dernière ligne plus courte pour plus de réalisme
    if (lineNumber === props.textLines) {
        return ['50%', '60%', '70%'][Math.floor(Math.random() * 3)];
    }
    return ['85%', '90%', '95%'][Math.floor(Math.random() * 3)];
};

const getTagWidth = (): string => {
    return ['60px', '80px', '100px'][Math.floor(Math.random() * 3)];
};

const getActionWidth = (): string => {
    return ['80px', '100px', '120px'][Math.floor(Math.random() * 3)];
};
</script>

<style scoped>
.skeleton-card {
    /* Optimisation */
    contain: layout style paint;
}

.skeleton-image {
    /* S'assurer que l'image skeleton prend toute la largeur */
    width: 100%;
    display: block;
}

/* Variantes de taille */
.skeleton-card.max-w-sm {
    width: 100%;
    max-width: 24rem; /* 384px */
}

.skeleton-card.max-w-md {
    width: 100%;
    max-width: 28rem; /* 448px */
}

.skeleton-card.max-w-lg {
    width: 100%;
    max-width: 32rem; /* 512px */
}
</style>