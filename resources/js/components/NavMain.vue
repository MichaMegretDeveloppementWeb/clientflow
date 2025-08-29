<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';

defineProps<{
    items: NavItem[];
}>();

const page = usePage();

const isActiveRoute = (href: string) => {
    const currentPath = page.url;
    if (href === '/dashboard') {
        return currentPath === '/dashboard' || currentPath === '/';
    }
    return currentPath.startsWith(href);
};
</script>

<template>
    <SidebarGroup class="px-2 py-2">
        <SidebarGroupLabel class="mb-2 px-2 text-xs font-semibold tracking-wider text-sidebar-foreground/70 uppercase">
            Navigation
        </SidebarGroupLabel>
        <SidebarMenu class="space-y-1">
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <SidebarMenuButton
                    as-child
                    :is-active="isActiveRoute(item.href)"
                    :tooltip="item.title"
                    class="w-full justify-start gap-3 rounded-md px-3 py-2.5 text-sm font-medium transition-colors hover:bg-sidebar-accent/50 data-[active=true]:bg-sidebar-accent data-[active=true]:font-semibold data-[active=true]:text-sidebar-accent-foreground"
                >
                    <Link :href="item.href" class="flex w-full items-center gap-3">
                        <component :is="item.icon" class="h-4 w-4 shrink-0" />
                        <span class="truncate">{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
