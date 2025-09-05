<template>
    <div class="mx-auto max-w-[100em]">
        <!-- Message d'erreur -->
        <Card v-if="hasError" class="border border-red-200 bg-red-50 shadow-sm">
            <CardContent class="p-6">
                <div class="flex items-center justify-center py-12">
                    <div class="text-center">
                        <Icon name="alert-triangle" class="mx-auto h-12 w-12 text-red-400 mb-4" />
                        <h3 class="text-lg font-medium text-red-900 mb-2">
                            Erreur de chargement
                        </h3>
                        <div class="text-sm text-red-700 mb-4 flex flex-col items-start">
                            <p v-for="(message, key) in getErrorMessages()" :key="key" class="mb-1">
                                - {{ message }}
                            </p>
                        </div>
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
                        <div class="grid gap-6 sm:grid-cols-2">
                            <div class="space-y-3">
                                <div class="h-4 w-20 bg-gray-200 rounded animate-pulse"></div>
                                <div class="h-12 bg-gray-200 rounded animate-pulse"></div>
                            </div>
                            <div class="space-y-3">
                                <div class="h-4 w-28 bg-gray-200 rounded animate-pulse"></div>
                                <div class="h-12 bg-gray-200 rounded animate-pulse"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Section Informations complémentaires -->
                    <div class="space-y-6">
                        <div class="border-b border-gray-100 pb-4">
                            <div class="h-6 w-56 bg-gray-200 rounded animate-pulse mb-2"></div>
                            <div class="h-4 w-72 bg-gray-200 rounded animate-pulse"></div>
                        </div>
                        <div class="space-y-3">
                            <div class="h-4 w-16 bg-gray-200 rounded animate-pulse"></div>
                            <div class="h-20 bg-gray-200 rounded animate-pulse"></div>
                        </div>
                        <div class="space-y-3">
                            <div class="h-4 w-12 bg-gray-200 rounded animate-pulse"></div>
                            <div class="h-24 bg-gray-200 rounded animate-pulse"></div>
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
                <div v-else-if="client && form">
                    <form @submit.prevent="submit?.()" class="space-y-8">
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
                                <p class="text-sm text-gray-600">Les informations essentielles de votre client</p>
                            </div>

                            <div class="space-y-6">
                                <div class="grid gap-6 lg:grid-cols-2">
                                    <!-- Nom (obligatoire) -->
                                    <div class="space-y-2">
                                        <Label for="name" class="text-sm font-medium text-gray-900">Nom du client *</Label>
                                        <div class="relative">
                                            <Icon name="user" class="absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 transform text-blue-400" />
                                            <Input
                                                id="name"
                                                v-model="form.name"
                                                @input="form.clearErrors('name')"
                                                type="text"
                                                required
                                                placeholder="ex: Jean Dupont"
                                                class="h-12 border-gray-200 pl-11 pr-4 transition-all focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 hover:border-gray-300 rounded-lg"
                                                :class="{ 'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500/10': form.errors.name }"
                                            />
                                        </div>
                                        <InputError :message="form.errors.name" />
                                        <p class="text-xs text-gray-500">Nom complet de la personne de contact</p>
                                    </div>

                                    <!-- Entreprise -->
                                    <div class="space-y-2">
                                        <Label for="company" class="text-sm font-medium text-gray-900">Entreprise</Label>
                                        <div class="relative">
                                            <Icon name="building" class="absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 transform text-indigo-400" />
                                            <Input
                                                id="company"
                                                v-model="form.company"
                                                @input="form.clearErrors('company')"
                                                type="text"
                                                placeholder="ex: ACME Corporation"
                                                class="h-12 border-gray-200 pl-11 pr-4 transition-all focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 hover:border-gray-300 rounded-lg"
                                                :class="{ 'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500/10': form.errors.company }"
                                            />
                                        </div>
                                        <InputError :message="form.errors.company" />
                                        <p class="text-xs text-gray-500">Nom de l'entreprise ou organisation</p>
                                    </div>
                                </div>

                                <div class="grid gap-6 lg:grid-cols-2">
                                    <!-- Email -->
                                    <div class="space-y-2">
                                        <Label for="email" class="text-sm font-medium text-gray-900">Email</Label>
                                        <div class="relative">
                                            <Icon name="mail" class="absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 transform text-green-400" />
                                            <Input
                                                id="email"
                                                v-model="form.email"
                                                @input="form.clearErrors('email')"
                                                type="email"
                                                placeholder="ex: jean@acme.com"
                                                class="h-12 border-gray-200 pl-11 pr-4 transition-all focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 hover:border-gray-300 rounded-lg"
                                                :class="{ 'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500/10': form.errors.email }"
                                            />
                                        </div>
                                        <InputError :message="form.errors.email" />
                                        <p class="text-xs text-gray-500">Adresse email de contact principal</p>
                                    </div>

                                    <!-- Téléphone -->
                                    <div class="space-y-2">
                                        <Label for="phone" class="text-sm font-medium text-gray-900">Téléphone</Label>
                                        <div class="relative">
                                            <Icon name="phone" class="absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 transform text-purple-400" />
                                            <Input
                                                id="phone"
                                                v-model="form.phone"
                                                @input="form.clearErrors('phone')"
                                                type="tel"
                                                placeholder="ex: +33 1 23 45 67 89"
                                                class="h-12 border-gray-200 pl-11 pr-4 transition-all focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 hover:border-gray-300 rounded-lg"
                                                :class="{ 'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500/10': form.errors.phone }"
                                            />
                                        </div>
                                        <InputError :message="form.errors.phone" />
                                        <p class="text-xs text-gray-500">Numéro de téléphone principal</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section Informations complémentaires -->
                        <div class="space-y-8">
                            <div class="space-y-1">
                                <h3 class="text-xl font-semibold text-gray-900 tracking-tight">Informations complémentaires</h3>
                                <p class="text-sm text-gray-600">Adresse et notes additionnelles sur votre client</p>
                            </div>

                            <div class="space-y-6">
                                <!-- Adresse -->
                                <div class="space-y-2">
                                    <Label for="address" class="text-sm font-medium text-gray-900">Adresse</Label>
                                    <div class="relative">
                                        <Icon name="map-pin" class="absolute top-3.5 left-3.5 h-4 w-4 text-orange-400" />
                                        <textarea
                                            id="address"
                                            v-model="form.address"
                                            @input="form.clearErrors('address')"
                                            rows="3"
                                            placeholder="123 Rue de la République&#10;75001 Paris, France"
                                            class="min-h-[90px] w-full resize-none rounded-lg border border-gray-200 bg-white py-3.5 pr-4 pl-11 text-sm placeholder:text-gray-400 transition-all focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 hover:border-gray-300"
                                            :class="{ 'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500/10': form.errors.address }"
                                        ></textarea>
                                    </div>
                                    <InputError :message="form.errors.address" />
                                    <p class="text-xs text-gray-500">Adresse postale complète du client</p>
                                </div>

                                <!-- Notes -->
                                <div class="space-y-2">
                                    <Label for="notes" class="text-sm font-medium text-gray-900">Notes</Label>
                                    <div class="relative">
                                        <Icon name="file-text" class="absolute top-3.5 left-3.5 h-4 w-4 text-slate-400" />
                                        <textarea
                                            id="notes"
                                            v-model="form.notes"
                                            @input="form.clearErrors('notes')"
                                            rows="4"
                                            placeholder="Notes importantes sur ce client, préférences, historique..."
                                            class="min-h-[110px] w-full resize-none rounded-lg border border-gray-200 bg-white py-3.5 pr-4 pl-11 text-sm placeholder:text-gray-400 transition-all focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 hover:border-gray-300"
                                            :class="{ 'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-500/10': form.errors.notes }"
                                        ></textarea>
                                    </div>
                                    <InputError :message="form.errors.notes" />
                                    <p class="text-xs text-gray-500">Informations utiles pour le suivi du client</p>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-col gap-4 border-t border-gray-200 bg-gray-50/50 rounded-b-xl pt-8 pb-2 -mx-6 px-6 sm:flex-row sm:items-center sm:justify-between">
                            <div class="order-2 sm:order-1 flex flex-wrap gap-3">
                                <Button
                                    variant="outline"
                                    type="button"
                                    @click="cancel"
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
                                    class="h-12 px-8 w-full sm:w-auto bg-blue-600 text-white font-medium transition-all hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-600/25 focus:ring-4 focus:ring-blue-500/20 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:shadow-none"
                                >
                                    <Icon v-if="form.processing" name="loader-2" class="mr-2 h-4 w-4 animate-spin" />
                                    <Icon v-else name="save" class="mr-2 h-4 w-4" />
                                    {{ form.processing ? 'Mise à jour en cours...' : 'Mettre à jour' }}
                                </Button>
                            </div>
                        </div>
                    </form>
                </div>
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
                                            <h3 class="text-lg font-semibold text-gray-900">Supprimer le client</h3>
                                            <p class="text-sm text-gray-600">Cette action est irréversible</p>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-6">
                                        <p class="text-sm text-gray-700">
                                            Êtes-vous sûr de vouloir supprimer le client 
                                            <span class="font-semibold text-gray-900">{{ client?.name }}</span> ?
                                        </p>
                                        <p class="text-sm text-red-600 mt-2">
                                            Tous les projets et événements associés seront également supprimés.
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
import type { ClientEditData } from '@/types/clients/edit'

