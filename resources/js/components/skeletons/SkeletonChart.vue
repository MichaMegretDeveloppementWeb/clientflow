<template>
    <div 
        class="skeleton-chart bg-white rounded-lg shadow border border-gray-200 p-6"
        :class="{ 'animate-pulse': animated }"
    >
        <!-- En-tête du graphique -->
        <div class="flex items-center justify-between mb-6">
            <div class="space-y-2">
                <SkeletonLoader
                    shape="text"
                    size="lg"
                    width="200px"
                    :animated="animated"
                />
                <SkeletonLoader
                    v-if="hasSubtitle"
                    shape="text"
                    size="sm"
                    width="150px"
                    :animated="animated"
                />
            </div>
            
            <!-- Contrôles du graphique -->
            <div v-if="hasControls" class="flex space-x-2">
                <SkeletonLoader
                    v-for="control in controlCount"
                    :key="control"
                    shape="button"
                    size="sm"
                    width="100px"
                    :animated="animated"
                />
            </div>
        </div>

        <!-- Métriques principales -->
        <div v-if="hasMetrics" class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div
                v-for="metric in metricCount"
                :key="metric"
                class="text-center p-3 bg-gray-50 rounded-lg"
            >
                <SkeletonLoader
                    shape="text"
                    size="xl"
                    width="80%"
                    :animated="animated"
                    class="mb-2"
                />
                <SkeletonLoader
                    shape="text"
                    size="xs"
                    width="60%"
                    :animated="animated"
                />
            </div>
        </div>

        <!-- Zone du graphique principal -->
        <div class="relative" :style="{ height: chartHeight }">
            <!-- Axes Y (labels) -->
            <div class="absolute left-0 top-0 bottom-0 w-12 flex flex-col justify-between py-4">
                <SkeletonLoader
                    v-for="label in yAxisLabels"
                    :key="label"
                    shape="text"
                    size="xs"
                    width="80%"
                    :animated="animated"
                />
            </div>

            <!-- Zone du graphique -->
            <div class="ml-12 mr-4 h-full relative bg-gray-50 rounded">
                <!-- Lignes de grille horizontales -->
                <div
                    v-for="line in gridLines"
                    :key="line"
                    class="absolute left-0 right-0 h-px bg-gray-200"
                    :style="{ top: `${(line - 1) * (100 / (gridLines - 1))}%` }"
                />

                <!-- Simulation de données de graphique selon le type -->
                <div v-if="chartType === 'line'" class="absolute inset-0 p-4">
                    <!-- Points de données simulés -->
                    <div class="relative h-full">
                        <div
                            v-for="point in dataPoints"
                            :key="point"
                            class="absolute w-2 h-2 bg-blue-400 rounded-full animate-pulse"
                            :style="{
                                left: `${(point - 1) * (100 / (dataPoints - 1))}%`,
                                top: `${Math.random() * 60 + 10}%`
                            }"
                        />
                    </div>
                </div>

                <div v-else-if="chartType === 'bar'" class="absolute inset-0 p-4 flex items-end space-x-2">
                    <!-- Barres simulées -->
                    <div
                        v-for="bar in dataPoints"
                        :key="bar"
                        class="flex-1 bg-blue-300 rounded-t animate-pulse"
                        :style="{ height: `${Math.random() * 80 + 20}%` }"
                    />
                </div>

                <div v-else-if="chartType === 'pie'" class="absolute inset-0 flex items-center justify-center">
                    <!-- Cercle pour graphique en secteurs -->
                    <div class="w-40 h-40 rounded-full border-8 border-gray-300 border-t-blue-400 border-r-green-400 border-b-yellow-400 border-l-red-400 animate-pulse" />
                </div>

                <div v-else class="absolute inset-0 flex items-center justify-center">
                    <!-- Forme générique -->
                    <SkeletonLoader
                        shape="rectangle"
                        width="80%"
                        height="60%"
                        :animated="animated"
                    />
                </div>
            </div>

            <!-- Axe X (labels) -->
            <div class="ml-12 mr-4 mt-2 flex justify-between">
                <SkeletonLoader
                    v-for="label in xAxisLabels"
                    :key="label"
                    shape="text"
                    size="xs"
                    width="60px"
                    :animated="animated"
                />
            </div>
        </div>

        <!-- Légende -->
        <div v-if="hasLegend" class="flex flex-wrap justify-center gap-4 mt-6 pt-4 border-t border-gray-100">
            <div
                v-for="legend in legendItems"
                :key="legend"
                class="flex items-center space-x-2"
            >
                <div class="w-3 h-3 rounded animate-pulse" :class="getLegendColorClass(legend)" />
                <SkeletonLoader
                    shape="text"
                    size="xs"
                    width="80px"
                    :animated="animated"
                />
            </div>
        </div>

        <!-- Pied du graphique -->
        <div v-if="hasFooter" class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-between">
            <SkeletonLoader
                shape="text"
                size="xs"
                width="150px"
                :animated="animated"
            />
            <SkeletonLoader
                shape="button"
                size="xs"
                width="100px"
                :animated="animated"
            />
        </div>
    </div>
</template>

<script setup lang="ts">
import SkeletonLoader from './SkeletonLoader.vue';

interface Props {
    chartType?: 'line' | 'bar' | 'pie' | 'area' | 'scatter' | 'generic';
    chartHeight?: string;
    hasSubtitle?: boolean;
    hasControls?: boolean;
    hasMetrics?: boolean;
    hasLegend?: boolean;
    hasFooter?: boolean;
    controlCount?: number;
    metricCount?: number;
    dataPoints?: number;
    gridLines?: number;
    xAxisLabels?: number;
    yAxisLabels?: number;
    legendItems?: number;
    animated?: boolean;
}

withDefaults(defineProps<Props>(), {
    chartType: 'line',
    chartHeight: '300px',
    hasSubtitle: false,
    hasControls: true,
    hasMetrics: true,
    hasLegend: true,
    hasFooter: false,
    controlCount: 3,
    metricCount: 4,
    dataPoints: 8,
    gridLines: 5,
    xAxisLabels: 7,
    yAxisLabels: 5,
    legendItems: 3,
    animated: true
});

// Classes de couleur pour la légende
const getLegendColorClass = (index: number): string => {
    const colors = [
        'bg-blue-400',
        'bg-green-400',
        'bg-yellow-400',
        'bg-red-400',
        'bg-purple-400',
        'bg-pink-400'
    ];
    return colors[index % colors.length];
};
</script>

<style scoped>
.skeleton-chart {
    /* Optimisation */
    contain: layout style paint;
}

/* Animation des points de données */
@keyframes data-point-pulse {
    0%, 100% {
        opacity: 0.4;
        transform: scale(1);
    }
    50% {
        opacity: 1;
        transform: scale(1.2);
    }
}

.skeleton-chart .animate-pulse {
    animation: data-point-pulse 2s ease-in-out infinite;
}
</style>