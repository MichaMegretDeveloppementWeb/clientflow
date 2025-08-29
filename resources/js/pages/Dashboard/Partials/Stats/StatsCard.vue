<template>
    <component
        :is="href ? Link : 'div'"
        :href="href"
        :class="linkClasses"
    >
        <Card
            class="h-full rounded-xl border border-gray-200/60 bg-white shadow-sm transition-all duration-300"
            :class="cardClasses"
        >
            <CardContent class="flex h-full flex-col p-4 sm:p-6">
                <div class="flex flex-1 items-start justify-between">
                    <div class="flex h-full flex-1 flex-col justify-start">
                        <!-- Header avec icône -->
                        <div class="mb-3 flex items-center gap-2">
                            <div
                                class="rounded-lg p-2"
                                :class="iconBgClasses"
                            >
                                <OptimizedIcon
                                    :name="icon"
                                    :size="16"
                                    :class="iconColorClasses"
                                    preload
                                />
                            </div>
                            <span class="text-sm font-medium text-gray-600">
                                {{ title }}
                            </span>
                        </div>

                        <!-- Valeur principale -->
                        <div class="space-y-1">
                            <div class="text-2xl font-bold text-gray-900">
                                {{ formattedValue }}
                            </div>

                            <!-- Croissance/métrique additionnelle -->
                            <div v-if="hasGrowth" class="mt-2 flex items-center gap-1.5">
                                <div class="flex items-center gap-1">
                                    <div
                                        class="h-1 w-1 rounded-full"
                                        :class="growthDotColor"
                                    ></div>
                                    <span
                                        class="text-xs font-medium"
                                        :class="growthTextColor"
                                    >
                                        {{ growthPrefix }}{{ growth }}
                                    </span>
                                </div>
                                <span class="text-xs text-gray-500">
                                    {{ growthLabel }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </CardContent>
        </Card>
    </component>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { Card, CardContent } from '@/components/ui/card';
import OptimizedIcon from '@/components/OptimizedIcon.vue';

// Types de couleur
type Color = 'blue' | 'emerald' | 'purple' | 'orange' | 'red' | 'yellow';

// Props
interface Props {
    title: string;
    value: number | string;
    icon: string;
    color?: Color;
    growth?: number;
    growthLabel?: string;
    href?: string;
    formatAsNumber?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    color: 'blue',
    formatAsNumber: true
});

// Classes de couleur
const colorConfig = {
    blue: {
        iconBg: 'bg-blue-50',
        iconColor: 'text-blue-600',
        hover: 'group-hover:border-blue-200',
        navIcon: 'group-hover:text-blue-500'
    },
    emerald: {
        iconBg: 'bg-emerald-50',
        iconColor: 'text-emerald-600',
        hover: 'group-hover:border-emerald-200',
        navIcon: 'group-hover:text-emerald-500'
    },
    purple: {
        iconBg: 'bg-purple-50',
        iconColor: 'text-purple-600',
        hover: 'group-hover:border-purple-200',
        navIcon: 'group-hover:text-purple-500'
    },
    orange: {
        iconBg: 'bg-orange-50',
        iconColor: 'text-orange-600',
        hover: 'group-hover:border-orange-200',
        navIcon: 'group-hover:text-orange-500'
    },
    red: {
        iconBg: 'bg-red-50',
        iconColor: 'text-red-600',
        hover: 'group-hover:border-red-200',
        navIcon: 'group-hover:text-red-500'
    },
    yellow: {
        iconBg: 'bg-yellow-50',
        iconColor: 'text-yellow-600',
        hover: 'group-hover:border-yellow-200',
        navIcon: 'group-hover:text-yellow-500'
    }
};

// Computed properties
const config = computed(() => colorConfig[props.color] || colorConfig.blue);

const linkClasses = computed(() => {
    return props.href ? 'group h-full' : 'h-full';
});

const cardClasses = computed(() => {
    return props.href
        ? `group-hover:-translate-y-0.5 group-hover:shadow-md ${config.value.hover}`
        : '';
});

const iconBgClasses = computed(() => config.value.iconBg);
const iconColorClasses = computed(() => config.value.iconColor);
const navIconClasses = computed(() => config.value.navIcon);

const formattedValue = computed(() => {
    if (typeof props.value === 'string') {
        return props.value;
    }

    // Vérifier si la valeur est valide
    if (props.value === undefined || props.value === null || isNaN(Number(props.value))) {
        return '0';
    }

    if (props.formatAsNumber) {
        return new Intl.NumberFormat('fr-FR').format(props.value);
    }

    return props.value.toString();
});

const hasGrowth = computed(() => {
    return typeof props.growth === 'number' && props.growthLabel;
});

const growthPrefix = computed(() => {
    if (typeof props.growth !== 'number') return '';
    return props.growth >= 0 ? '+' : '';
});

const growthTextColor = computed(() => {
    if (typeof props.growth !== 'number') return 'text-gray-600';

    if (props.growth > 0) return 'text-emerald-600';
    if (props.growth < 0) return 'text-red-600';
    return 'text-gray-600';
});

const growthDotColor = computed(() => {
    if (typeof props.growth !== 'number') return 'bg-gray-500';

    if (props.growth > 0) return 'bg-emerald-500';
    if (props.growth < 0) return 'bg-red-500';
    return 'bg-gray-500';
});
</script>

<style scoped>
.stats-card {
    contain: layout style paint;
}
</style>
