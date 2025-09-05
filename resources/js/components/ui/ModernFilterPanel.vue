<template>
    <div class="space-y-4">
        <!-- Modern toggle button with improved visual feedback -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <Button
                    @click="toggleVisibility"
                    :variant="hasActiveFilters ? 'default' : 'outline'"
                    :class="[
                        'transition-all duration-200',
                        hasActiveFilters 
                            ? 'bg-blue-600 hover:bg-blue-700 text-white' 
                            : 'border-gray-300 hover:bg-gray-50'
                    ]"
                >
                    <Icon name="filter" class="mr-2 h-4 w-4" />
                    Filtres
                    <Badge
                        v-if="hasActiveFilters"
                        :variant="hasActiveFilters ? 'secondary' : 'outline'"
                        class="ml-2 bg-blue-100 text-blue-800 border-blue-200"
                    >
                        {{ displayableActiveFilters.length }}
                    </Badge>
                    <Icon
                        name="chevron-down"
                        class="ml-2 h-4 w-4 transition-transform duration-200"
                        :class="{ 'rotate-180': localVisible }"
                    />
                </Button>

                <!-- Quick filter status -->
                <div v-if="hasActiveFilters" class="hidden sm:flex items-center gap-2">
                    <div class="flex items-center gap-1.5 px-2 py-1 bg-blue-50 rounded-md">
                        <Icon name="check-circle" class="h-3 w-3 text-blue-600" />
                        <span class="text-xs font-medium text-blue-700">
                            {{ displayableActiveFilters.length }} filtre{{ displayableActiveFilters.length > 1 ? 's' : '' }} actif{{ displayableActiveFilters.length > 1 ? 's' : '' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Clear all button - redesigned -->
            <Button
                v-if="hasActiveFilters"
                variant="ghost"
                size="sm"
                class="h-8 px-3 text-xs text-red-600 hover:bg-red-50 hover:text-red-700 border border-red-200 hover:border-red-300"
                @click="$emit('clear-all')"
            >
                <Icon name="x" class="mr-1 h-3 w-3" />
                Tout effacer
            </Button>
        </div>

        <!-- Modern collapsible filters section with better animation -->
        <Transition
            enter-active-class="transition-all duration-300 ease-out"
            enter-from-class="transform opacity-0 scale-95 -translate-y-4"
            enter-to-class="transform opacity-100 scale-100 translate-y-0"
            leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="transform opacity-100 scale-100 translate-y-0"
            leave-to-class="transform opacity-0 scale-95 -translate-y-4"
        >
            <Card 
                v-show="localVisible" 
                class="border border-gray-200 bg-gradient-to-br from-white to-gray-50/50 shadow-lg backdrop-blur-sm"
            >
                <CardContent class="p-6">
                    <div class="space-y-8">
                        <!-- Search section with modern design -->
                        <div class="space-y-3">
                            <div class="flex items-center gap-2 mb-4">
                                <Icon name="search" class="h-4 w-4 text-gray-500" />
                                <h4 class="text-sm font-semibold text-gray-800">Recherche</h4>
                            </div>
                            
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <Icon name="search" class="h-5 w-5 text-gray-400" />
                                </div>
                                <Input
                                    ref="searchInput"
                                    :model-value="searchValue"
                                    type="text"
                                    :placeholder="searchPlaceholder"
                                    class="h-11 border-gray-200 pl-10 pr-10 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 bg-white"
                                    @input="handleSearchInput"
                                    @keydown.enter="handleSearchEnter"
                                />
                                <Button
                                    v-if="searchValue"
                                    variant="ghost"
                                    size="sm"
                                    class="absolute right-1 top-1/2 h-7 w-7 -translate-y-1/2 transform px-0 hover:bg-gray-100"
                                    @click="clearSearch"
                                >
                                    <Icon name="x" class="h-4 w-4" />
                                </Button>
                            </div>
                        </div>

                        <!-- Dynamic filter sections -->
                        <div v-if="filterSections.length > 0" class="space-y-8">
                            <div 
                                v-for="section in filterSections" 
                                :key="section.key"
                                class="space-y-4"
                            >
                                <div class="flex items-center gap-2">
                                    <Icon :name="section.icon" class="h-4 w-4 text-gray-500" />
                                    <h4 class="text-sm font-semibold text-gray-800">{{ section.title }}</h4>
                                </div>

                                <!-- Grid layout for filters -->
                                <div :class="[
                                    'grid gap-4',
                                    section.layout === 'single' ? 'grid-cols-1' : 
                                    section.layout === 'double' ? 'sm:grid-cols-2' : 
                                    'sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3'
                                ]">
                                    <div 
                                        v-for="filter in section.filters" 
                                        :key="filter.key"
                                        class="space-y-2"
                                    >
                                        <Label class="text-sm font-medium text-gray-700">
                                            {{ filter.label }}
                                        </Label>

                                        <!-- Select filter -->
                                        <Select
                                            v-if="filter.type === 'select'"
                                            :model-value="filter.value"
                                            @update:model-value="(value) => handleFilterChange(filter.key, value)"
                                        >
                                            <SelectTrigger class="h-10 border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                                                <SelectValue :placeholder="filter.placeholder" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem 
                                                    v-for="option in filter.options" 
                                                    :key="option.value"
                                                    :value="option.value"
                                                >
                                                    {{ option.label }}
                                                </SelectItem>
                                            </SelectContent>
                                        </Select>

                                        <!-- Checkbox filter -->
                                        <div 
                                            v-else-if="filter.type === 'checkbox'"
                                            class="flex items-center space-x-3 p-3 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                                        >
                                            <input
                                                :id="filter.key"
                                                :checked="filter.value"
                                                type="checkbox"
                                                :class="[
                                                    'h-4 w-4 rounded border-gray-300 focus:ring-2 focus:ring-offset-0',
                                                    filter.color === 'red' ? 'text-red-600 focus:ring-red-500' :
                                                    filter.color === 'orange' ? 'text-orange-600 focus:ring-orange-500' :
                                                    'text-blue-600 focus:ring-blue-500'
                                                ]"
                                                @change="(e) => handleFilterChange(filter.key, e.target.checked)"
                                            />
                                            <Label
                                                :for="filter.key"
                                                class="text-sm font-medium text-gray-700 cursor-pointer flex-1"
                                            >
                                                <div class="flex items-center gap-2">
                                                    <span>{{ filter.label }}</span>
                                                    <Icon 
                                                        v-if="filter.icon"
                                                        :name="filter.icon" 
                                                        :class="[
                                                            'h-3.5 w-3.5',
                                                            filter.color === 'red' ? 'text-red-500' :
                                                            filter.color === 'orange' ? 'text-orange-500' :
                                                            'text-blue-500'
                                                        ]" 
                                                    />
                                                </div>
                                            </Label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Active filters display - improved -->
                        <div v-if="hasActiveFilters" class="pt-6 border-t border-gray-200">
                            <div class="space-y-3">
                                <div class="flex items-center gap-2">
                                    <Icon name="tag" class="h-4 w-4 text-gray-500" />
                                    <span class="text-sm font-semibold text-gray-800">
                                        Filtres actifs
                                    </span>
                                </div>
                                
                                <div class="flex flex-wrap gap-2">
                                    <div
                                        v-for="filter in displayableActiveFilters"
                                        :key="filter.key"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-100 text-blue-800 text-xs font-medium rounded-full border border-blue-200"
                                    >
                                        <Icon name="filter" class="h-3 w-3" />
                                        <span>{{ filter.label }}</span>
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            class="h-4 w-4 p-0 hover:bg-blue-200 rounded-full"
                                            @click="$emit('clear-filter', filter.key)"
                                        >
                                            <Icon name="x" class="h-3 w-3" />
                                        </Button>
                                    </div>
                                </div>
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
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select'
import Icon from '@/components/Icon.vue'

