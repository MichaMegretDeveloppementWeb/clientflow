<script setup lang="ts">
import { Link, usePage, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import { Calendar, FolderKanban, LayoutGrid, Users, Menu, X, User, LogOut, Settings } from 'lucide-vue-next'
import { route } from 'ziggy-js'
import stoneLogo from '../../images/stone_logo.webp'
import { Button } from '@/components/ui/button'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuSeparator, DropdownMenuTrigger } from '@/components/ui/dropdown-menu'
import { getInitials } from '@/composables/useInitials'
import type { NavItem } from '@/types'

const page = usePage()
const auth = computed(() => page.props.auth)

// Vérifications de sécurité pour les données utilisateur
const user = computed(() => auth.value?.user || null)
const userName = computed(() => user.value?.name || 'Utilisateur')
const userEmail = computed(() => user.value?.email || '')
const userInitials = computed(() => getInitials(userName.value))

// État du menu mobile
const mobileMenuOpen = ref(false)

// Navigation items
const navItems: NavItem[] = [
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
]

// Fonction pour vérifier si une route est active
const isActiveRoute = (href: string) => {
    return page.url.startsWith(href)
}

// Fermer le menu mobile quand on clique sur un lien
const closeMobileMenu = () => {
    mobileMenuOpen.value = false
}

// Fonction de déconnexion
const logout = () => {
    if (confirm('Êtes-vous sûr de vouloir vous déconnecter ?')) {
        router.post(route('logout'))
    }
}
</script>

<template>
    <nav class="sticky top-0 z-50 w-full shadow-sm bg-white shadow-sm">
        <div class="mx-auto w-full max-w-full min-[1000px]:max-w-[min(95%,100em)] px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <!-- Logo et nom de l'app -->
                <div class="flex items-center">
                    <a :href="route('home')" class="flex items-center gap-3">
                        <img
                            :src="stoneLogo"
                            alt="Stone Logo"
                            class="h-8 w-8 object-contain"
                        />
                        <span class="text-xl font-bold text-gray-900">Stone</span>
                    </a>
                </div>

                <!-- Navigation desktop -->
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <Link
                            v-for="item in navItems"
                            :key="item.title"
                            :href="item.href"
                            :class="[
                                'flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium transition-colors',
                                isActiveRoute(item.href)
                                    ? 'bg-gray-900 text-white'
                                    : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900'
                            ]"
                        >
                            <component v-if="item.icon" :is="item.icon" class="h-4 w-4" />
                            {{ item.title }}
                        </Link>
                    </div>
                </div>

                <!-- Menu utilisateur et burger mobile -->
                <div class="flex items-center gap-2">
                    <!-- Menu utilisateur -->
                    <DropdownMenu v-if="user">
                        <DropdownMenuTrigger as-child>
                            <Button variant="ghost" class="relative h-10 w-10 rounded-full">
                                <Avatar class="h-9 w-9">
                                    <AvatarImage v-if="user.avatar" :src="user.avatar" :alt="userName" />
                                    <AvatarFallback class="bg-blue-600 text-white font-medium">
                                        {{ userInitials }}
                                    </AvatarFallback>
                                </Avatar>
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end" class="w-56">
                            <div class="flex items-center justify-start gap-2 p-2">
                                <div class="flex flex-col space-y-1 leading-none">
                                    <p class="font-medium">{{ userName }}</p>
                                    <p class="w-[200px] truncate text-sm text-muted-foreground">
                                        {{ userEmail }}
                                    </p>
                                </div>
                            </div>
                            <DropdownMenuSeparator />
                            <DropdownMenuItem as-child>
                                <Link :href="route('profile.edit')" class="flex items-center gap-2">
                                    <User class="h-4 w-4" />
                                    Profil
                                </Link>
                            </DropdownMenuItem>
                            <DropdownMenuSeparator />
                            <DropdownMenuItem @click="logout" class="flex items-center gap-2 text-red-600">
                                <LogOut class="h-4 w-4" />
                                Déconnexion
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>

                    <!-- Bouton burger mobile -->
                    <Button
                        variant="ghost"
                        size="icon"
                        class="md:hidden"
                        @click="mobileMenuOpen = !mobileMenuOpen"
                    >
                        <Menu v-if="!mobileMenuOpen" class="h-6 w-6" />
                        <X v-else class="h-6 w-6" />
                    </Button>
                </div>
            </div>
        </div>

        <!-- Menu mobile -->
        <div v-if="mobileMenuOpen" class="md:hidden">
            <div class="space-y-1 border-t bg-white px-2 pb-3 pt-2 sm:px-3">
                <Link
                    v-for="item in navItems"
                    :key="item.title"
                    :href="item.href"
                    :class="[
                        'flex items-center gap-3 rounded-md px-3 py-2 text-base font-medium transition-colors',
                        isActiveRoute(item.href)
                            ? 'bg-gray-900 text-white'
                            : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900'
                    ]"
                    @click="closeMobileMenu"
                >
                    <component v-if="item.icon" :is="item.icon" class="h-5 w-5" />
                    {{ item.title }}
                </Link>
            </div>
        </div>
    </nav>
</template>
