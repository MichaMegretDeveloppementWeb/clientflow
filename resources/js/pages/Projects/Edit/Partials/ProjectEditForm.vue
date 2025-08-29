<template>
    <Card class="border-0 bg-white shadow-sm ring-1 ring-gray-200/60 gap-0">
        <CardHeader class="border-b border-gray-100">
            <CardTitle class="text-lg font-semibold text-gray-900">Informations sur projet</CardTitle>
        </CardHeader>
        <CardContent class="p-6">
            <!-- Error State -->
            <div v-if="hasError && !isLoading" class="mb-6 rounded-md bg-red-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <Icon name="alert-circle" class="h-5 w-5 text-red-400" />
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            Impossible de charger le projet
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p>Une erreur est survenue. Veuillez actualiser la page.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="isLoading" class="space-y-6">
                <div class="grid gap-6 md:grid-cols-2">
                    <div v-for="i in 6" :key="i" class="space-y-2">
                        <div class="h-4 bg-gray-200 rounded animate-pulse w-20"></div>
                        <div class="h-10 bg-gray-200 rounded animate-pulse"></div>
                    </div>
                </div>
                <div class="flex justify-end space-x-3">
                    <div class="h-10 bg-gray-200 rounded animate-pulse w-20"></div>
                    <div class="h-10 bg-gray-200 rounded animate-pulse w-32"></div>
                </div>
            </div>

            <!-- Form -->
            <form v-if="!isLoading && !hasError && project" @submit.prevent="submit" class="space-y-6">

                <!-- Nom du projet -->
                <div class="space-y-3">
                    <Label for="name" class="text-sm font-medium text-gray-700">
                        Nom du projet <span class="text-red-500">*</span>
                    </Label>
                    <Input
                        id="name"
                        v-model="form.name"
                        type="text"
                        placeholder="Entrez le nom du projet"
                        :class="{ 'border-red-300': form.errors.name }"
                    />
                    <p v-if="form.errors.name" class="text-sm text-red-600">{{ form.errors.name }}</p>
                </div>

                <!-- Client et Statut côte à côte -->
                <div class="grid gap-6 md:grid-cols-2">
                    <!-- Client (affichage simple) -->
                    <div class="space-y-3">
                        <Label class="text-sm font-medium text-gray-700">Client</Label>
                        <div class="flex items-center gap-3 p-2.5 bg-gray-50 rounded-md border border-gray-200">
                            <Icon name="user" class="h-4 w-4 text-gray-400" />
                            <div class="flex items-center justify-start gap-2">
                                <span class="text-sm font-medium text-gray-900">{{ project.client.name }}</span>
                                <span v-if="project.client.company" class="text-xs text-gray-500">{{ project.client.company }}</span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500">Le client ne peut pas être modifié après création</p>
                    </div>

                    <!-- Statut -->
                    <div class="space-y-3">
                        <Label for="status" class="text-sm font-medium text-gray-700">
                            Statut <span class="text-red-500">*</span>
                        </Label>
                        <Select v-model="form.status" :class="{ 'border-red-300': form.errors.status }">
                            <SelectTrigger>
                                <SelectValue placeholder="Sélectionner un statut" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="active">Actif</SelectItem>
                                <SelectItem value="completed">Terminé</SelectItem>
                                <SelectItem value="on_hold">En pause</SelectItem>
                                <SelectItem value="cancelled">Annulé</SelectItem>
                            </SelectContent>
                        </Select>
                        <p v-if="form.errors.status" class="text-sm text-red-600">{{ form.errors.status }}</p>
                    </div>
                </div>

                <!-- Budget seul -->
                <div class="space-y-3 w-max">
                    <Label for="budget" class="text-sm font-medium text-gray-700">Budget</Label>
                    <div class="relative">
                        <Input
                            id="budget"
                            v-model="form.budget"
                            type="number"
                            step="0.01"
                            min="0"
                            placeholder="0.00"
                            class="pr-8"
                            :class="{ 'border-red-300': form.errors.budget }"
                        />
                        <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 text-sm">€</span>
                    </div>
                    <p v-if="form.errors.budget" class="text-sm text-red-600">{{ form.errors.budget }}</p>
                </div>

                <!-- Dates côte à côte -->
                <div class="grid gap-6 md:grid-cols-2">
                    <!-- Date de début -->
                    <div class="space-y-3">
                        <Label for="start_date" class="text-sm font-medium text-gray-700">Date de début</Label>
                        <Input
                            id="start_date"
                            v-model="form.start_date"
                            type="date"
                            :class="{ 'border-red-300': form.errors.start_date }"
                        />
                        <p v-if="form.errors.start_date" class="text-sm text-red-600">{{ form.errors.start_date }}</p>
                    </div>

                    <!-- Date de fin -->
                    <div class="space-y-3">
                        <Label for="end_date" class="text-sm font-medium text-gray-700">Date de fin</Label>
                        <Input
                            id="end_date"
                            v-model="form.end_date"
                            type="date"
                            :class="{ 'border-red-300': form.errors.end_date }"
                        />
                        <p v-if="form.errors.end_date" class="text-sm text-red-600">{{ form.errors.end_date }}</p>
                    </div>
                </div>

                <!-- Description -->
                <div class="space-y-3">
                    <Label for="description" class="text-sm font-medium text-gray-700">Description</Label>
                    <div class="relative">
                        <Icon name="file-text" class="absolute top-3 left-3 h-4 w-4 text-gray-400" />
                        <textarea
                            id="description"
                            v-model="form.description"
                            placeholder="Décrivez le projet..."
                            rows="4"
                            class="min-h-[10em] max-h-[20em] w-full rounded-md border border-gray-200 bg-white py-3 pr-3 pl-10 text-sm placeholder:text-gray-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-0"
                            :class="{ 'border-red-500 focus:border-red-500': form.errors.description }"
                        ></textarea>
                    </div>
                    <p v-if="form.errors.description" class="text-sm text-red-600">{{ form.errors.description }}</p>
                </div>

                <!-- Erreur générale -->
                <div v-if="form.errors.general" class="rounded-md bg-red-50 p-4">
                    <div class="flex">
                        <Icon name="alert-circle" class="h-5 w-5 text-red-400" />
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                Erreur lors de la modification
                            </h3>
                            <p class="mt-1 text-sm text-red-700">
                                {{ form.errors.general }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col-reverse items-center justify-between gap-4 border-t border-gray-100 pt-6 md:flex-row">
                    <div class="flex gap-2 w-full md:w-auto">
                        <button
                            type="button"
                            @click="goBack"
                            :disabled="form.processing"
                            class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 grow-1 flex items-center justify-center"
                        >
                            Annuler
                        </button>
                        <button
                            type="button"
                            @click="showDeleteModal = true"
                            :disabled="form.processing"
                            class="rounded-md bg-destructive px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-destructive/90 focus:outline-none focus:ring-offset-2 grow-1 flex items-center justify-center"
                        >
                            <Icon name="trash-2" class="mr-2 h-4 w-4" />
                            Supprimer
                        </button>
                    </div>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center rounded-md border border-transparent bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer w-full md:w-auto flex items-center justify-center"
                    >
                        <Icon v-if="form.processing" name="loader-2" class="mr-2 h-4 w-4 animate-spin" />
                        <Icon v-else name="save" class="mr-2 h-4 w-4" />
                        {{ form.processing ? 'Mise à jour...' : 'Mettre à jour' }}
                    </button>
                </div>
            </form>
        </CardContent>
    </Card>

    <!-- Modal de suppression -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-[#00000082]">
        <div class="mx-4 w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
            <div class="mb-4 flex items-center gap-3">
                <div class="rounded-full bg-red-100 p-2">
                    <Icon name="alert-triangle" class="h-5 w-5 text-red-600" />
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Supprimer le projet</h3>
            </div>
            <p class="mb-6 text-gray-600">
                Êtes-vous sûr de vouloir supprimer {{ project?.name }} ? Cette action est irréversible et supprimera également tous les événements associés.
            </p>
            <div class="flex gap-3">
                <button
                    type="button"
                    @click="showDeleteModal = false"
                    class="flex-1 inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                >
                    Annuler
                </button>
                <button
                    type="button"
                    @click="confirmDelete"
                    class="flex-1 inline-flex items-center justify-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                >
                    Supprimer
                </button>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { toRef } from 'vue'
import Icon from '@/components/Icon.vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { useProjectEditForm } from '@/composables/projects/edit/useProjectEditForm'
import type { ProjectEditFormData } from '@/types/projects/edit'

interface Props {
    project: ProjectEditFormData | null
    isLoading: boolean
    hasError: boolean
}

const props = defineProps<Props>()

// Composable pour la gestion du formulaire
const {
    form,
    showDeleteModal,
    submit,
    goBack,
    confirmDelete,
    resetForm,
} = useProjectEditForm(toRef(props, 'project'))

// Expose resetForm method to parent
defineExpose({
    resetForm
})
</script>
