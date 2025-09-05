<template>
    <div class="mx-auto max-w-[100em]">
        <div class="grid gap-8 form:grid-cols-12">
            <!-- Message d'erreur -->
            <Card v-if="hasError" class="border border-red-200 bg-red-50 shadow-sm form:col-span-8">
                <CardContent class="p-6">
                    <div class="flex items-center justify-center py-12">
                        <div class="text-center">
                            <Icon name="alert-triangle" class="mx-auto h-12 w-12 text-red-400 mb-4" />
                            <h3 class="text-lg font-medium text-red-900 mb-2">
                                Erreur de chargement
                            </h3>
                            <p class="text-sm text-red-700 mb-4">
                                Impossible de charger les données nécessaires à la création du client.
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
                                    <div class="h-11 bg-gray-200 rounded animate-pulse"></div>
                                </div>
                                <div class="space-y-3">
                                    <div class="h-4 w-24 bg-gray-200 rounded animate-pulse"></div>
                                    <div class="h-11 bg-gray-200 rounded animate-pulse"></div>
                                </div>
                            </div>
                            <div class="grid gap-6 sm:grid-cols-2">
                                <div class="space-y-3">
                                    <div class="h-4 w-20 bg-gray-200 rounded animate-pulse"></div>
                                    <div class="h-11 bg-gray-200 rounded animate-pulse"></div>
                                </div>
                                <div class="space-y-3">
                                    <div class="h-4 w-28 bg-gray-200 rounded animate-pulse"></div>
                                    <div class="h-11 bg-gray-200 rounded animate-pulse"></div>
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
                                <div class="h-10 w-24 bg-gray-200 rounded animate-pulse"></div>
                            </div>
                            <div class="h-10 w-32 bg-gray-200 rounded animate-pulse"></div>
                        </div>
                    </div>

                    <!-- Formulaire réel -->
                    <div v-else-if="form">
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
                                <div class="order-2 sm:order-1">
                                    <Button
                                        variant="outline"
                                        type="button"
                                        @click="cancel?.()"
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
                                        class="h-12 px-8 w-full sm:w-auto bg-blue-600 text-white font-medium transition-all hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-600/25 focus:ring-4 focus:ring-blue-500/20 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:shadow-none"
                                    >
                                        <Icon v-if="form.processing" name="loader-2" class="mr-2 h-4 w-4 animate-spin" />
                                        <Icon v-else name="check" class="mr-2 h-4 w-4" />
                                        {{ form.processing ? 'Création en cours...' : 'Créer le client' }}
                                    </Button>
                                </div>
                            </div>
                        </form>
                    </div>
                </CardContent>
            </Card>

            <!-- Sidebar -->
            <div class="form:col-span-4">
                <ClientCreateSidebar
                    :is-loading="isLoading"
                    :has-error="hasError"
                />
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { watch, ref } from 'vue'
import Icon from '@/components/Icon.vue'
import { Card, CardContent } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import InputError from '@/components/InputError.vue'
import ClientCreateSidebar from './ClientCreateSidebar.vue'
import { useClientCreateForm } from '@/composables/clients/create/useClientCreateForm'
import type { ClientCreateFormProps } from '@/types/clients/create'

const props = defineProps<ClientCreateFormProps>()

// Variables réactives pour le formulaire
const form = ref<any>(null)
const initializeForm = ref<((clientData: any) => void) | null>(null)
const submit = ref<(() => void) | null>(null)
const cancel = ref<(() => void) | null>(null)

// Créer le composable directement
const composable = useClientCreateForm()

// Assigner les références
form.value = composable.form
initializeForm.value = composable.initializeForm
submit.value = composable.submit
cancel.value = composable.cancel

// Initialiser le formulaire quand les données sont disponibles
watch(() => props.clientData, (newData) => {
    if (newData && initializeForm.value) {
        initializeForm.value(newData)
    }
}, { immediate: true })

// Fonction pour recharger la page
const reloadPage = () => {
    globalThis.location.reload()
}
</script>
