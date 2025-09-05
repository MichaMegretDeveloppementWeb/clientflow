<template>
    <div class="relative">
        <div class="relative">
            <Icon name="folder" class="absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 transform text-blue-400 pointer-events-none" />
            <input
                ref="searchInput"
                v-model="searchQuery"
                @input="handleInput"
                @focus="handleFocus"
                @blur="handleBlur"
                @keydown="handleKeydown"
                type="text"
                :placeholder="placeholder"
                :required="required"
                class="h-12 w-full rounded-lg border border-gray-200 bg-white py-2 pr-10 pl-11 text-sm transition-all focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 hover:border-gray-300"
                :class="{
                    'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500/10': hasError,
                    'rounded-b-none': isDropdownOpen && filteredProjects.length > 0
                }"
            />
            <Icon 
                name="chevron-down" 
                class="absolute top-1/2 right-3.5 h-4 w-4 -translate-y-1/2 transform text-gray-400 pointer-events-none transition-transform"
                :class="{ 'rotate-180': isDropdownOpen && filteredProjects.length > 0 }"
            />
        </div>

        <!-- Dropdown avec liste filtrée -->
        <Transition
            enter-active-class="transition-all duration-200 ease-out"
            enter-from-class="opacity-0 -translate-y-1"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition-all duration-150 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 -translate-y-1"
        >
            <div 
                v-if="isDropdownOpen && filteredProjects.length > 0"
                class="absolute top-full left-0 right-0 z-50 max-h-60 overflow-y-auto rounded-b-lg border border-t-0 border-gray-200 bg-white shadow-lg"
            >
                <div 
                    v-for="(project, index) in filteredProjects" 
                    :key="project.id"
                    @mousedown.prevent="selectProject(project)"
                    class="flex cursor-pointer items-center gap-3 px-4 py-3 transition-colors hover:bg-gray-50"
                    :class="{ 
                        'bg-gray-50': index === highlightedIndex,
                        'border-b border-gray-100': index < filteredProjects.length - 1
                    }"
                >
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100">
                        <Icon name="folder" class="h-4 w-4 text-blue-600" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-900 truncate">{{ project.name }}</p>
                        <p class="text-sm text-gray-600 truncate">{{ project.client.name }}</p>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- Message "Aucun résultat" -->
        <div 
            v-if="isDropdownOpen && searchQuery && filteredProjects.length === 0"
            class="absolute top-full left-0 right-0 z-50 rounded-b-lg border border-t-0 border-gray-200 bg-white p-4 shadow-lg"
        >
            <div class="flex items-center gap-3 text-gray-500">
                <Icon name="search-x" class="h-4 w-4" />
                <span class="text-sm">Aucun projet trouvé pour "{{ searchQuery }}"</span>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, ref, nextTick, watch } from 'vue'
import Icon from '@/components/Icon.vue'

interface Project {
    id: number
    name: string
    client: {
        id: number
        name: string
    }
}

interface Props {
    projects: Project[]
    modelValue?: number | null
    placeholder?: string
    required?: boolean
    hasError?: boolean
}

interface Emits {
    (e: 'update:modelValue', value: number | null): void
    (e: 'change', project: Project | null): void
}

const props = withDefaults(defineProps<Props>(), {
    placeholder: 'Rechercher un projet...',
    required: false,
    hasError: false
})

const emit = defineEmits<Emits>()

const searchInput = ref<HTMLInputElement>()
const searchQuery = ref('')
const isDropdownOpen = ref(false)
const highlightedIndex = ref(-1)
const selectedProject = ref<Project | null>(null)

// Trouve le projet sélectionné par ID
watch(() => props.modelValue, (newValue) => {
    if (newValue) {
        const project = props.projects.find(p => p.id === newValue)
        if (project) {
            selectedProject.value = project
            searchQuery.value = `${project.client.name} - ${project.name}`
        }
    } else {
        selectedProject.value = null
        searchQuery.value = ''
    }
}, { immediate: true })

// Filtre les projets en temps réel
const filteredProjects = computed(() => {
    if (!searchQuery.value || searchQuery.value === getDisplayValue(selectedProject.value)) {
        return props.projects
    }
    
    const query = searchQuery.value.toLowerCase().trim()
    return props.projects.filter(project => 
        project.name.toLowerCase().includes(query) || 
        project.client.name.toLowerCase().includes(query)
    )
})

const getDisplayValue = (project: Project | null): string => {
    return project ? `${project.client.name} - ${project.name}` : ''
}

const handleInput = () => {
    if (!isDropdownOpen.value) {
        isDropdownOpen.value = true
    }
    highlightedIndex.value = -1
    
    // Si l'utilisateur tape et que ce n'est plus la valeur du projet sélectionné
    if (selectedProject.value && searchQuery.value !== getDisplayValue(selectedProject.value)) {
        selectedProject.value = null
        emit('update:modelValue', null)
        emit('change', null)
    }
}

const handleFocus = () => {
    isDropdownOpen.value = true
    highlightedIndex.value = -1
}

const handleBlur = () => {
    // Délai pour permettre la sélection par clic
    setTimeout(() => {
        isDropdownOpen.value = false
        
        // Si aucun projet n'est sélectionné, vider le champ
        if (!selectedProject.value) {
            searchQuery.value = ''
        } else {
            // Restaurer la valeur affichée du projet sélectionné
            searchQuery.value = getDisplayValue(selectedProject.value)
        }
    }, 150)
}

const handleKeydown = (event: KeyboardEvent) => {
    if (!isDropdownOpen.value || filteredProjects.value.length === 0) return
    
    switch (event.key) {
        case 'ArrowDown':
            event.preventDefault()
            highlightedIndex.value = Math.min(highlightedIndex.value + 1, filteredProjects.value.length - 1)
            break
            
        case 'ArrowUp':
            event.preventDefault()
            highlightedIndex.value = Math.max(highlightedIndex.value - 1, -1)
            break
            
        case 'Enter':
            event.preventDefault()
            if (highlightedIndex.value >= 0 && highlightedIndex.value < filteredProjects.value.length) {
                selectProject(filteredProjects.value[highlightedIndex.value])
            }
            break
            
        case 'Escape':
            event.preventDefault()
            isDropdownOpen.value = false
            searchInput.value?.blur()
            break
    }
}

const selectProject = (project: Project) => {
    selectedProject.value = project
    searchQuery.value = getDisplayValue(project)
    isDropdownOpen.value = false
    highlightedIndex.value = -1
    
    emit('update:modelValue', project.id)
    emit('change', project)
    
    // Enlever le focus du champ
    nextTick(() => {
        searchInput.value?.blur()
    })
}
</script>