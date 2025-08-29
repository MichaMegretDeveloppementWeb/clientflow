<template>
    <div class="border-b border-gray-100 pb-8">
        <!-- Breadcrumb style header -->
        <div class="mb-6 flex items-center gap-3 text-sm">
            <Link :href="route('dashboard')" class="flex items-center gap-2 text-gray-500 hover:text-gray-700 transition-colors">
                <Icon name="home" class="h-4 w-4" />
                <span>Tableau de bord</span>
            </Link>
            <div class="h-4 w-px bg-gray-300"></div>
            <Link :href="route('clients.index')" class="text-gray-500 transition-colors hover:text-gray-700">Clients</Link>
        </div>

        <!-- Main header content -->
        <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="mb-2 flex items-center gap-3">
                    <div class="rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 p-2 shadow-sm">
                        <Icon name="user" class="h-6 w-6 text-white" />
                    </div>
                    <div>
                        <div v-if="isLoading" class="animate-pulse">
                            <div class="h-8 w-48 bg-gray-200 rounded mb-2"></div>
                            <div class="h-4 w-32 bg-gray-200 rounded"></div>
                        </div>
                        <div v-else>
                            <h1 class="text-2xl font-bold tracking-tight text-gray-900">{{ client?.name }}</h1>
                            <p v-if="client?.company" class="text-sm text-gray-600">{{ client.company }}</p>
                        </div>
                    </div>
                </div>
                <p class="max-w-2xl leading-relaxed text-gray-600">Détails du client et gestion de ses projets</p>
            </div>

            <div v-if="!isLoading && client" class="flex flex-col gap-2 sm:flex-row">
                <Button variant="outline" as-child class="w-full border-gray-300 hover:bg-gray-50 sm:w-auto">
                    <Link :href="route('clients.edit', client.id)">
                        <Icon name="edit" class="mr-2 h-4 w-4" />
                        Modifier le client
                    </Link>
                </Button>
                <Button as-child class="w-full bg-primary text-primary-foreground hover:bg-primary/90 sm:w-auto">
                    <Link :href="route('projects.create', { client_id: client.id })">
                        <Icon name="plus" class="mr-2 h-4 w-4" />
                        Nouveau projet
                    </Link>
                </Button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import Icon from '@/components/Icon.vue'
import { Button } from '@/components/ui/button'
import { Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import type { ClientDetailHeaderProps } from '@/types/clients/detail/index'

defineProps<ClientDetailHeaderProps>()
</script>