<script setup lang="ts">
import { SidebarGroup, SidebarGroupContent, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';

interface Props {
    items: NavItem[];
    class?: string;
}

defineProps<Props>();
</script>

<template>
    <SidebarGroup :class="`px-2 py-2 ${$props.class || ''}`">
        <SidebarGroupLabel class="mb-2 px-2 text-xs font-semibold tracking-wider text-sidebar-foreground/50 uppercase">
            Ressources
        </SidebarGroupLabel>
        <SidebarGroupContent>
            <SidebarMenu class="space-y-1">
                <SidebarMenuItem v-for="item in items" :key="item.title">
                    <SidebarMenuButton
                        as-child
                        class="w-full justify-start gap-3 rounded-md px-3 py-2 text-sm font-medium text-sidebar-foreground/70 transition-colors hover:bg-sidebar-accent/30 hover:text-sidebar-foreground"
                    >
                        <a :href="item.href" target="_blank" rel="noopener noreferrer" class="flex w-full items-center gap-3">
                            <component :is="item.icon" class="h-4 w-4 shrink-0" />
                            <span class="truncate">{{ item.title }}</span>
                        </a>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarGroupContent>
    </SidebarGroup>
</template>