export interface FilterOption {
    value: string
    label: string
}

export interface FilterConfig {
    key: string
    type: 'select' | 'checkbox'
    label: string
    placeholder?: string
    value: any
    options?: FilterOption[]
    icon?: string
    color?: 'blue' | 'red' | 'orange'
}

export interface FilterSection {
    key: string
    title: string
    icon: string
    layout: 'single' | 'double' | 'triple'
    filters: FilterConfig[]
}

export interface ActiveFilter {
    key: string
    label: string
    type?: string
}

interface Props {
    visible?: boolean
    searchValue?: string
    searchPlaceholder?: string
    filterSections: FilterSection[]
    activeFilters: ActiveFilter[]
    hasActiveFilters: boolean
    isLoading?: boolean
}

const props = withDefaults(defineProps<Props>(), {
    visible: false,
    isLoading: false,
    searchValue: '',
    searchPlaceholder: 'Rechercher...'
})

const emit = defineEmits<{
    'update:visible': [value: boolean]
    'search-input': [value: string]
    'search-enter': [value: string]
    'clear-search': []
    'filter-change': [key: string, value: any]
    'clear-filter': [key: string]
    'clear-all': []
}>()

// État local
const localVisible = ref(props.visible)
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

const handleSearchInput = (event: Event) => {
    const target = event.target as HTMLInputElement
    const value = target.value

    if (searchTimeout) clearTimeout(searchTimeout)
    
    searchTimeout = setTimeout(() => {
        emit('search-input', value)
    }, 300)
}

const handleSearchEnter = (event: Event) => {
    const target = event.target as HTMLInputElement
    const value = target.value
    
    if (searchTimeout) clearTimeout(searchTimeout)
    emit('search-enter', value)
}

const clearSearch = () => {
    emit('clear-search')
}

const handleFilterChange = (key: string, value: any) => {
    emit('filter-change', key, value)
}
</script>