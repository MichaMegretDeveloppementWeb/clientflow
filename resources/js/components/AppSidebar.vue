<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Calendar, Folder, FolderKanban, LayoutGrid, Users } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
        icon: LayoutGrid,
    },
    {
        title: 'Clients',
        href: '/clients',
        icon: Users,
    },
    {
        title: 'Projets',
        href: '/projects',
        icon: FolderKanban,
    },
    {
        title: 'Événements',
        href: '/events',
        icon: Calendar,
    },
];

</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader class="border-b border-sidebar-border/50">
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child class="transition-colors hover:bg-sidebar-accent/30">
                        <Link :href="route('dashboard')" class="flex items-center gap-3">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent class="py-2">
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter class="border-t border-sidebar-border/50 pt-4">
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