interface Props {
    clientId: number
    isLoading: boolean
    hasError: boolean
    client: ClientEditData | null
    errors: Record<string, any>
}

const props = defineProps<Props>()

// Formulaire Inertia simple
const form = useForm({
    name: '',
    company: '',
    email: '',
    phone: '',
    address: '',
    notes: ''
})

// Variables pour stocker les valeurs originales et gérer la modal
const originalData = ref<ClientEditData | null>(null)
const showDeleteModal = ref(false)

// Initialiser les données du formulaire
watch(
    () => props.client,
    (newClient) => {
        if (newClient && !props.isLoading && !props.hasError) {
            // Stocker les valeurs originales pour la réinitialisation
            originalData.value = { ...newClient }
            
            // Remplir le formulaire
            form.name = newClient.name || ''
            form.company = newClient.company || ''
            form.email = newClient.email || ''
            form.phone = newClient.phone || ''
            form.address = newClient.address || ''
            form.notes = newClient.notes || ''
        }
    },
    { immediate: true }
)

// Actions
const submit = () => {
    if (!props.client) return
    form.put(route('clients.update', props.client.id))
}

const cancel = () => {
    window.history.back()
}

const resetForm = () => {
    if (!originalData.value) return
    
    // Restaurer les valeurs originales
    form.name = originalData.value.name || ''
    form.company = originalData.value.company || ''
    form.email = originalData.value.email || ''
    form.phone = originalData.value.phone || ''
    form.address = originalData.value.address || ''
    form.notes = originalData.value.notes || ''
    
    // Effacer les erreurs
    form.clearErrors()
}

const reloadPage = () => {
    window.location.reload()
}

const getErrorMessages = () => {
    return props.errors ? Object.values(props.errors).filter(Boolean) : []
}

// Fonction pour supprimer le client
const confirmDelete = () => {
    if (!props.client) return
    
    router.delete(route('clients.destroy', props.client.id), {
        onSuccess: () => {
            showDeleteModal.value = false
        }
    })
}
</script>