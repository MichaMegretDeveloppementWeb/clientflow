<template>
    <Link
        :href="activity.link"
        :class="[
            'group flex items-start gap-3 p-3 sm:p-4 py-4 sm:py-6',
            props.customClass || 'rounded-lg bg-gray-200 hover:bg-gray-300 hover:shadow-sm'
        ]"
    >
        <!-- Icône de type -->
<!--        <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-xl ring-1 ring-inset" :class="getIconBgClasses()">
            <OptimizedIcon
                :name="activity.icon"
                :size="16"
                :class="activity.icon_color"
                preload
            />
        </div>-->

        <!-- Contenu principal -->
        <div class="min-w-0 flex-1">
            <!-- Titre avec badge type intégré -->
            <div class="mb-3 sm:mb-2">
                <div class="flex flex-col md:flex-row md:items-center justify-start flex-wrap gap-2 md:gap-3">
                    <!-- Ligne 1: Badge type + titre -->
                    <div class="flex items-center gap-3">
                        <span
                            class="inline-flex items-center px-2 py-0.5 rounded-full text-sm font-medium border flex-shrink-0"
                            :class="getTypeBadgeClasses()"
                        >
                            {{ activity.entity_type }}
                        </span>
                        <h4 class="break-words font-medium leading-tight text-gray-900 text-sm sm:text-base min-w-0">
                            {{ activity.name }}
                        </h4>
                    </div>

                    <!-- Ligne 2 mobile: Company si présente -->
                    <span v-if="activity.company" class="text-sm text-gray-500 ml-0 block sm:hidden">
                        ({{ activity.company }})
                    </span>

                    <!-- Ligne 2 desktop: Company inline + badge statut -->
                    <div class="flex items-center justify-between">
                        <span v-if="activity.company" class="text-sm text-gray-500">
                            ({{ activity.company }})
                        </span>
                    </div>

                    <div class="flex items-center gap-2">

                        <div class="inline-flex items-center gap-1 rounded-full text-md font-medium" :class="getStatusBadgeClasses()">
                            <OptimizedIcon :name="getStatusIcon()" :size="15" />
                            {{ activity.status_label }}
                        </div>

                        <!-- Montant si facturation -->
                        <div
                            v-if="activity.amount"
                            class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700 w-fit order-1 sm:order-2"
                        >
                            <OptimizedIcon name="euro" :size="12" />
                            {{ activity.formatted_amount }}
                        </div>

                    </div>

                </div>
            </div>


            <!-- Informations contextuelles : projet/client + montant + date -->
            <div class="space-y-2 sm:space-y-1">
                <!-- Projet/Client (toujours visible sur mobile, masqué sur desktop si pas d'info) -->
                <div v-if="getParentInfo()" class="break-words text-sm text-gray-600">
                    {{ getParentInfo() }}
                </div>

                <!-- Date -->
                <div class="flex items-center gap-2 order-2 sm:order-1 mt-3">
                    <OptimizedIcon name="clock" :size="12" class="text-gray-400 flex-shrink-0" />
                    <span class="text-sm text-gray-500">
                        {{ activity.time_ago }}
                    </span>
            </div>
            </div>
        </div>
    </Link>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import OptimizedIcon from '@/components/OptimizedIcon.vue';
import type { Activity } from '@/types/dashboard/activities';

// Props
interface Props {
    activity: Activity;
    customClass?: string;
}

const props = defineProps<Props>();

// Obtenir les classes du badge de type
const getTypeBadgeClasses = (): string => {
    switch (props.activity.type) {
        case 'billing':
            return 'bg-violet-50 text-violet-700 border-violet-200';
        case 'step':
            return 'bg-blue-50 text-blue-700 border-blue-200';
        case 'project':
            return 'bg-indigo-50 text-indigo-700 border-indigo-200';
        case 'client':
            return 'bg-amber-50 text-amber-700 border-amber-200';
        default:
            return 'bg-gray-50 text-gray-700 border-gray-200';
    }
};

// Obtenir les classes du badge de statut
const getStatusBadgeClasses = (): string => {
    switch (props.activity.status) {
        case 'created':
            return 'text-blue-700';
        case 'done':
            return 'text-emerald-700';
        case 'sent':
            return 'text-sky-700';
        case 'paid':
            return 'text-green-700';
        default:
            return 'text-gray-700';
    }
};

// Obtenir l'icône pour le statut
const getStatusIcon = (): string => {
    switch (props.activity.status) {
        case 'created':
            return 'plus-circle';
        case 'done':
            return 'check-circle';
        case 'sent':
            return 'send';
        case 'paid':
            return 'check-circle-2';
        default:
            return 'circle';
    }
};

// Obtenir les informations parent à afficher
const getParentInfo = (): string | null => {
    const { type, parent_project, parent_client } = props.activity;

    if (type === 'client') {
        // Pour un client, pas d'info parent
        return null;
    } else if (type === 'project') {
        // Pour un projet, afficher le client
        return parent_client ? `Client: ${parent_client.name}` : null;
    } else {
        // Pour un événement, afficher projet et client
        if (parent_project && parent_client) {
            return `${parent_project.name} - ${parent_client.name}`;
        }
        return null;
    }
};
</script>

<style scoped>
.hover-lift {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.hover-lift:hover {
    transform: translateY(-1px);
}
</style>
