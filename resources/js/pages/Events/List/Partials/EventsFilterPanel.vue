<template>
    <div class="space-y-4">
        <!-- Toggle button -->
        <div class="flex items-center justify-between">
            <Button
                @click="toggleVisibility"
                variant="outline"
                class="border-gray-300 transition-colors hover:bg-gray-50"
            >
                <Icon name="filter" class="mr-2 h-4 w-4" />
                Filtres
                <Badge
                    v-if="hasActiveFilters"
                    variant="secondary"
                    class="ml-2"
                >
                    {{ displayableActiveFilters.length }}
                </Badge>
                <Icon
                    name="chevron-down"
                    class="ml-2 h-4 w-4 transition-transform"
                    :class="{ 'rotate-180': localVisible }"
                />
            </Button>

            <div v-if="hasActiveFilters" class="flex items-center gap-2 text-sm">
                <span class="text-gray-500">Filtres actifs :</span>
                <Button
                    variant="ghost"
                    size="sm"
                    class="h-7 px-2 text-xs text-red-600 hover:bg-red-50 hover:text-red-700"
                    @click="$emit('clear-all')"
                >
                    <Icon name="x" class="mr-1 h-3 w-3" />
                    Effacer tout
                </Button>
            </div>
        </div>

        <!-- Collapsible filters section -->
        <Transition
            enter-active-class="transition-all duration-200 ease-out"
            enter-from-class="transform opacity-0 -translate-y-2"
            enter-to-class="transform opacity-100 translate-y-0"
            leave-active-class="transition-all duration-150 ease-in"
            leave-from-class="transform opacity-100 translate-y-0"
            leave-to-class="transform opacity-0 -translate-y-2"
        >
            <Card v-show="localVisible" class="border border-gray-200 bg-white shadow-sm">
                <CardContent class="p-6">
                    <div class="space-y-6">
                        <!-- Search section -->
                        <div>
                            <div class="grid gap-4 sm:grid-cols-1">
                                <div class="space-y-2">
                                    <Label class="text-sm font-medium text-gray-700">
                                        Recherche
                                    </Label>
                                    <div class="relative">
                                        <Icon
                                            name="search"
                                            class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 transform text-gray-400"
                                        />
                                        <Input
                                            ref="searchInput"
                                            v-model="localFilters.search"
                                            type="text"
                                            placeholder="Nom, description, projet..."
                                            class="h-10 border-gray-300 pl-10 focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20"
                                            @input="handleSearchInput"
                                            @keydown.enter="applyFilters"
                                        />
                                        <Button
                                            v-if="localFilters.search"
                                            variant="ghost"
                                            size="sm"
                                            class="absolute right-1 top-1/2 h-7 -translate-y-1/2 transform px-2"
                                            @click="clearSearch"
                                        >
                                            <Icon name="x" class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <Separator />

                        <!-- Filters section -->
                        <div>
                            <h4 class="mb-4 flex items-center gap-2 text-sm font-medium text-gray-800">
                                <Icon name="filter" class="h-4 w-4 text-gray-500" />
                                Filtres
                            </h4>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <!-- Type d'événement -->
                                <div class="space-y-2">
                                    <Label class="text-sm font-medium text-gray-700">
                                        Type d'événement
                                    </Label>
                                    <Select
                                        v-model="localFilters.event_type"
                                        @update:model-value="handleFilterChange"
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Tous les types" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="all">Tous les types</SelectItem>
                                            <SelectItem value="step">Étapes</SelectItem>
                                            <SelectItem value="billing">Facturations</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>

                                <!-- Projet -->
                                <div class="space-y-2">
                                    <Label class="text-sm font-medium text-gray-700">
                                        Projet
                                    </Label>
                                    <Select
                                        v-model="localFilters.project_id"
                                        @update:model-value="handleFilterChange"
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Tous les projets" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="all">Tous les projets</SelectItem>
                                            <SelectItem
                                                v-for="project in filteredProjects"
                                                :key="project.id"
                                                :value="project.id.toString()"
                                            >
                                                {{ project.name }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>

                                <!-- Client -->
                                <div class="space-y-2">
                                    <Label class="text-sm font-medium text-gray-700">
                                        Client
                                    </Label>
                                    <Select
                                        v-model="localFilters.client_id"
                                        @update:model-value="handleFilterChange"
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Tous les clients" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="all">Tous les clients</SelectItem>
                                            <SelectItem
                                                v-for="client in filteredClients"
                                                :key="client.id"
                                                :value="client.id.toString()"
                                            >
                                                {{ client.name }}
                                            </SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>

                                <!-- Statut de paiement -->
                                <div v-if="showPaymentStatusFilter" class="space-y-2">
                                    <Label class="text-sm font-medium text-gray-700">
                                        Statut de paiement
                                    </Label>
                                    <Select
                                        v-model="localFilters.payment_status"
                                        @update:model-value="handleFilterChange"
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Tous les statuts" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="all">Tous les statuts</SelectItem>
                                            <SelectItem value="pending">En attente</SelectItem>
                                            <SelectItem value="paid">Payé</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                            </div>
                        </div>

                        <Separator />

                        <!-- Sort section -->
                        <div>
                            <h4 class="mb-4 flex items-center gap-2 text-sm font-medium text-gray-800">
                                <Icon name="arrow-up-down" class="h-4 w-4 text-gray-500" />
                                Tri
                            </h4>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div class="space-y-2">
                                    <Label class="text-sm font-medium text-gray-700">
                                        Trier par
                                    </Label>
                                    <Select
                                        v-model="localFilters.sort"
                                        @update:model-value="handleSortChange"
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Sélectionner" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="name">Nom</SelectItem>
                                            <SelectItem value="created_date">Date de création</SelectItem>
                                            <SelectItem value="due_date">Date de réalisation prévue</SelectItem>
                                            <SelectItem v-if="showPaymentStatusFilter" value="amount">Montant</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>

                                <div class="space-y-2">
                                    <Label class="text-sm font-medium text-gray-700">
                                        Ordre
                                    </Label>
                                    <Select
                                        v-model="localFilters.direction"
                                        @update:model-value="handleSortChange"
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Sélectionner" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="asc">Croissant</SelectItem>
                                            <SelectItem value="desc">Décroissant</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>
                            </div>
                        </div>

                        <!-- Filtres de retard -->
                        <div v-if="showPaymentOverdueFilter" class="space-y-4">
                            <h4 class="flex items-center gap-2 text-sm font-medium text-gray-800">
                                <Icon name="alert-circle" class="h-4 w-4 text-gray-500" />
                                Alertes
                            </h4>

                            <!-- Filtre paiements en retard (seulement pour les facturations) -->
                            <div class="space-y-2">
                                <div class="flex items-center space-x-2">
                                    <input
                                        id="payment-overdue"
                                        v-model="localFilters.payment_overdue"
                                        type="checkbox"
                                        class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500 focus:ring-offset-0"
                                        @change="applyFilters"
                                    />
                                    <Label
                                        for="payment-overdue"
                                        class="text-sm font-medium text-gray-700 cursor-pointer"
                                    >
                                        <div class="flex items-center gap-1.5">
                                            Paiements en retard
                                            <Icon name="credit-card" class="h-3.5 w-3.5 text-red-500" />
                                        </div>
                                    </Label>
                                </div>
                            </div>
                        </div>

                        <!-- Active filters display -->
                        <div v-if="hasActiveFilters" class="pt-4">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-sm font-medium text-gray-700">
                                    Filtres actifs :
                                </span>

                                <ActiveFilterTag
                                    v-for="filter in displayableActiveFilters"
                                    :key="filter.key"
                                    :label="filter.label"
                                    @remove="$emit('clear-filter', filter.key)"
                                />
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </Transition>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, watch, nextTick } from 'vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Badge } from '@/components/ui/badge'
import { Separator } from '@/components/ui/separator'
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select'
import Icon from '@/components/Icon.vue'
import ActiveFilterTag from '@/pages/Clients/List/Partials/ActiveFilterTag.vue'
import type { EventFilters, ActiveFilter } from '@/types/events/list'

