<template>
    <!-- Disposition événements avec finesse visuelle premium -->
    <div
        class="group cursor-pointer transition-all duration-200 hover:bg-slate-50/40 hover:shadow-sm relative min-h-[140px]"
        @click="handleClick"
    >
        <!-- Trait fin gauche élégant -->
        <div class="absolute left-0 top-0 h-full w-px bg-transparent group-hover:bg-slate-300/60 transition-all duration-200"></div>

        <div class="flex flex-col gap-4 p-6 py-8 lg:flex-row lg:items-center lg:justify-between">
            <!-- Contenu principal -->
            <div class="flex-1 space-y-2.5">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div class="space-y-1.5">
                        <!-- Nom du client -->
                        <div class="flex items-center gap-4">
                            <h3 class="text-base font-medium transition-colors group-hover:text-emerald-700 text-slate-900">
                                {{ client.name }}
                            </h3>
                            <!-- Icône d'alerte discrète pour retards de paiement -->
                            <div v-if="client.has_overdue_payments" class="flex items-center gap-1">
                                <Icon
                                    name="alert-triangle"
                                    class="h-3.5 w-3.5 text-red-600"
                                    title="Paiements en retard"
                                />
                                <span class="text-red-600 text-xs">Retard paiement</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations secondaires (entreprise, email, téléphone) -->
                <div class="flex flex-col gap-1.5 text-sm text-slate-600 sm:flex-row sm:items-center sm:gap-5">
                    <span v-if="client.company" class="flex items-center gap-1.5">
                        <Icon name="building" class="h-3.5 w-3.5 text-slate-400" />
                        {{ client.company }}
                    </span>
                    <span v-if="client.email" class="flex items-center gap-1.5">
                        <Icon name="mail" class="h-3.5 w-3.5 text-slate-400" />
                        {{ client.email }}
                    </span>
                    <span v-if="client.phone" class="flex items-center gap-1.5">
                        <Icon name="phone" class="h-3.5 w-3.5 text-slate-400" />
                        {{ formatPhoneNumber(client.phone) }}
                    </span>
                </div>

                <!-- Métriques business -->
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Nombre de projets -->
                    <span
                        class="inline-flex items-center gap-1.5 text-xs font-medium text-blue-700"
                    >
                        <Icon name="briefcase" class="h-3 w-3" />
                        {{ client.projects_count }} {{ client.projects_count > 1 ? 'projets' : 'projet' }}
                    </span>

                    <!-- Projets actifs -->
                    <span
                        v-if="client.active_projects_count > 0"
                        class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-700"
                    >
                        <Icon name="activity" class="h-3 w-3" />
                        {{ client.active_projects_count }} actif{{ client.active_projects_count > 1 ? 's' : '' }}
                    </span>

                    <!-- Chiffre d'affaires -->
                    <span
                        v-if="client.total_revenue > 0"
                        class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-700"
                    >
                        <Icon name="banknote" class="h-3 w-3" />
                        {{ formatCurrencyCompact(client.total_revenue) }}
                    </span>

                    <!-- Montant en attente -->
                    <span
                        v-if="client.pending_amount > 0"
                        class="inline-flex items-center gap-1.5 text-xs font-medium text-amber-700"
                    >
                        <Icon name="clock" class="h-3 w-3" />
                        {{ formatCurrencyCompact(client.pending_amount) }} en attente
                    </span>
                </div>

            </div>
        </div>

        <!-- Menu contextuel discret -->
        <div class="absolute top-3 right-3 transition-opacity">
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <button
                        class="inline-flex items-center justify-center p-1 rounded-md text-slate-800 text-slate-600 bg-slate-200 transition-colors cursor-pointer"
                        @click.stop
                        title="Actions"
                    >
                        <Icon name="more-horizontal" class="h-3.5 w-3.5" />
                    </button>
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end" class="w-40">
                    <DropdownMenuItem @click.stop="handleView">
                        <Icon name="external-link" class="mr-2 h-3.5 w-3.5" />
                        Voir le profil
                    </DropdownMenuItem>
                    <DropdownMenuItem @click.stop="handleEdit">
                        <Icon name="edit" class="mr-2 h-3.5 w-3.5" />
                        Modifier
                    </DropdownMenuItem>
                    <DropdownMenuSeparator />
                    <DropdownMenuItem
                        class="text-red-600 focus:text-red-600 focus:bg-red-50"
                        @click.stop="handleDelete"
                    >
                        <Icon name="trash-2" class="mr-2 h-3.5 w-3.5" />
                        Supprimer
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </div>

    </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import Icon from '@/components/Icon.vue'
import type { ClientDTO } from '@/types/models'

interface Props {
    client: ClientDTO
}

const props = defineProps<Props>()

const emit = defineEmits<{
    click: []
    delete: []
}>()

// Logique de statut minimaliste - texte coloré uniquement
const getStatusTextClass = () => {
    if (props.client.has_overdue_payments) {
        return 'text-red-600'
    }
    if (props.client.active_projects_count > 0) {
        return 'text-emerald-600'
    }
    return 'text-slate-500'
}

const getStatusIcon = () => {
    if (props.client.has_overdue_payments) {
        return 'alert-triangle'
    }
    if (props.client.active_projects_count > 0) {
        return 'check-circle'
    }
    return 'circle'
}

const getStatusLabel = () => {
    if (props.client.has_overdue_payments) {
        return 'Urgent'
    }
    if (props.client.active_projects_count > 0) {
        return 'Actif'
    }
    return 'Inactif'
}

// Formatage des données
const formatCurrencyCompact = (amount: number): string => {
    if (amount >= 1000000) {
        return `${Math.round(amount / 100000) / 10}M€`
    }
    if (amount >= 1000) {
        return `${Math.round(amount / 100) / 10}k€`
    }
    return `${amount}€`
}

const formatPhoneNumber = (phone: string): string => {
    if (!phone || phone.length < 10) return phone
    return phone.replace(/(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/, '$1 $2 $3 $4 $5')
}

const formatRelativeDate = (dateString: string): string => {
    const date = new Date(dateString)
    const now = new Date()
    const diffInDays = Math.floor((now.getTime() - date.getTime()) / (1000 * 60 * 60 * 24))

    if (diffInDays === 0) return 'aujourd\'hui'
    if (diffInDays === 1) return 'hier'
    if (diffInDays < 30) return `il y a ${diffInDays}j`
    if (diffInDays < 365) return `il y a ${Math.floor(diffInDays / 30)}m`
    return `il y a ${Math.floor(diffInDays / 365)}a`
}

// Gestionnaires d'événements
const handleClick = () => {
    emit('click')
}

const handleView = () => {
    router.get(route('clients.show', { client: props.client.id }))
}

const handleEdit = () => {
    router.get(route('clients.edit', { client: props.client.id }))
}

const handleDelete = () => {
    emit('delete')
}
</script>
