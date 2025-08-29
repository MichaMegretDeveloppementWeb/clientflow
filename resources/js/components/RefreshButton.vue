<template>
    <Button
        variant="outline"
        size="sm"
        :disabled="isRefreshing"
        @click="handleRefresh"
        class="transition-all duration-200"
        :class="{ 'animate-pulse': isRefreshing }"
    >
        <Icon 
            :name="isRefreshing ? 'loader-2' : 'refresh-cw'"
            class="h-4 w-4"
            :class="{ 'animate-spin': isRefreshing }"
        />
        <span class="ml-2">{{ isRefreshing ? 'Actualisation...' : 'Actualiser' }}</span>
    </Button>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import Icon from '@/components/Icon.vue';

interface Props {
    only?: string[];
    preserveState?: boolean;
    preserveScroll?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    only: () => [],
    preserveState: true,
    preserveScroll: true
});

const isRefreshing = ref(false);

const handleRefresh = () => {
    if (isRefreshing.value) return;
    
    isRefreshing.value = true;
    
    router.reload({
        only: props.only.length > 0 ? props.only : undefined,
        preserveState: props.preserveState,
        preserveScroll: props.preserveScroll,
        onFinish: () => {
            isRefreshing.value = false;
        }
    });
};
</script>