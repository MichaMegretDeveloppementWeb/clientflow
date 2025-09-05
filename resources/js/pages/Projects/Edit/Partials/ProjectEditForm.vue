<template>
    <div class="mx-auto max-w-[100em]">
        <!-- Message d'erreur -->
        <Card v-if="hasError && !isLoading" class="border border-red-200 bg-red-50 shadow-sm">
            <CardContent class="p-6">
                <div class="flex items-center justify-center py-12">
                    <div class="text-center">
                        <Icon name="alert-triangle" class="mx-auto h-12 w-12 text-red-400 mb-4" />
                        <h3 class="text-lg font-medium text-red-900 mb-2">
                            Erreur de chargement
                        </h3>
                        <p class="text-sm text-red-700 mb-4">
                            Impossible de charger les données du projet.
                        </p>
                        <Button
                            variant="outline"
                            @click="reloadPage"
                            class="border-red-300 text-red-700 hover:bg-red-100"
                        >
                            <Icon name="refresh-cw" class="mr-2 h-4 w-4" />
                            Réessayer
                        </Button>
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- Formulaire principal -->
        <Card v-else class="border border-gray-200 bg-white shadow-sm">
            <CardContent class="p-6">
                <!-- Skeleton du formulaire -->
                <div v-if="isLoading" class="space-y-8">
                    <!-- Section Informations de base -->
                    <div class="space-y-6">
                        <div class="border-b border-gray-100 pb-4">
                            <div class="h-6 w-48 bg-gray-200 rounded animate-pulse mb-2"></div>
                            <div class="h-4 w-64 bg-gray-200 rounded animate-pulse"></div>
                        </div>
                        <div class="grid gap-6 sm:grid-cols-2">
                            <div class="space-y-3">
                                <div class="h-4 w-32 bg-gray-200 rounded animate-pulse"></div>
                                <div class="h-12 bg-gray-200 rounded animate-pulse"></div>
                            </div>
                            <div class="space-y-3">
                                <div class="h-4 w-24 bg-gray-200 rounded animate-pulse"></div>
                                <div class="h-12 bg-gray-200 rounded animate-pulse"></div>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="h-4 w-20 bg-gray-200 rounded animate-pulse"></div>
                            <div class="h-12 bg-gray-200 rounded animate-pulse"></div>
                        </div>
                    </div>

                    <!-- Section Planification -->
                    <div class="space-y-6">
                        <div class="border-b border-gray-100 pb-4">
                            <div class="h-6 w-32 bg-gray-200 rounded animate-pulse mb-2"></div>
                            <div class="h-4 w-56 bg-gray-200 rounded animate-pulse"></div>
                        </div>
                        <div class="space-y-3">
                            <div class="h-4 w-28 bg-gray-200 rounded animate-pulse"></div>
                            <div class="h-24 bg-gray-200 rounded animate-pulse"></div>
                        </div>
                        <div class="grid gap-6 sm:grid-cols-3">
                            <div class="space-y-3">
                                <div class="h-4 w-32 bg-gray-200 rounded animate-pulse"></div>
                                <div class="h-12 bg-gray-200 rounded animate-pulse"></div>
                            </div>
                            <div class="space-y-3">
                                <div class="h-4 w-36 bg-gray-200 rounded animate-pulse"></div>
                                <div class="h-12 bg-gray-200 rounded animate-pulse"></div>
                            </div>
                            <div class="space-y-3">
                                <div class="h-4 w-24 bg-gray-200 rounded animate-pulse"></div>
                                <div class="h-12 bg-gray-200 rounded animate-pulse"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col items-center justify-between gap-4 border-t border-gray-100 pt-6 sm:flex-row">
                        <div class="flex gap-2">
                            <div class="h-12 w-24 bg-gray-200 rounded animate-pulse"></div>
                            <div class="h-12 w-28 bg-gray-200 rounded animate-pulse"></div>
                        </div>
                        <div class="h-12 w-36 bg-gray-200 rounded animate-pulse"></div>
                    </div>
                </div>

                <!-- Formulaire réel -->
                <form v-else-if="!isLoading && !hasError && project" @submit.prevent="submit" class="space-y-8">
                    <!-- General errors -->
                    <div v-if="form.errors.general" class="rounded-xl bg-red-50 border border-red-200 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <Icon name="alert-circle" class="h-5 w-5 text-red-400" />
                            </div>
                            <div class="ml-3">
                                <div class="text-sm text-red-700 font-medium">
                                    {{ form.errors.general }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section Informations de base -->
                    <div class="space-y-8">
                        <div class="space-y-1">
                            <h3 class="text-xl font-semibold text-gray-900 tracking-tight">Informations de base</h3>
                            <p class="text-sm text-gray-600">Les informations essentielles de votre projet</p>
                        </div>

                        <div class="space-y-6">
                            <div class="grid gap-6 lg:grid-cols-2">
                                <!-- Client (lecture seule) -->
                                <div class="space-y-2">
                                    <Label class="text-sm font-medium text-gray-900">Client assigné</Label>
                                    <div class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50/50 p-4">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100">
                                            <Icon name="user" class="h-5 w-5 text-blue-600" />
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ project.client.name }}</p>
                                            <p v-if="project.client.company" class="text-sm text-gray-600">{{ project.client.company }}</p>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500">Le client ne peut pas être modifié après création</p>
                                </div>

                                <!-- Statut -->
                                <div class="space-y-2">
                                    <Label for="status" class="text-sm font-medium text-gray-900">Statut *</Label>
                                    <div class="relative">
                                        <Icon name="play-circle" class="absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 transform text-green-400 pointer-events-none" />
                                        <select
                                            id="status"
                                            v-model="form.status"
                                            required
                                            class="h-12 w-full appearance-none rounded-lg border border-gray-200 bg-white py-2 pr-10 pl-11 text-sm transition-all focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 hover:border-gray-300"
                                            :class="{ 'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500/10': form.errors.status }"
                                        >
                                            <option value="active">Actif</option>
                                            <option value="completed">Terminé</option>
                                            <option value="on_hold">En pause</option>
                                            <option value="cancelled">Annulé</option>
                                        </select>
                                        <Icon name="chevron-down" class="absolute top-1/2 right-3.5 h-4 w-4 -translate-y-1/2 transform text-gray-400 pointer-events-none" />
                                    </div>
                                    <InputError :message="form.errors.status" />
                                    <p class="text-xs text-gray-500">État actuel du projet</p>
                                </div>
                            </div>

                            <!-- Nom du projet -->
                            <div class="space-y-2">
                                <Label for="name" class="text-sm font-medium text-gray-900">Nom du projet *</Label>
                                <div class="relative">
                                    <Icon name="folder" class="absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 transform text-purple-400" />
                                    <Input
                                        id="name"
                                        v-model="form.name"
                                        type="text"
                                        required
                                        placeholder="ex: Site web corporate"
                                        class="h-12 border-gray-200 pl-11 pr-4 transition-all focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 hover:border-gray-300 rounded-lg"
                                        :class="{ 'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500/10': form.errors.name }"
                                    />
                                </div>
                                <InputError :message="form.errors.name" />
                                <p class="text-xs text-gray-500">Nom descriptif qui identifie clairement le projet</p>
                            </div>
                        </div>
                    </div>

                    <!-- Section Planification -->
                    <div class="space-y-8">
                        <div class="space-y-1">
                            <h3 class="text-xl font-semibold text-gray-900 tracking-tight">Planification</h3>
                            <p class="text-sm text-gray-600">Définissez les détails et le planning de votre projet</p>
                        </div>

                        <div class="space-y-6">
                            <!-- Description -->
                            <div class="space-y-2">
                                <Label for="description" class="text-sm font-medium text-gray-900">Description</Label>
                                <div class="relative">
                                    <Icon name="file-text" class="absolute top-3.5 left-3.5 h-4 w-4 text-indigo-400" />
                                    <textarea
                                        id="description"
                                        v-model="form.description"
                                        rows="4"
                                        placeholder="Décrivez les objectifs, le périmètre et les livrables du projet..."
                                        class="min-h-[110px] w-full resize-none rounded-lg border border-gray-200 bg-white py-3.5 pr-4 pl-11 text-sm placeholder:text-gray-400 transition-all focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 hover:border-gray-300"
                                        :class="{ 'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500/10': form.errors.description }"
                                    ></textarea>
                                </div>
                                <InputError :message="form.errors.description" />
                                <p class="text-xs text-gray-500">Détails sur les objectifs et les livrables attendus</p>
                            </div>

                            <!-- Grille responsive pour les dates et budget -->
                            <div class="grid gap-6 sm:grid-cols-3">
                                <!-- Date de début -->
                                <div class="space-y-2">
                                    <Label for="start_date" class="text-sm font-medium text-gray-900">Date de début *</Label>
                                    <div class="relative">
                                        <Icon name="calendar" class="absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 transform text-emerald-400" />
                                        <Input
                                            id="start_date"
                                            v-model="form.start_date"
                                            type="date"
                                            required
                                            class="h-12 border-gray-200 pl-11 pr-4 transition-all focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 hover:border-gray-300 rounded-lg"
                                            :class="{ 'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500/10': form.errors.start_date }"
                                        />
                                    </div>
                                    <InputError :message="form.errors.start_date" />
                                    <p class="text-xs text-gray-500">Début prévu du projet</p>
                                </div>

                                <!-- Date de fin -->
                                <div class="space-y-2">
                                    <Label for="end_date" class="text-sm font-medium text-gray-900">Date de fin</Label>
                                    <div class="relative">
                                        <Icon name="calendar-check" class="absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 transform text-orange-400" />
                                        <Input
                                            id="end_date"
                                            v-model="form.end_date"
                                            type="date"
                                            :min="form.start_date || undefined"
                                            class="h-12 border-gray-200 pl-11 pr-4 transition-all focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 hover:border-gray-300 rounded-lg"
                                            :class="{ 'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500/10': form.errors.end_date }"
                                        />
                                    </div>
                                    <InputError :message="form.errors.end_date" />
                                    <p class="text-xs text-gray-500">Fin prévue du projet</p>
                                </div>

                                <!-- Budget -->
                                <div class="space-y-2">
                                    <Label for="budget" class="text-sm font-medium text-gray-900">Budget (€)</Label>
                                    <div class="relative">
                                        <Icon name="banknote" class="absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 transform text-green-400" />
                                        <Input
                                            id="budget"
                                            v-model="form.budget"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            placeholder="0.00"
                                            class="h-12 border-gray-200 pl-11 pr-4 transition-all focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 hover:border-gray-300 rounded-lg"
                                            :class="{ 'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500/10': form.errors.budget }"
                                        />
                                    </div>
                                    <InputError :message="form.errors.budget" />
                                    <p class="text-xs text-gray-500">Budget initial prévu</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col gap-4 border-t border-gray-200 bg-gray-50/50 rounded-b-xl pt-8 pb-2 -mx-6 px-6 sm:flex-row sm:items-center sm:justify-between">
                        <div class="order-2 sm:order-1 flex flex-wrap gap-3">
                            <Button
                                variant="outline"
                                type="button"
                                @click="handleCancel"
                                class="h-12 px-6 text-sm font-medium transition-all hover:bg-gray-100 border-gray-300 flex-1 sm:flex-none"
                            >
                                <Icon name="x" class="mr-2 h-4 w-4" />
                                Annuler
                            </Button>
                            <Button
                                variant="outline"
                                type="button"
                                @click="resetForm"
                                class="h-12 px-6 text-sm font-medium transition-all hover:bg-amber-50 border-amber-300 text-amber-700 hover:text-amber-800 flex-1 sm:flex-none"
                            >
                                <Icon name="rotate-ccw" class="mr-2 h-4 w-4" />
                                Réinitialiser
                            </Button>
                            <Button
                                variant="destructive"
                                type="button"
                                @click="showDeleteModal = true"
                                :disabled="form.processing"
                                class="h-12 px-6 text-sm font-medium transition-all hover:bg-red-600 bg-red-500 text-white border-red-500 hover:border-red-600 flex-1 sm:flex-none"
                            >
                                <Icon name="trash-2" class="mr-2 h-4 w-4" />
                                Supprimer
                            </Button>
                        </div>
                        <div class="order-1 sm:order-2">
                            <Button
                                type="submit"
                                :disabled="form.processing"
                                class="h-12 px-8 w-full sm:w-auto bg-purple-600 text-white font-medium transition-all hover:bg-purple-700 hover:shadow-lg hover:shadow-purple-600/25 focus:ring-4 focus:ring-purple-500/20 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:shadow-none"
                            >
                                <Icon v-if="form.processing" name="loader-2" class="mr-2 h-4 w-4 animate-spin" />
                                <Icon v-else name="save" class="mr-2 h-4 w-4" />
                                {{ form.processing ? 'Mise à jour en cours...' : 'Mettre à jour' }}
                            </Button>
                        </div>
                    </div>
                </form>
            </CardContent>
        </Card>

        <!-- Modale de confirmation de suppression -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition-all duration-300 ease-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition-all duration-200 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="showDeleteModal" class="fixed inset-0 z-[9999] overflow-y-auto">
                    <!-- Overlay -->
                    <div 
                        class="fixed inset-0 bg-black/50 backdrop-blur-sm"
                        @click="showDeleteModal = false"
                    ></div>
                    
                    <!-- Modal -->
                    <div class="flex min-h-full items-center justify-center p-4">
                        <Transition
                            enter-active-class="transition-all duration-300 ease-out"
                            enter-from-class="opacity-0 scale-95 translate-y-4"
                            enter-to-class="opacity-100 scale-100 translate-y-0"
                            leave-active-class="transition-all duration-200 ease-in"
                            leave-from-class="opacity-100 scale-100 translate-y-0"
                            leave-to-class="opacity-0 scale-95 translate-y-4"
                        >
                            <Card v-if="showDeleteModal" class="relative z-10 w-full max-w-md bg-white shadow-xl">
                                <CardContent class="p-6">
                                    <div class="flex items-center gap-4 mb-6">
                                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                                            <Icon name="trash-2" class="h-6 w-6 text-red-600" />
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">Supprimer le projet</h3>
                                            <p class="text-sm text-gray-600">Cette action est irréversible</p>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-6">
                                        <p class="text-sm text-gray-700">
                                            Êtes-vous sûr de vouloir supprimer le projet 
                                            <span class="font-semibold text-gray-900">{{ project?.name }}</span> ?
                                        </p>
                                        <p class="text-sm text-red-600 mt-2">
                                            Tous les événements associés seront également supprimés.
                                        </p>
                                    </div>
                                    
                                    <div class="flex justify-end gap-3">
                                        <Button
                                            variant="outline"
                                            @click="showDeleteModal = false"
                                            class="h-10 px-4"
                                        >
                                            Annuler
                                        </Button>
                                        <Button
                                            variant="destructive"
                                            @click="confirmDelete"
                                            class="h-10 px-4 bg-red-600 hover:bg-red-700"
                                        >
                                            <Icon name="trash-2" class="mr-2 h-4 w-4" />
                                            Supprimer
                                        </Button>
                                    </div>
                                </CardContent>
                            </Card>
                        </Transition>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>

