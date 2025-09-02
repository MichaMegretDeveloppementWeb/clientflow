<template>
    <Link
        :href="activity.link"
        :class="[
            'group flex items-start gap-3 p-0 py-6',
            props.customClass || 'rounded-lg bg-gray-200 hover:bg-gray-300 hover:shadow-sm'
        ]"
    >
        <!-- Icône de type -->
        <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-xl ring-1 ring-inset" :class="getIconBgClasses()">
            <OptimizedIcon
                :name="activity.icon"
                :size="16"
                :class="activity.icon_color"
                preload
            />
        </div>

        <!-- Contenu principal -->
        <div class="min-w-0 flex-1">
            <!-- Titre et badge -->
            <div class="mb-2 flex flex-col md:flex-row md:items-center gap-3">
                <h4 class="break-words font-medium leading-tight text-gray-900">
                    {{ activity.entity_type }} - {{ activity.name }}
                    <span v-if="activity.company" class="text-sm text-gray-500">({{ activity.company }})</span>
                </h4>

                <!-- Badge de statut -->
                <div class="inline-flex w-fit items-center gap-1 rounded-sm px-1 py-0 text-md font-medium" :class="getStatusBadgeClasses()">
                    <OptimizedIcon :name="getStatusIcon()" :size="15" />
                    {{ activity.status_label }}
                </div>
            </div>

            <!-- Informations parent (projet/client) et montant -->
            <div class="mb-2 mt-4 md:mt-2 flex flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-between">
                <!-- Informations parent -->
                <div v-if="getParentInfo()" class="break-words text-sm text-gray-600 hidden md:block">
                    {{ getParentInfo() }}
                </div>

                <!-- Montant si facturation -->
                <div
                    v-if="activity.amount"
                    class="inline-flex w-fit items-center gap-1 rounded-full bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700"
                >
                    <OptimizedIcon name="euro" :size="12" />
                    {{ activity.formatted_amount }}
                </div>
            </div>

            <!-- Date -->
            <div class="flex items-center gap-2">
                <OptimizedIcon name="clock" :size="12" class="text-gray-400 flex-shrink-0" />
                <span class="break-words text-sm text-gray-500">
                    {{ activity.time_ago }}
                </span>
            </div>
        </div>
    </Link>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import OptimizedIcon from '@/components/OptimizedIcon.vue';

// Interface pour les activités
interface Activity {
    id: string;
    type: 'client' | 'project' | 'step' | 'billing';
    entity_type: string;
    name: string;
    company?: string;
    status: 'created' | 'done' | 'sent' | 'paid';
    status_label: string;
    timestamp: string;
    time_ago: string;
    link: string;
    parent_project?: {
        id: number;
        name: string;
    } | null;
    parent_client?: {
        id: number;
        name: string;
    } | null;
    amount?: number | null;
    formatted_amount?: string | null;
    icon: string;
    icon_color: string;
}

// Props
interface Props {
    activity: Activity;
    customClass?: string;
}

const props = defineProps<Props>();

// Obtenir les classes de fond pour l'icône
const getIconBgClasses = (): string => {
    switch (props.activity.type) {
        case 'client':
            return 'bg-blue-50 ring-blue-200';
        case 'project':
            return 'bg-purple-50 ring-purple-200';
        case 'step':
            return 'bg-green-50 ring-green-200';
        case 'billing':
            return 'bg-emerald-50 ring-emerald-200';
        default:
            return 'bg-gray-50 ring-gray-200';
    }
};

// Obtenir les classes du badge de statut
const getStatusBadgeClasses = (): string => {
    switch (props.activity.status) {
        case 'created':
            return 'text-blue-700 ring-1 ring-blue-600/20';
        case 'done':
            return 'text-green-700 ring-1 ring-green-600/20';
        case 'sent':
            return 'text-emerald-700 ring-1 ring-emerald-600/20';
        case 'paid':
            return 'text-green-700 ring-1 ring-green-600/20';
        default:
            return 'text-gray-700 ring-1 ring-gray-600/20';
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
