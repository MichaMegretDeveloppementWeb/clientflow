<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import type { BreadcrumbItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppShell variant="sidebar">
        <AppSidebar />
        <AppContent variant="sidebar" class="overflow-x-hidden bg-gray-50/30">
            <AppSidebarHeader :breadcrumbs="breadcrumbs" />
            <div class="mx-auto w-full max-w-[100em] flex-1 p-4 sm:p-6 lg:p-8">
                <slot />
            </div>
        </AppContent>
    </AppShell>
</template>
