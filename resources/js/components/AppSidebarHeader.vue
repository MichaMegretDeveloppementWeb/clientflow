<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItemType } from '@/types';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItemType[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center gap-3 border-b border-sidebar-border/60 bg-background/60 px-6 backdrop-blur-sm transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
    >
        <div class="flex items-center gap-3">
            <SidebarTrigger class="-ml-1 transition-colors hover:bg-sidebar-accent/50" />
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <div class="h-5 w-px bg-border/60" />
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>
    </header>
</template>
