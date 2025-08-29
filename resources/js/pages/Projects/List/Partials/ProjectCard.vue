<template>
    <div
        class="group cursor-pointer transition-all duration-200 hover:bg-slate-50/40 hover:shadow-sm relative min-h-[140px]"
        @click="handleProjectClick"
    >
        <!-- Trait fin gauche élégant -->
        <div class="absolute left-0 top-0 h-full w-px bg-transparent group-hover:bg-slate-300/60 transition-all duration-200"></div>

        <div class="flex flex-col gap-4 p-6 py-8 lg:flex-row lg:items-center lg:justify-between relative">
            <!-- Contenu principal -->
            <div class="flex-1 space-y-2.5">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div class="space-y-1.5">
                        <!-- Nom du projet -->
                        <div class="flex items-center gap-4">
                            <h3 class="text-base font-medium transition-colors group-hover:text-emerald-700 text-slate-900">
                                {{ project.name }}
                            </h3>
                            <!-- Badge de statut -->
                            <span
                                class="inline-flex w-fit items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset"
                                :class="getStatusClasses(project.status)"
                            >
                                {{ getStatusLabel(project.status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Informations secondaires -->
                <div class="flex flex-col gap-1.5 text-sm text-slate-600 sm:flex-row sm:items-center sm:gap-5">
                    <span v-if="project.client?.name" class="flex items-center gap-1.5">
                        <Icon name="user" class="h-3.5 w-3.5 text-slate-400" />
                        {{ project.client.name }}
                    </span>
                    <span v-if="project.description" class="flex items-center gap-1.5">
                        <Icon name="file-text" class="h-3.5 w-3.5 text-slate-400" />
                        {{ truncateText(project.description, 50) }}
                    </span>
                </div>

                <!-- Métriques business -->
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Date de début -->
                    <span
                        v-if="project.start_date"
                        class="inline-flex items-center gap-1.5 text-xs font-medium text-blue-700"
                    >
                        <Icon name="calendar" class="h-3 w-3" />
                        {{ formatDate(project.start_date) }}
                    </span>

                    <!-- Budget -->
                    <span
                        v-if="project.budget"
                        class="inline-flex items-center gap-1.5 text-xs font-medium text-emerald-700"
                    >
                        <Icon name="banknote" class="h-3 w-3" />
                        {{ formatCurrencyCompact(project.budget) }}
                    </span>

                    <!-- Montant facturé -->
                    <span
                        v-if="project.total_billed > 0"
                        class="inline-flex items-center gap-1.5 text-xs font-medium text-purple-700"
                    >
                        <Icon name="receipt" class="h-3 w-3" />
                        {{ formatCurrencyCompact(project.total_billed) }} facturé
                    </span>

                    <!-- Nombre d'événements -->
                    <span
                        v-if="project.events_count > 0"
                        class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-700"
                    >
                        <Icon name="activity" class="h-3 w-3" />
                        {{ project.events_count }} événement{{ project.events_count > 1 ? 's' : '' }}
                    </span>
                </div>

                <!-- Indicateurs d'alerte -->
                <div v-if="hasAnyAlerts(project)" class="flex flex-wrap items-center gap-2 mt-2">
                    <!-- Projet en retard -->
                    <span
                        v-if="isProjectOverdue(project)"
                        class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-medium text-red-700 bg-red-50 rounded-md ring-1 ring-red-200"
                    >
                        <Icon name="clock" class="h-3 w-3" />
                        Projet en retard
                    </span>

                    <!-- Événements en retard -->
                    <span
                        v-if="project.has_overdue_events"
                        class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-medium text-orange-700 bg-orange-50 rounded-md ring-1 ring-orange-200"
                    >
                        <Icon name="alert-triangle" class="h-3 w-3" />
                        Tâches en retard
                    </span>

                    <!-- Retards de paiement -->
                    <span
                        v-if="project.has_payment_overdue"
                        class="inline-flex items-center gap-1.5 px-2 py-1 text-xs font-medium text-red-700 bg-red-50 rounded-md ring-1 ring-red-200"
                    >
                        <Icon name="credit-card" class="h-3 w-3" />
                        Paiements en retard
                    </span>
                </div>
            </div>

            <!-- Menu contextuel discret -->
            <div class="transition-opacity absolute right-3 top-3">
                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <button
                            class="inline-flex items-center justify-center p-1 rounded-md text-slate-600 bg-slate-200 hover:bg-slate-300 transition-colors cursor-pointer"
                            @click.stop
                            title="Actions"
                        >
                            <Icon name="more-horizontal" class="h-3.5 w-3.5" />
                        </button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end" class="w-40">
                        <DropdownMenuItem @click.stop="handleView">
                            <Icon name="external-link" class="mr-2 h-3.5 w-3.5" />
                            Voir le projet
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
    </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import Icon from '@/components/Icon.vue'
import { route } from 'ziggy-js'
import type { ProjectDTO } from '@/types/models'

/**
 * Composant de carte projet réutilisable
 * Responsabilités :
 * - Affichage des informations du projet
 * - Interactions de navigation et actions
 * - Menu contextuel avec actions
 */

interface Props {
    project: ProjectDTO
}

const props = defineProps<Props>()

const emit = defineEmits<{
    'project-click': [project: ProjectDTO]
    'view': [project: ProjectDTO]
    'edit': [project: ProjectDTO]
    'delete': [projectId: number]
}>()

// Gestion du clic sur le projet
const handleProjectClick = () => {
    emit('project-click', props.project)
}

// Actions du menu
const handleView = () => {
    emit('view', props.project)
}

const handleEdit = () => {
    emit('edit', props.project)
}

const handleDelete = () => {
    emit('delete', props.project.id)
}

// Utilitaires de formatage
const truncateText = (text: string, maxLength: number): string => {
    return text.length > maxLength ? `${text.slice(0, maxLength)}...` : text
}

const formatDate = (date: string): string => {
    return new Intl.DateTimeFormat('fr-FR', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    }).format(new Date(date))
}

const formatCurrencyCompact = (amount: number): string => {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
        notation: amount >= 1000 ? 'compact' : 'standard'
    }).format(amount)
}

// Logique des statuts
const getStatusLabel = (status: string): string => {
    const labels: Record<string, string> = {
        'active': 'Actif',
        'completed': 'Terminé',
        'on_hold': 'En pause',
        'cancelled': 'Annulé'
    }
    return labels[status] || status
}

const getStatusClasses = (status: string): string => {
    const classes: Record<string, string> = {
        'active': 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        'completed': 'bg-blue-50 text-blue-700 ring-blue-600/20',
        'on_hold': 'bg-orange-50 text-orange-700 ring-orange-600/20',
        'cancelled': 'bg-red-50 text-red-700 ring-red-600/20'
    }
    return classes[status] || 'bg-gray-50 text-gray-700 ring-gray-600/20'
}

const isProjectOverdue = (project: ProjectDTO): boolean => {
    if (!project.end_date) return false
    const endDate = new Date(project.end_date)
    const now = new Date()
    return endDate < now && project.status === 'active'
}

const hasAnyAlerts = (project: ProjectDTO): boolean => {
    return isProjectOverdue(project) || 
           project.has_overdue_events || 
           project.has_payment_overdue
}
</script>