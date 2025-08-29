<template>
    <div
        class="skeleton-table bg-white rounded-lg shadow border border-gray-200"
        :class="{
            'compact': variant === 'compact',
            'spacious': variant === 'spacious'
        }"
    >
        <!-- En-tête du tableau -->
        <div class="border-b border-gray-200 bg-gray-50" :class="{
            'px-4 py-2': variant === 'compact',
            'px-8 py-4': variant === 'spacious',
            'px-6 py-3': variant === 'default'
        }">
            <div class="grid gap-4" :style="{ gridTemplateColumns }">
                <SkeletonLoader
                    v-for="column in columns"
                    :key="column.key"
                    shape="text"
                    size="sm"
                    :width="column.headerWidth || '60%'"
                    class="bg-gray-300"
                />
            </div>
        </div>

        <!-- Corps du tableau -->
        <div class="divide-y divide-gray-100">
            <div
                v-for="row in rowCount"
                :key="row"
                :class="[
                    'hover:bg-gray-50 transition-colors',
                    {
                        'px-4 py-3': variant === 'compact',
                        'px-8 py-6': variant === 'spacious',
                        'px-6 py-4': variant === 'default'
                    },
                    {
                        'animate-pulse': animated
                    }
                ]"
            >
                <div class="grid gap-4 items-center" :style="{ gridTemplateColumns }">
                    <div
                        v-for="(column, index) in columns"
                        :key="`${row}-${column.key}`"
                        class="flex items-center"
                    >
                        <!-- Avatar pour la première colonne si spécifié -->
                        <div
                            v-if="column.hasAvatar && index === 0"
                            class="flex items-center space-x-3"
                        >
                            <SkeletonLoader
                                shape="circle"
                                size="sm"
                                :animated="animated"
                            />
                            <SkeletonLoader
                                shape="text"
                                size="sm"
                                :width="column.cellWidth || '70%'"
                                :animated="animated"
                            />
                        </div>

                        <!-- Badge pour les colonnes de statut -->
                        <SkeletonLoader
                            v-else-if="column.cellType === 'badge'"
                            shape="button"
                            size="xs"
                            :width="column.cellWidth || '80px'"
                            :animated="animated"
                            class="rounded-full"
                        />

                        <!-- Bouton pour les colonnes d'action -->
                        <div
                            v-else-if="column.cellType === 'actions'"
                            class="flex space-x-2"
                        >
                            <SkeletonLoader
                                v-for="action in (column.actionCount || 2)"
                                :key="action"
                                shape="circle"
                                size="xs"
                                :animated="animated"
                            />
                        </div>

                        <!-- Contenu numérique -->
                        <SkeletonLoader
                            v-else-if="column.cellType === 'number'"
                            shape="text"
                            size="sm"
                            :width="column.cellWidth || '40%'"
                            :animated="animated"
                            class="ml-auto"
                        />

                        <!-- Contenu texte par défaut -->
                        <SkeletonLoader
                            v-else
                            shape="text"
                            size="sm"
                            :width="getRandomWidth(column.cellWidth)"
                            :animated="animated"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination en bas -->
        <div
            v-if="showPagination"
            :class="[
                'border-t border-gray-200 bg-gray-50',
                {
                    'px-4 py-2': variant === 'compact',
                    'px-8 py-4': variant === 'spacious',
                    'px-6 py-3': variant === 'default'
                }
            ]"
        >
            <div class="flex items-center justify-between">
                <SkeletonLoader
                    shape="text"
                    size="sm"
                    width="150px"
                    :animated="animated"
                />
                <div class="flex space-x-2">
                    <SkeletonLoader
                        v-for="page in 3"
                        :key="page"
                        shape="button"
                        size="xs"
                        width="32px"
                        :animated="animated"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import SkeletonLoader from './SkeletonLoader.vue';

interface SkeletonTableColumn {
    key: string;
    width?: string;
    headerWidth?: string;
    cellWidth?: string;
    cellType?: 'text' | 'badge' | 'actions' | 'number';
    hasAvatar?: boolean;
    actionCount?: number;
}

interface Props {
    columns: SkeletonTableColumn[];
    rowCount?: number;
    showPagination?: boolean;
    animated?: boolean;
    variant?: 'default' | 'compact' | 'spacious';
}

const props = withDefaults(defineProps<Props>(), {
    rowCount: 5,
    showPagination: true,
    animated: true,
    variant: 'default'
});

// Calcul de la grille
const gridTemplateColumns = computed(() => {
    return props.columns
        .map(col => col.width || '1fr')
        .join(' ');
});

// Générer des largeurs aléatoires pour plus de réalisme
const getRandomWidth = (baseWidth?: string): string => {
    if (baseWidth) return baseWidth;

    const widths = ['60%', '70%', '80%', '90%'];
    return widths[Math.floor(Math.random() * widths.length)];
};
</script>

<style scoped>
.skeleton-table {
    /* Optimisation pour le skeleton */
    contain: layout style paint;
}
</style>
