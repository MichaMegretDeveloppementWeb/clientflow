<template>
    <div class="mx-auto max-w-7xl">
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
                        <form @submit.prevent="submit?.()" class="space-y-6">
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
                            <div class="space-y-6 my-12 mt-0">
                                <div class="border-b border-gray-100 pb-4">
                                    <h3 class="mb-1 text-lg font-semibold text-gray-900">Informations de base</h3>
                                    <p class="text-sm text-gray-600">Les informations essentielles de votre client</p>
                                </div>

                                <div class="grid gap-6 sm:grid-cols-2">
                                    <!-- Nom (obligatoire) -->
                                    <div class="space-y-3">
                                        <Label for="name" class="text-sm font-medium text-gray-700">Nom du client *</Label>
                                        <div class="relative">
                                            <Icon name="user" class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 transform text-gray-400" />
                                            <Input
                                                id="name"
                                                v-model="form.name"
                                                @input="form.clearErrors('name')"
                                                type="text"
                                                required
                                                placeholder="Nom complet du client"
                                                class="h-11 border-gray-200 pl-10 focus:border-emerald-500 focus:ring-emerald-500"
                                                :class="{ 'border-red-500 focus:border-red-500': form.errors.name }"
                                            />
                                        </div>
                                        <InputError :message="form.errors.name" />
                                    </div>

                                    <!-- Entreprise -->
                                    <div class="space-y-3">
                                        <Label for="company" class="text-sm font-medium text-gray-700">Entreprise</Label>
                                        <div class="relative">
                                            <Icon name="building" class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 transform text-gray-400" />
                                            <Input
                                                id="company"
                                                v-model="form.company"
                                                @input="form.clearErrors('company')"
                                                type="text"
                                                placeholder="Nom de l'entreprise"
                                                class="h-11 border-gray-200 pl-10 focus:border-emerald-500 focus:ring-emerald-500"
                                                :class="{ 'border-red-500 focus:border-red-500': form.errors.company }"
                                            />
                                        </div>
                                        <InputError :message="form.errors.company" />
                                    </div>
                                </div>

                                <div class="grid gap-6 sm:grid-cols-2">
                                    <!-- Email -->
                                    <div class="space-y-3">
                                        <Label for="email" class="text-sm font-medium text-gray-700">Email</Label>
                                        <div class="relative">
                                            <Icon name="mail" class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 transform text-gray-400" />
                                            <Input
                                                id="email"
                                                v-model="form.email"
                                                @input="form.clearErrors('email')"
                                                type="email"
                                                placeholder="adresse@email.com"
                                                class="h-11 border-gray-200 pl-10 focus:border-emerald-500 focus:ring-emerald-500"
                                                :class="{ 'border-red-500 focus:border-red-500': form.errors.email }"
                                            />
                                        </div>
                                        <InputError :message="form.errors.email" />
                                    </div>

                                    <!-- Téléphone -->
                                    <div class="space-y-3">
                                        <Label for="phone" class="text-sm font-medium text-gray-700">Téléphone</Label>
                                        <div class="relative">
                                            <Icon name="phone" class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 transform text-gray-400" />
                                            <Input
                                                id="phone"
                                                v-model="form.phone"
                                                @input="form.clearErrors('phone')"
                                                type="tel"
                                                placeholder="+33 1 23 45 67 89"
                                                class="h-11 border-gray-200 pl-10 focus:border-emerald-500 focus:ring-emerald-500"
                                                :class="{ 'border-red-500 focus:border-red-500': form.errors.phone }"
                                            />
                                        </div>
                                        <InputError :message="form.errors.phone" />
                                    </div>
                                </div>
                            </div>

                            <!-- Section Informations complémentaires -->
                            <div class="space-y-6 my-12">
                                <div class="border-b border-gray-100 pb-4">
                                    <h3 class="mb-1 text-lg font-semibold text-gray-900">Informations complémentaires</h3>
                                    <p class="text-sm text-gray-600">Adresse et notes additionnelles sur votre client</p>
                                </div>

                                <!-- Adresse -->
                                <div class="space-y-3">
                                    <Label for="address" class="text-sm font-medium text-gray-700">Adresse</Label>
                                    <div class="relative">
                                        <Icon name="map-pin" class="absolute top-3 left-3 h-4 w-4 text-gray-400" />
                                        <textarea
                                            id="address"
                                            v-model="form.address"
                                            @input="form.clearErrors('address')"
                                            rows="3"
                                            placeholder="Adresse complète du client..."
                                            class="min-h-[80px] w-full resize-none rounded-md border border-gray-200 bg-white py-3 pr-3 pl-10 text-sm placeholder:text-gray-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-0"
                                            :class="{ 'border-red-500 focus:border-red-500': form.errors.address }"
                                        ></textarea>
                                    </div>
                                    <InputError :message="form.errors.address" />
                                </div>

                                <!-- Notes -->
                                <div class="space-y-3">
                                    <Label for="notes" class="text-sm font-medium text-gray-700">Notes</Label>
                                    <div class="relative">
                                        <Icon name="file-text" class="absolute top-3 left-3 h-4 w-4 text-gray-400" />
                                        <textarea
                                            id="notes"
                                            v-model="form.notes"
                                            @input="form.clearErrors('notes')"
                                            rows="4"
                                            placeholder="Notes additionnelles sur le client..."
                                            class="min-h-[100px] w-full resize-none rounded-md border border-gray-200 bg-white py-3 pr-3 pl-10 text-sm placeholder:text-gray-400 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500 focus:ring-offset-0"
                                            :class="{ 'border-red-500 focus:border-red-500': form.errors.notes }"
                                        ></textarea>
                                    </div>
                                    <InputError :message="form.errors.notes" />
                                </div>
                            </div>

                            <!-- Erreur générale -->
                            <div v-if="form.errors.general" class="rounded-md bg-red-50 p-4">
                                <div class="flex">
                                    <Icon name="alert-circle" class="h-5 w-5 text-red-400" />
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800">
                                            Erreur lors de la création
                                        </h3>
                                        <p class="mt-1 text-sm text-red-700">
                                            {{ form.errors.general }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex flex-col items-center justify-between gap-4 border-t border-gray-100 pt-6 sm:flex-row">
                                <div class="flex gap-2">
                                    <button
                                        type="button"
                                        @click="cancel?.()"
                                        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                                    >
                                        Annuler
                                    </button>
                                </div>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="inline-flex items-center rounded-md border border-transparent bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <Icon v-if="form.processing" name="loader-2" class="mr-2 h-4 w-4 animate-spin" />
                                    <Icon v-else name="check" class="mr-2 h-4 w-4" />
                                    {{ form.processing ? 'Création...' : 'Créer le client' }}
                                </button>
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