interface Props {
    visible?: boolean
    filters: EventFilters
    activeFilters: ActiveFilter[]
    hasActiveFilters: boolean
    isLoading?: boolean
    availableProjects?: Array<{ id: number; name: string; client_id?: number }>
    availableClients?: Array<{ id: number; name: string }>
}

const props = withDefaults(defineProps<Props>(), {
    visible: false,
    isLoading: false,
    availableProjects: () => [],
    availableClients: () => []
})

const emit = defineEmits<{
    'update:visible': [value: boolean]
    'update:filters': [filters: Partial<EventFilters>]
    'clear-filter': [key: keyof EventFilters]
    'clear-all': []
}>()

// État local
const localVisible = ref(props.visible)
const localFilters = ref<EventFilters>({ ...props.filters })
const searchInput = ref()
let searchTimeout: NodeJS.Timeout | null = null

// Computed
const displayableActiveFilters = computed(() => {
    return Array.isArray(props.activeFilters) ? props.activeFilters : []
})

const showPaymentOverdueFilter = computed(() => {
    // Afficher le filtre paiements en retard seulement si :
    // - Aucun type sélectionné (all)
    // - Type billing sélectionné
    // - PAS step sélectionné
    return !localFilters.value.event_type ||
           localFilters.value.event_type === 'all' ||
           localFilters.value.event_type === 'billing'
})

