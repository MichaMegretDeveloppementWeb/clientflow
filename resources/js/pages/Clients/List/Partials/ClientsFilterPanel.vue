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
                                            placeholder="Nom, email, entreprise..."
                                            class="h-10 border-gray-300 pl-10 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
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
                                        v-model="localFilters.sort_by"
                                        @update:model-value="handleSortChange"
                                    >
                                        <SelectTrigger>
                                            <SelectValue placeholder="Sélectionner" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectItem value="name">Nom</SelectItem>
                                            <SelectItem value="email">Email</SelectItem>
                                            <SelectItem value="company">Entreprise</SelectItem>
                                            <SelectItem value="projects_count">Nombre de projets</SelectItem>
                                            <SelectItem value="total_revenue">Montant facturé</SelectItem>
                                            <SelectItem value="created_at">Ancienneté</SelectItem>
                                        </SelectContent>
                                    </Select>
                                </div>

                                <div class="space-y-2">
                                    <Label class="text-sm font-medium text-gray-700">
                                        Ordre
                                    </Label>
                                    <Select
                                        v-model="localFilters.sort_order"
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

                        <!-- Filtre retard de paiement -->
                        <div class="space-y-2 mt-10 mb-3">
                            <div class="flex items-center justify-center space-x-2">
                                <input
                                    id="overdue-payments"
                                    v-model="localFilters.has_overdue_payments"
                                    type="checkbox"
                                    class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500 focus:ring-offset-0"
                                    @change="applyFilters"
                                />
                                <Label
                                    for="overdue-payments"
                                    class="text-sm font-medium text-gray-700 cursor-pointer"
                                >
                                    <div class="flex items-center gap-1.5">
                                        Clients en retard de paiement
                                        <Icon name="alert-triangle" class="h-3.5 w-3.5 text-red-500" />
                                    </div>
                                </Label>
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
import ActiveFilterTag from './ActiveFilterTag.vue'
import type { ClientFilters, ActiveFilter } from '@/types/clients/list'

/**
 * Panneau de filtres pour la liste des clients
 * Responsabilités :
 * - Interface de saisie des filtres
 * - Affichage des filtres actifs
 * - Debouncing de la recherche
 * - Émission des changements
 */

interface Props {
    visible?: boolean
    filters: ClientFilters
    activeFilters: ActiveFilter[]
    hasActiveFilters: boolean
    isLoading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
    visible: false,
    isLoading: false
})

const emit = defineEmits<{
    'update:visible': [value: boolean]
    'update:filters': [filters: Partial<ClientFilters>]
    'clear-filter': [key: keyof ClientFilters]
    'clear-all': []
}>()

// État local
const localVisible = ref(props.visible)
const localFilters = ref<ClientFilters>({ ...props.filters })
const searchInput = ref()
let searchTimeout: NodeJS.Timeout | null = null

// Computed
const displayableActiveFilters = computed(() => {
    return Array.isArray(props.activeFilters) 
        ? props.activeFilters.filter(f => f.type !== 'select')
        : []
})

// Watchers
watch(() => props.visible, (newVal) => {
    localVisible.value = newVal
})

watch(() => props.filters, (newFilters) => {
    localFilters.value = { ...newFilters }
}, { deep: true })

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

const handleSortChange = () => {
    applyFilters()
}

const clearSearch = () => {
    localFilters.value.search = ''
    applyFilters()
}

const applyFilters = () => {
    emit('update:filters', { ...localFilters.value })
}
</script>
