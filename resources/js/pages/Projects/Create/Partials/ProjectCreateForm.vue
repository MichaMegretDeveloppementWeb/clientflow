<template>
    <div class="mx-auto max-w-[100em]">
        <div class="grid gap-8 form:grid-cols-12">
            <!-- Loading State -->
            <Card v-if="isLoading" class="border border-gray-200 bg-white shadow-sm form:col-span-8">
                <CardContent class="p-6">
                    <div class="space-y-8">
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
                            <div class="grid gap-6 sm:grid-cols-2">
                                <div class="space-y-3">
                                    <div class="h-4 w-32 bg-gray-200 rounded animate-pulse"></div>
                                    <div class="h-12 bg-gray-200 rounded animate-pulse"></div>
                                </div>
                                <div class="space-y-3">
                                    <div class="h-4 w-36 bg-gray-200 rounded animate-pulse"></div>
                                    <div class="h-12 bg-gray-200 rounded animate-pulse"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-col items-center justify-between gap-4 border-t border-gray-100 pt-6 sm:flex-row">
                            <div class="flex gap-2">
                                <div class="h-12 w-24 bg-gray-200 rounded animate-pulse"></div>
                            </div>
                            <div class="h-12 w-32 bg-gray-200 rounded animate-pulse"></div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Error State -->
            <Card v-else-if="hasError" class="border border-red-200 bg-red-50 shadow-sm form:col-span-8">
                <CardContent class="p-6">
                    <div class="flex items-center justify-center py-12">
                        <div class="text-center">
                            <Icon name="alert-triangle" class="mx-auto h-12 w-12 text-red-400 mb-4" />
                            <h3 class="text-lg font-medium text-red-900 mb-2">
                                Erreur de chargement
                            </h3>
                            <p class="text-sm text-red-700 mb-4">
                                Impossible de charger les données nécessaires à la création du projet.
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
            <Card v-else class="border border-gray-200 bg-white shadow-sm form:col-span-8">
                <CardContent class="p-6">
                    <form @submit.prevent="submit" class="space-y-8">
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
                                <!-- Sélecteur de client (si pas de client présélectionné) -->
                                <div v-if="!selectedClient" class="space-y-2">
                                    <Label for="client_id" class="text-sm font-medium text-gray-900">Client *</Label>
                                    <div class="relative">
                                        <Icon name="user" class="absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 transform text-blue-400 pointer-events-none" />
                                        <select
                                            id="client_id"
                                            v-model="form.client_id"
                                            required
                                            class="h-12 w-full appearance-none rounded-lg border border-gray-200 bg-white py-2 pr-10 pl-11 text-sm transition-all focus:border-purple-500 focus:ring-4 focus:ring-purple-500/10 hover:border-gray-300"
                                            :class="{ 'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500/10': form.errors.client_id }"
                                        >
                                            <option value="">Sélectionner un client</option>
                                            <option v-for="client in clients" :key="client.id" :value="client.id">
                                                {{ client.name }}{{ client.company ? ` - ${client.company}` : '' }}
                                            </option>
                                        </select>
                                        <Icon name="chevron-down" class="absolute top-1/2 right-3.5 h-4 w-4 -translate-y-1/2 transform text-gray-400 pointer-events-none" />
                                    </div>
                                    <InputError :message="form.errors.client_id" />
                                    <p class="text-xs text-gray-500">Client pour lequel ce projet sera réalisé</p>
                                </div>

                                <!-- Affichage du client présélectionné -->
                                <div v-else-if="currentClient" class="space-y-2">
                                    <Label class="text-sm font-medium text-gray-900">Client sélectionné</Label>
                                    <div class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50/50 p-4">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100">
                                            <Icon name="user" class="h-5 w-5 text-blue-600" />
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ currentClient.name }}</p>
                                            <p v-if="currentClient.company" class="text-sm text-gray-600">{{ currentClient.company }}</p>
                                        </div>
                                    </div>
                                    <input type="hidden" name="client_id" :value="form.client_id" />
                                    <p class="text-xs text-gray-500">Le client ne peut pas être modifié lors de la création depuis sa page</p>
                                </div>

                                <div class="grid gap-6 lg:grid-cols-2">
                                    <!-- Statut (déplacé ici) -->

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
                                    
                                    <!-- Espace vide pour maintenir la grille si client présélectionné -->
                                    <div v-if="selectedClient"></div>
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
                            <div class="order-2 sm:order-1">
                                <Button
                                    variant="outline"
                                    type="button"
                                    @click="handleCancel"
                                    class="h-12 px-6 text-sm font-medium transition-all hover:bg-gray-100 border-gray-300"
                                >
                                    <Icon name="x" class="mr-2 h-4 w-4" />
                                    Annuler
                                </Button>
                            </div>
                            <div class="order-1 sm:order-2">
                                <Button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="h-12 px-8 w-full sm:w-auto bg-purple-600 text-white font-medium transition-all hover:bg-purple-700 hover:shadow-lg hover:shadow-purple-600/25 focus:ring-4 focus:ring-purple-500/20 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:shadow-none"
                                >
                                    <Icon v-if="form.processing" name="loader-2" class="mr-2 h-4 w-4 animate-spin" />
                                    <Icon v-else name="check" class="mr-2 h-4 w-4" />
                                    {{ form.processing ? 'Création en cours...' : 'Créer le projet' }}
                                </Button>
                            </div>
                        </div>
                    </form>
                </CardContent>
            </Card>

            <!-- Sidebar avec guides -->
            <div class="space-y-6 form:col-span-4">
                <!-- Guide rapide -->
                <Card class="border-0 bg-gradient-to-br from-purple-50 via-purple-50 to-indigo-50 shadow-sm overflow-hidden">
                    <CardContent class="p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-purple-100">
                                <Icon name="lightbulb" class="h-4 w-4 text-purple-600" />
                            </div>
                            <h3 class="text-base font-semibold text-purple-900">Guide rapide</h3>
                        </div>
                        <div class="space-y-3 text-sm text-purple-800">
                            <div class="flex items-start gap-3">
                                <div class="flex h-5 w-5 items-center justify-center rounded-full bg-purple-200 mt-0.5">
                                    <div class="h-2 w-2 rounded-full bg-purple-600"></div>
                                </div>
                                <p>Sélectionnez d'abord le client pour organiser le projet</p>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="flex h-5 w-5 items-center justify-center rounded-full bg-purple-200 mt-0.5">
                                    <div class="h-2 w-2 rounded-full bg-purple-600"></div>
                                </div>
                                <p>Les dates de fin doivent être postérieures au début</p>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="flex h-5 w-5 items-center justify-center rounded-full bg-purple-200 mt-0.5">
                                    <div class="h-2 w-2 rounded-full bg-purple-600"></div>
                                </div>
                                <p>Une description détaillée aide au suivi du projet</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Statuts disponibles -->
                <Card class="border border-gray-100 shadow-sm">
                    <CardContent class="p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-100">
                                <Icon name="info" class="h-4 w-4 text-gray-600" />
                            </div>
                            <h3 class="text-base font-semibold text-gray-900">Statuts disponibles</h3>
                        </div>
                        <div class="space-y-3">
                            <div class="rounded-xl border border-green-200 bg-green-50/50 p-4">
                                <div class="mb-2 flex items-center gap-3">
                                    <div class="flex h-6 w-6 items-center justify-center rounded-full bg-green-100">
                                        <Icon name="play-circle" class="h-3 w-3 text-green-600" />
                                    </div>
                                    <span class="font-medium text-green-900">Actif</span>
                                </div>
                                <p class="text-sm text-green-800">Projet en cours de réalisation</p>
                            </div>
                            <div class="rounded-xl border border-blue-200 bg-blue-50/50 p-4">
                                <div class="mb-2 flex items-center gap-3">
                                    <div class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-100">
                                        <Icon name="check-circle" class="h-3 w-3 text-blue-600" />
                                    </div>
                                    <span class="font-medium text-blue-900">Terminé</span>
                                </div>
                                <p class="text-sm text-blue-800">Projet livré et finalisé</p>
                            </div>
                            <div class="rounded-xl border border-yellow-200 bg-yellow-50/50 p-4">
                                <div class="mb-2 flex items-center gap-3">
                                    <div class="flex h-6 w-6 items-center justify-center rounded-full bg-yellow-100">
                                        <Icon name="pause-circle" class="h-3 w-3 text-yellow-600" />
                                    </div>
                                    <span class="font-medium text-yellow-900">En pause</span>
                                </div>
                                <p class="text-sm text-yellow-800">Projet temporairement suspendu</p>
                            </div>
                            <div class="rounded-xl border border-red-200 bg-red-50/50 p-4">
                                <div class="mb-2 flex items-center gap-3">
                                    <div class="flex h-6 w-6 items-center justify-center rounded-full bg-red-100">
                                        <Icon name="x-circle" class="h-3 w-3 text-red-600" />
                                    </div>
                                    <span class="font-medium text-red-900">Annulé</span>
                                </div>
                                <p class="text-sm text-red-800">Projet abandonné ou annulé</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, watch } from 'vue'
