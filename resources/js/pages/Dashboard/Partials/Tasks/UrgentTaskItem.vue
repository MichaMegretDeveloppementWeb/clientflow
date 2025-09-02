<template>
    <Link
        :href="route('events.show', task.id)"
        class="group flex items-start gap-3 rounded-xl p-4 transition-all duration-200 task-container"
        :class="taskClasses"
    >
        <!-- Icône de statut -->
        <div
            class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-xl ring-1 ring-inset"
            :class="iconClasses"
        >
            <OptimizedIcon
                :name="task.event_type === 'billing' ? 'banknote' : 'flag'"
                :size="16"
                :class="iconColorClasses"
                preload
            />
        </div>

        <!-- Contenu principal -->
        <div class="min-w-0 flex-1">
            <!-- Titre et badge -->
            <div class="mb-2 flex flex-col gap-2">
                <h4 class="break-words font-medium leading-tight text-gray-900">
                    {{ task.name }}
                </h4>
            </div>

            <!-- Projet/client et montant -->
            <div class="mb-2 mt-2 flex flex-col-reverse gap-2 sm:flex-row sm:items-center sm:justify-between">
                <p class="break-words text-sm text-gray-600">
                    {{ task.project.name }} - <b>{{ task.project.client.name }}</b>
                </p>
                <div
                    v-if="isBillingTask(task) && task.amount"
                    class="inline-flex w-fit items-center gap-1 rounded-full bg-emerald-50 px-2 py-1 text-xs font-medium text-emerald-700"
                >
                    <OptimizedIcon name="euro" :size="12" />
                    {{ formatCurrency(task.amount) }}
                </div>
            </div>

            <!-- Badge de statut temporel -->
            <div class="flex">
                <div
                    class="inline-flex w-fit items-center gap-1 rounded-full text-xs font-medium"
                    :class="getTimeBadgeClasses(task)"
                >
                    <OptimizedIcon :name="getTimeBadgeIcon(task)" :size="12" />
                    {{ getTimeBadgeText(task) }}
                </div>
            </div>
        </div>

    </Link>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import OptimizedIcon from '@/components/OptimizedIcon.vue';
import type { Task } from '@/types/dashboard/tasks';
import { isStepTask, isBillingTask } from '@/types/dashboard/tasks';

// Props
interface Props {
    task: Task;
}

const props = defineProps<Props>();

// Classes calculées pour le style
const taskClasses = computed(() => {

    if (props.task.is_overdue) {
        return 'hover:border-red-300 hover:bg-red-100';
    }

    if (isToday(getReferenceDate(props.task))) {
        return 'hover:border-amber-300 hover:bg-amber-100';
    }

    return 'hover:border-gray-300 hover:bg-gray-50 hover:shadow-sm';
});

const iconClasses = computed(() => {
    if (props.task.is_overdue) {
        return 'bg-red-50 ring-red-200';
    }

    if (isToday(getReferenceDate(props.task))) {
        return 'bg-amber-50 ring-amber-200';
    }

    if (props.task.event_type === 'step') {
        return 'bg-blue-50 ring-blue-200';
    }

    return 'bg-emerald-50 ring-emerald-200';
});

const iconColorClasses = computed(() => {
    if (props.task.is_overdue) {
        return 'text-red-600';
    }

    if (isToday(getReferenceDate(props.task))) {
        return 'text-amber-600';
    }

    if (props.task.event_type === 'step') {
        return 'text-blue-600';
    }

    return 'text-emerald-600';
});

// Utilitaires
const formatCurrency = (amount: number): string => {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
    }).format(amount);
};

const isToday = (dateString?: string): boolean => {
    if (!dateString) return false;
    const date = new Date(dateString);
    const today = new Date();
    return date.toDateString() === today.toDateString();
};

// Helper functions for task properties
const getReferenceDate = (task: Task): string => {
    return isStepTask(task) ? task.execution_date : task.send_date;
};


// Obtenir le texte du badge temporel
const getTimeBadgeText = (task: Task): string => {
    const dateStr = getReferenceDate(task);
    if (!dateStr) return 'À planifier';

    const date = new Date(dateStr);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    date.setHours(0, 0, 0, 0);

    const diffTime = date.getTime() - today.getTime();
    const diffDays = Math.round(diffTime / (1000 * 60 * 60 * 24));

    // Si en retard - on affiche le retard
    if (diffDays < 0) {
        const daysOverdue = Math.abs(diffDays);
        return `${daysOverdue}j de retard`;
    }

    // Si pas en retard - on affiche la date au format d/m/Y
    const prefix = task.event_type === 'step' ? 'À faire' : 'À envoyer';

    // Pour aujourd'hui, demain et dans 2 jours, on garde le texte
    switch(diffDays) {
        case 0:
            return `${prefix} aujourd'hui`;
        case 1:
            return `${prefix} demain`;
        case 2:
            return `${prefix} dans 2 jours`;
        default:
            // Pour toutes les autres dates, format d/m/Y
            const day = date.getDate().toString().padStart(2, '0');
            const month = (date.getMonth() + 1).toString().padStart(2, '0');
            const year = date.getFullYear();
            return `${prefix} le ${day}/${month}/${year}`;
    }
};

// Nouveau : obtenir les classes CSS du badge
const getTimeBadgeClasses = (task: Task): string => {
    const dateStr = getReferenceDate(task);
    if (!dateStr) return 'bg-gray-50 text-gray-700 ring-gray-600/20';

    const date = new Date(dateStr);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    date.setHours(0, 0, 0, 0);

    const diffDays = Math.round((date.getTime() - today.getTime()) / (1000 * 60 * 60 * 24));

    // En retard
    if (diffDays < 0) {
        return 'text-red-700';
    }

    // Aujourd'hui
    if (diffDays === 0) {
        return 'text-amber-700';
    }

    // Demain ou dans 2 jours
    if (diffDays <= 2) {
        return 'text-orange-700';
    }

    // Dans 3-7 jours
    if (diffDays <= 7) {
        return 'text-blue-700';
    }

    // Plus tard
    return 'text-gray-700';
};

// Nouveau : obtenir l'icône du badge
const getTimeBadgeIcon = (task: Task): string => {
    const dateStr = getReferenceDate(task);
    if (!dateStr) return 'calendar-x';

    const date = new Date(dateStr);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    date.setHours(0, 0, 0, 0);

    const diffDays = Math.round((date.getTime() - today.getTime()) / (1000 * 60 * 60 * 24));

    if (diffDays < 0) {
        return 'alert-triangle'; // En retard
    } else if (diffDays === 0) {
        return 'clock'; // Aujourd'hui
    } else if (diffDays <= 2) {
        return 'clock-alert'; // Urgent mais pas en retard
    } else {
        return 'calendar-check'; // Planifié
    }
};
</script>

<style scoped>
.task-container {
    contain: layout style paint;
}

.hover-lift {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.hover-lift:hover {
    transform: translateY(-1px);
}
</style>
