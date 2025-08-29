<template>
    <span
        class="inline-flex items-center gap-1 rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset"
        :class="badgeClasses"
    >
        <Icon v-if="icon" :name="icon" class="h-3 w-3" />
        <slot>{{ label }}</slot>
    </span>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import Icon from '@/components/Icon.vue';
import type { ProjectStatus, EventType, EventStatus, PaymentStatus } from '@/types/models';

interface Props {
    type: 'project' | 'event' | 'payment' | 'custom';
    status?: ProjectStatus | EventStatus | PaymentStatus | string;
    eventType?: EventType;
    label?: string;
    icon?: string;
    variant?: 'default' | 'solid' | 'outline';
}

const props = withDefaults(defineProps<Props>(), {
    variant: 'default'
});

const badgeClasses = computed(() => {
    const baseClasses = 'transition-colors duration-200';
    let colorClasses = '';
    
    if (props.type === 'project') {
        colorClasses = getProjectStatusClasses(props.status as ProjectStatus);
    } else if (props.type === 'event') {
        if (props.eventType === 'billing') {
            colorClasses = getBillingEventStatusClasses(props.status as EventStatus);
        } else {
            colorClasses = getStepEventStatusClasses(props.status as EventStatus);
        }
    } else if (props.type === 'payment') {
        colorClasses = getPaymentStatusClasses(props.status as PaymentStatus);
    } else {
        // Type custom avec couleurs par défaut
        colorClasses = 'bg-gray-50 text-gray-700 ring-gray-600/20';
    }
    
    return `${baseClasses} ${colorClasses}`;
});

const getProjectStatusClasses = (status: ProjectStatus): string => {
    const classes = {
        active: 'bg-green-50 text-green-700 ring-green-600/20',
        completed: 'bg-blue-50 text-blue-700 ring-blue-600/20',
        on_hold: 'bg-yellow-50 text-yellow-700 ring-yellow-600/20',
        cancelled: 'bg-red-50 text-red-700 ring-red-600/20'
    };
    return classes[status] || 'bg-gray-50 text-gray-700 ring-gray-600/20';
};

const getStepEventStatusClasses = (status: EventStatus): string => {
    const classes = {
        todo: 'bg-yellow-50 text-yellow-700 ring-yellow-600/20',
        done: 'bg-green-50 text-green-700 ring-green-600/20',
        cancelled: 'bg-red-50 text-red-700 ring-red-600/20'
    };
    return classes[status] || 'bg-gray-50 text-gray-700 ring-gray-600/20';
};

const getBillingEventStatusClasses = (status: EventStatus): string => {
    const classes = {
        to_send: 'bg-orange-50 text-orange-700 ring-orange-600/20',
        sent: 'bg-blue-50 text-blue-700 ring-blue-600/20',
        cancelled: 'bg-red-50 text-red-700 ring-red-600/20'
    };
    return classes[status] || 'bg-gray-50 text-gray-700 ring-gray-600/20';
};

const getPaymentStatusClasses = (status: PaymentStatus): string => {
    const classes = {
        pending: 'bg-orange-50 text-orange-700 ring-orange-600/20',
        paid: 'bg-green-50 text-green-700 ring-green-600/20'
    };
    return classes[status] || 'bg-gray-50 text-gray-700 ring-gray-600/20';
};
</script>