import { Card, CardContent } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import Icon from '@/components/Icon.vue'
import InputError from '@/components/InputError.vue'
import { useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

interface Client {
    id: number
    name: string
    company?: string
}

interface Props {
    isLoading: boolean
    hasError: boolean
    clients?: Client[]
    selectedClient?: Client | null
}

const props = withDefaults(defineProps<Props>(), {
    clients: () => [],
    selectedClient: null
})

// Formulaire simple sans le composable complexe
const form = useForm({
    client_id: props.selectedClient ? props.selectedClient.id.toString() : '',
    name: '',
    description: '',
    status: 'active',
    start_date: '',
    end_date: '',
    budget: '',
})

// Watcher pour mettre à jour le client_id quand selectedClient change
watch(() => props.selectedClient, (newClient) => {
    if (newClient && !form.client_id) {
        form.client_id = newClient.id.toString()
    }
}, { immediate: true })

// Client actuel (présélectionné ou null)
const currentClient = computed(() => {
    if (props.selectedClient) {
        return props.selectedClient
    }
    if (form.client_id && props.clients.length > 0) {
        return props.clients.find(client => client.id.toString() === form.client_id) || null
    }
    return null
})

const submit = () => {
    form.post(route('projects.store'))
}

const handleCancel = () => {
    window.history.back()
}

const reloadPage = () => {
    window.location.reload()
}
</script>
