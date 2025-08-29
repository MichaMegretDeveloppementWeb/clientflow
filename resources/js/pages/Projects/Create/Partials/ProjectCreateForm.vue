<template>
    <div class="mx-auto max-w-6xl">
        <!-- Loading State -->
        <div v-if="isLoading" class="grid gap-8 md:grid-cols-12">
            <div class="md:col-span-8">
                <Card class="border border-gray-200 bg-white shadow-sm">
                    <CardContent class="p-6">
                        <div class="space-y-6">
                            <div v-for="i in 6" :key="i" class="space-y-3">
                                <div class="h-4 bg-gray-200 rounded animate-pulse w-20"></div>
                                <div class="h-10 bg-gray-200 rounded animate-pulse"></div>
                            </div>
                            <div class="flex justify-end space-x-3">
                                <div class="h-10 bg-gray-200 rounded animate-pulse w-20"></div>
                                <div class="h-10 bg-gray-200 rounded animate-pulse w-32"></div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
            <div class="md:col-span-4 space-y-6">
                <div class="h-40 bg-gray-200 rounded animate-pulse"></div>
                <div class="h-32 bg-gray-200 rounded animate-pulse"></div>
            </div>
        </div>

        <!-- Error State -->
        <div v-else-if="hasError" class="rounded-md bg-red-50 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <Icon name="alert-circle" class="h-5 w-5 text-red-400" />
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        Impossible de charger le formulaire
                    </h3>
                    <div class="mt-2 text-sm text-red-700">
                        <p>Une erreur est survenue. Veuillez actualiser la page.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Form -->
        <div v-else class="grid gap-8 md:grid-cols-12">
            <!-- Formulaire principal -->
            <Card class="border border-gray-200 bg-white shadow-sm md:col-span-8">
                <CardContent class="p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- General errors -->
                        <div v-if="form.errors.general" class="rounded-md bg-red-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <Icon name="alert-circle" class="h-5 w-5 text-red-400" />
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm text-red-700">
                                        {{ form.errors.general }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section Informations de base -->
                        <div class="space-y-6">
                            <div class="border-b border-gray-100 pb-4">
                                <h3 class="mb-1 text-lg font-semibold text-gray-900">Informations de base</h3>
                                <p class="text-sm text-gray-600">Les informations essentielles de votre projet</p>
                            </div>

                            <div class="grid gap-6 sm:grid-cols-2">
                                <!-- Client -->
                                <div class="space-y-3">
                                    <Label for="client_id" class="text-sm font-medium text-gray-700">
                                        Client <span class="text-red-500">*</span>
                                    </Label>
                                    <div class="relative">
                                        <Icon name="user" class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 transform text-gray-400" />
                                        <select
                                            id="client_id"
                                            v-model="form.client_id"
                                            required
                                            class="h-11 w-full rounded-md border border-gray-200 bg-white px-3 py-2 pl-10 text-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500 focus:ring-offset-0"
                                            :class="{ 'border-red-300': form.errors.client_id }"
                                        >
                                            <option value="">Sélectionner un client</option>
                                            <option v-for="client in clients" :key="client.id" :value="client.id">
                                                {{ client.name }}{{ client.company ? ` - ${client.company}` : '' }}
                                            </option>
                                        </select>
                                    </div>
                                    <p v-if="form.errors.client_id" class="text-sm text-red-600">{{ form.errors.client_id }}</p>
                                </div>

                                <!-- Statut -->
                                <div class="space-y-3">
                                    <Label for="status" class="text-sm font-medium text-gray-700">
                                        Statut <span class="text-red-500">*</span>
                                    </Label>
                                    <div class="relative">
                                        <Icon name="settings" class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 transform text-gray-400" />
                                        <select
                                            id="status"
                                            v-model="form.status"
                                            required
                                            class="h-11 w-full rounded-md border border-gray-200 bg-white px-3 py-2 pl-10 text-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500 focus:ring-offset-0"
                                            :class="{ 'border-red-300': form.errors.status }"
                                        >
                                            <option value="active">Actif</option>
                                            <option value="completed">Terminé</option>
                                            <option value="on_hold">En pause</option>
                                            <option value="cancelled">Annulé</option>
                                        </select>
                                    </div>
                                    <p v-if="form.errors.status" class="text-sm text-red-600">{{ form.errors.status }}</p>
                                </div>
                            </div>

                            <!-- Nom du projet -->
                            <div class="space-y-3">
                                <Label for="name" class="text-sm font-medium text-gray-700">
                                    Nom du projet <span class="text-red-500">*</span>
                                </Label>
                                <div class="relative">
                                    <Icon name="folder" class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 transform text-gray-400" />
                                    <Input
                                        id="name"
                                        v-model="form.name"
                                        type="text"
                                        required
                                        placeholder="ex: Site web corporate"
                                        class="h-11 border-gray-200 pl-10 focus:border-purple-500 focus:ring-purple-500"
                                        :class="{ 'border-red-300': form.errors.name }"
                                    />
                                </div>
                                <p v-if="form.errors.name" class="text-sm text-red-600">{{ form.errors.name }}</p>
                            </div>
                        </div>

                        <!-- Section Détails -->
                        <div class="space-y-6">
                            <div class="border-b border-gray-100 pb-4">
                                <h3 class="mb-1 text-lg font-semibold text-gray-900">Détails du projet</h3>
                                <p class="text-sm text-gray-600">Informations complémentaires et planning</p>
                            </div>

                            <!-- Description -->
                            <div class="space-y-3">
                                <Label for="description" class="text-sm font-medium text-gray-700">Description</Label>
                                <div class="relative">
                                    <Icon name="file-text" class="absolute top-3 left-3 h-4 w-4 text-gray-400" />
                                    <textarea
                                        id="description"
                                        v-model="form.description"
                                        rows="4"
                                        placeholder="Décrivez les objectifs et la portée du projet..."
                                        class="min-h-[100px] w-full rounded-md border border-gray-200 bg-white px-3 py-3 pl-10 text-sm placeholder:text-gray-400 focus:border-purple-500 focus:ring-2 focus:ring-purple-500 focus:ring-offset-0"
                                        :class="{ 'border-red-300': form.errors.description }"
                                    />
                                </div>
                                <p v-if="form.errors.description" class="text-sm text-red-600">{{ form.errors.description }}</p>
                            </div>

                            <!-- Dates et Budget -->
                            <div class="grid gap-6 sm:grid-cols-3">
                                <!-- Date de début -->
                                <div class="space-y-3">
                                    <Label for="start_date" class="text-sm font-medium text-gray-700">
                                        Date de début <span class="text-red-500">*</span>
                                    </Label>
                                    <div class="relative">
                                        <Icon name="calendar" class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 transform text-gray-400" />
                                        <Input
                                            id="start_date"
                                            v-model="form.start_date"
                                            type="date"
                                            required
                                            class="h-11 border-gray-200 pl-10 focus:border-purple-500 focus:ring-purple-500"
                                            :class="{ 'border-red-300': form.errors.start_date || startDateError }"
                                            @blur="markStartDateAsTouched"
                                        />
                                    </div>
                                    <p v-if="form.errors.start_date" class="text-sm text-red-600">{{ form.errors.start_date }}</p>
                                    <p v-if="startDateError" class="text-sm text-red-600">{{ startDateError }}</p>
                                </div>

                                <!-- Date de fin -->
                                <div class="space-y-3">
                                    <Label for="end_date" class="text-sm font-medium text-gray-700">Date de fin</Label>
                                    <div class="relative">
                                        <Icon name="calendar" class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 transform text-gray-400" />
                                        <Input
                                            id="end_date"
                                            v-model="form.end_date"
                                            type="date"
                                            :min="form.start_date"
                                            class="h-11 border-gray-200 pl-10 focus:border-purple-500 focus:ring-purple-500"
                                            :class="{ 
                                                'border-red-300': form.errors.end_date || endDateError
                                            }"
                                            @blur="markEndDateAsTouched"
                                        />
                                    </div>
                                    <p v-if="form.errors.end_date" class="text-sm text-red-600">{{ form.errors.end_date }}</p>
                                    <p v-if="endDateError" class="text-sm text-red-600">{{ endDateError }}</p>
                                </div>

                                <!-- Budget -->
                                <div class="space-y-3">
                                    <Label for="budget" class="text-sm font-medium text-gray-700">Budget (€)</Label>
                                    <div class="relative">
                                        <Icon name="banknote" class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 transform text-gray-400" />
                                        <Input
                                            id="budget"
                                            v-model="form.budget"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            placeholder="0.00"
                                            class="h-11 border-gray-200 pl-10 focus:border-purple-500 focus:ring-purple-500"
                                            :class="{ 'border-red-300': form.errors.budget }"
                                        />
                                    </div>
                                    <p v-if="form.errors.budget" class="text-sm text-red-600">{{ form.errors.budget }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-col-reverse items-center justify-between gap-4 border-t border-gray-100 pt-6 sm:flex-row">
                            <Button variant="outline" type="button" @click="goBack" class="w-full sm:w-auto">
                                <Icon name="x" class="mr-2 h-4 w-4" />
                                Annuler
                            </Button>
                            <Button
                                type="submit"
                                :disabled="form.processing || !isDateRangeValid"
                                class="w-full bg-purple-600 text-white hover:bg-purple-700 sm:w-auto disabled:opacity-50"
                            >
                                <Icon v-if="form.processing" name="loader-2" class="mr-2 h-4 w-4 animate-spin" />
                                <Icon v-else name="check" class="mr-2 h-4 w-4" />
                                Créer le projet
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>

            <!-- Sidebar avec conseils -->
            <div class="space-y-6 md:col-span-4">
                <Card class="border-0 bg-gradient-to-br from-purple-50 to-purple-100 shadow-sm">
                    <CardHeader class="pb-4">
                        <CardTitle class="flex items-center gap-2 text-purple-900">
                            <Icon name="lightbulb" class="h-5 w-5" />
                            Conseils
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4 text-sm text-purple-800">
                        <div class="flex items-start gap-3">
                            <Icon name="check-circle" class="mt-0.5 h-4 w-4 text-purple-600" />
                            <p>Choisissez un nom de projet clair et descriptif pour faciliter l'identification.</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <Icon name="check-circle" class="mt-0.5 h-4 w-4 text-purple-600" />
                            <p>Le statut "En pause" est parfait pour les projets en cours de négociation.</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <Icon name="check-circle" class="mt-0.5 h-4 w-4 text-purple-600" />
                            <p>Définissez un budget initial même approximatif pour un meilleur suivi.</p>
                        </div>
                    </CardContent>
                </Card>

                <Card class="border-0 bg-white shadow-sm">
                    <CardHeader class="pb-4">
                        <CardTitle class="flex items-center gap-2 text-gray-900">
                            <Icon name="info" class="h-5 w-5" />
                            Prochaines étapes
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-3 text-sm">
                        <div class="flex items-center gap-3 rounded-lg bg-gray-50 p-3">
                            <div class="flex h-6 w-6 items-center justify-center rounded-full bg-purple-100 text-xs font-medium text-purple-600">
                                1
                            </div>
                            <span>Créer le projet</span>
                        </div>
                        <div class="flex items-center gap-3 rounded-lg bg-gray-50 p-3 opacity-60">
                            <div class="flex h-6 w-6 items-center justify-center rounded-full bg-gray-200 text-xs font-medium text-gray-500">
                                2
                            </div>
                            <span>Ajouter des événements</span>
                        </div>
                        <div class="flex items-center gap-3 rounded-lg bg-gray-50 p-3 opacity-60">
                            <div class="flex h-6 w-6 items-center justify-center rounded-full bg-gray-200 text-xs font-medium text-gray-500">
                                3
                            </div>
                            <span>Suivre l'avancement</span>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { toRef } from 'vue'
import Icon from '@/components/Icon.vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { useProjectCreateForm } from '@/composables/projects/create/useProjectCreateForm'
import type { ProjectCreateClient } from '@/types/projects/create'

interface Props {
    clients: ProjectCreateClient[]
    selectedClientId: number | null
    isLoading: boolean
    hasError: boolean
}

const props = defineProps<Props>()

// Composable pour la gestion du formulaire
const {
    form,
    isDateRangeValid,
    startDateError,
    endDateError,
    submit,
    goBack,
    resetForm,
    markStartDateAsTouched,
    markEndDateAsTouched,
} = useProjectCreateForm(toRef(props, 'clients'), toRef(props, 'selectedClientId'))

// Expose resetForm method to parent
defineExpose({
    resetForm
})
</script>