<script setup lang="ts">
import { watch, ref } from 'vue'
import { Card, CardContent } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import Icon from '@/components/Icon.vue'
import InputError from '@/components/InputError.vue'
import { useForm, router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import type { ProjectEditData } from '@/types/projects/edit'

interface Props {
    projectId: number
    isLoading: boolean
    hasError: boolean
    project: ProjectEditData | null
    errors: Record<string, any>
}

const props = defineProps<Props>()

// Formulaire Inertia simple
const form = useForm({
    name: '',
    description: '',
    status: 'active' as 'active' | 'completed' | 'on_hold' | 'cancelled',
    budget: '',
    start_date: '',
    end_date: ''
})

// Variables pour stocker les valeurs originales et gérer la modal
const originalData = ref<ProjectEditData | null>(null)
const showDeleteModal = ref(false)

// Initialiser les données du formulaire
watch(
    () => props.project,
    (newProject) => {
        if (newProject && !props.isLoading && !props.hasError) {
            // Stocker les valeurs originales pour la réinitialisation
            originalData.value = { ...newProject }
            
            // Remplir le formulaire
            form.name = newProject.name
            form.description = newProject.description || ''
            form.status = newProject.status
            form.budget = newProject.budget ? String(newProject.budget) : ''
            form.start_date = newProject.start_date ? newProject.start_date.split('T')[0] : ''
            form.end_date = newProject.end_date ? newProject.end_date.split('T')[0] : ''
        }
    },
    { immediate: true }
)

// Actions
const submit = () => {
    if (!props.project) return
    form.put(route('projects.update', props.project.id))
}

const handleCancel = () => {
    window.history.back()
}

const resetForm = () => {
    if (!originalData.value) return
    
    // Restaurer les valeurs originales
    form.name = originalData.value.name
    form.description = originalData.value.description || ''
    form.status = originalData.value.status
    form.budget = originalData.value.budget ? String(originalData.value.budget) : ''
    form.start_date = originalData.value.start_date ? originalData.value.start_date.split('T')[0] : ''
    form.end_date = originalData.value.end_date ? originalData.value.end_date.split('T')[0] : ''
    
    // Effacer les erreurs
    form.clearErrors()
}

const reloadPage = () => {
    window.location.reload()
}

// Fonction pour supprimer le projet
const confirmDelete = () => {
    if (!props.project) return
    
    router.delete(route('projects.destroy', props.project.id), {
        onSuccess: () => {
            showDeleteModal.value = false
        }
    })
}
</script>