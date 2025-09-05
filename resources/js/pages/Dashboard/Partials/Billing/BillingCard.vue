<template>
    <component
        :is="link ? Link : 'div'"
        :href="link"
        :class="linkClasses"
    >
        <Card
            class="h-full rounded-xl border border-gray-200/60 bg-white shadow-sm transition-all duration-300 py-0"
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
                            <div class="md:text-2xl text-xl font-bold text-gray-900">
                                {{ formatCurrency(value) }}
                            </div>

                            <!-- Description -->
                            <div v-if="description" class="mt-2 flex items-center gap-1.5">
                                <div class="flex items-center gap-1">
                                    <div class="h-1 w-1 rounded-full bg-gray-500"></div>
                                    <span class="text-xs font-medium text-gray-600">
                                        {{ description }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </CardContent>
        </Card>
    </component>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { Card, CardContent } from '@/components/ui/card'
import OptimizedIcon from '@/components/OptimizedIcon.vue'

// Types de couleur
type Color = 'purple' | 'orange' | 'blue' | 'green' | 'red';

interface Props {
    title: string
    value: number
    icon: string
    color: Color
    link?: string
    description?: string | null
}

const props = defineProps<Props>()

// Configuration des couleurs
const colorConfig = {
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
    blue: {
        iconBg: 'bg-blue-50',
        iconColor: 'text-blue-600',
        hover: 'group-hover:border-blue-200',
        navIcon: 'group-hover:text-blue-500'
    },
    green: {
        iconBg: 'bg-green-50',
        iconColor: 'text-green-600',
        hover: 'group-hover:border-green-200',
        navIcon: 'group-hover:text-green-500'
    },
    red: {
        iconBg: 'bg-red-50',
        iconColor: 'text-red-600',
        hover: 'group-hover:border-red-200',
        navIcon: 'group-hover:text-red-500'
    }
};

// Computed properties
const config = computed(() => colorConfig[props.color] || colorConfig.blue);

const linkClasses = computed(() => {
    return props.link ? 'group h-full' : 'h-full';
});

const cardClasses = computed(() => {
    return props.link
        ? `group-hover:-translate-y-0.5 group-hover:shadow-md ${config.value.hover}`
        : '';
});

const iconBgClasses = computed(() => config.value.iconBg);
const iconColorClasses = computed(() => config.value.iconColor);
// const navIconClasses = computed(() => config.value.navIcon); // Réservé pour futurs éléments de navigation

const formatCurrency = (amount: number): string => {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
    }).format(amount).replace(/[\u202F\u00A0]/g, '\u2004')
}
</script>