const showPaymentStatusFilter = computed(() => {
    // Afficher le filtre status de paiement seulement si :
    // - Aucun type sélectionné (all)
    // - Type billing sélectionné
    // - PAS step sélectionné
    return !localFilters.value.event_type ||
           localFilters.value.event_type === 'all' ||
           localFilters.value.event_type === 'billing'
})

// Projets filtrés selon le client sélectionné
const filteredProjects = computed(() => {
    if (!localFilters.value.client_id || localFilters.value.client_id === 'all') {
        return props.availableProjects
    }

    // Filtrer les projets qui appartiennent au client sélectionné
    const clientId = parseInt(localFilters.value.client_id)
    return props.availableProjects.filter(project =>
        project.client_id === clientId
    )
})

// Clients filtrés selon le projet sélectionné
const filteredClients = computed(() => {
    if (!localFilters.value.project_id || localFilters.value.project_id === 'all') {
        return props.availableClients
    }

    // Trouver le client du projet sélectionné
    const projectId = parseInt(localFilters.value.project_id)
    const selectedProject = props.availableProjects.find(project =>
        project.id === projectId
    )

    if (selectedProject && selectedProject.client_id) {
        return props.availableClients.filter(client =>
            client.id === selectedProject.client_id
        )
    }

    return props.availableClients
})

// Watchers
watch(() => props.visible, (newVal) => {
    localVisible.value = newVal
})

watch(() => props.filters, (newFilters) => {
    localFilters.value = { ...newFilters }
}, { deep: true })

// Watcher pour réinitialiser le projet quand le client change
watch(() => localFilters.value.client_id, (newClientId, oldClientId) => {
    // Si on passe de "all" à un client spécifique, ou si on change de client
    if (newClientId !== oldClientId && newClientId && newClientId !== 'all') {
        // Vérifier si le projet actuel appartient toujours au nouveau client
        const currentProjectId = localFilters.value.project_id
        if (currentProjectId && currentProjectId !== 'all') {
            const currentProject = props.availableProjects.find(p => p.id === parseInt(currentProjectId))
            const newClientIdNum = parseInt(newClientId)

            // Si le projet n'appartient pas au nouveau client, le réinitialiser
            if (!currentProject || currentProject.client_id !== newClientIdNum) {
                localFilters.value.project_id = 'all'
                applyFilters()
            }
        }
    }
    // Si on revient à "all", pas besoin de réinitialiser le projet
})

// Watcher pour réinitialiser le client quand le projet change
watch(() => localFilters.value.project_id, (newProjectId, oldProjectId) => {
    // Si on passe de "all" à un projet spécifique
    if (newProjectId !== oldProjectId && newProjectId && newProjectId !== 'all') {
        // Trouver le client du projet sélectionné
        const selectedProject = props.availableProjects.find(p => p.id === parseInt(newProjectId))
        if (selectedProject && selectedProject.client_id) {
            // Mettre à jour automatiquement le client
            localFilters.value.client_id = selectedProject.client_id.toString()
            applyFilters()
        }
    }
    // Si on revient à "all", remettre le client à "all" aussi
    else if (newProjectId === 'all' && localFilters.value.client_id !== 'all') {
        localFilters.value.client_id = 'all'
        applyFilters()
    }
})


// Methods
const toggleVisibility = () => {
    localVisible.value = !localVisible.value
    emit('update:visible', localVisible.value)

    if (localVisible.value) {
        nextTick(() => {
            searchInput.value?.$el?.focus()
        })
    }
}

const handleSearchInput = () => {
    if (searchTimeout) clearTimeout(searchTimeout)

    searchTimeout = setTimeout(() => {
        applyFilters()
    }, 300)
}

const handleFilterChange = () => {
    applyFilters()
}

const handleSortChange = () => {
    applyFilters()
}

const clearSearch = () => {
    localFilters.value.search = ''
    applyFilters()
}

const applyFilters = () => {
    const filtersToApply = { ...localFilters.value }

    // Si on change vers 'step', effacer les filtres de paiement AVANT la requête
    if (filtersToApply.event_type === 'step') {
        if (filtersToApply.payment_overdue) {
            filtersToApply.payment_overdue = false
            localFilters.value.payment_overdue = false
        }
        if (filtersToApply.payment_status) {
            filtersToApply.payment_status = undefined
            localFilters.value.payment_status = undefined
        }
    }

    emit('update:filters', filtersToApply)
}
</script>
