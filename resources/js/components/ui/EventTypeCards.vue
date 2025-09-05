<template>
    <div class="space-y-4">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Type d'événement</h3>
            <p class="text-sm text-gray-600">Choisissez le type d'événement que vous souhaitez créer</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <!-- Carte Étape -->
            <div
                @click="selectType('step')"
                class="relative cursor-pointer rounded-xl border-1 p-6 transition-all duration-200 hover:shadow-md"
                :class="selectedValue === 'step'
                    ? 'border-blue-500 bg-blue-50 shadow-sm'
                    : 'border-gray-200 bg-white hover:border-gray-300'"
            >
                <!-- Indicateur de sélection -->
                <div class="absolute top-4 right-4">
                    <div
                        class="h-5 w-5 rounded-full border-2 transition-all duration-200"
                        :class="selectedValue === 'step'
                            ? 'border-blue-500 bg-blue-500'
                            : 'border-gray-300 bg-white'"
                    >
                        <div
                            v-if="selectedValue === 'step'"
                            class="absolute inset-1 rounded-full bg-white"
                        ></div>
                    </div>
                </div>

                <!-- Contenu de la carte -->
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-lg transition-colors duration-200"
                            :class="selectedValue === 'step'
                                ? 'bg-blue-100 text-blue-600'
                                : 'bg-gray-100 text-gray-600'"
                        >
                            <Icon name="flag" class="h-6 w-6" />
                        </div>
                        <div>
                            <h4
                                class="font-semibold transition-colors duration-200"
                                :class="selectedValue === 'step'
                                    ? 'text-blue-900'
                                    : 'text-gray-900'"
                            >
                                Étape
                            </h4>
                            <p
                                class="text-sm transition-colors duration-200"
                                :class="selectedValue === 'step'
                                    ? 'text-blue-700'
                                    : 'text-gray-600'"
                            >
                                Tâche ou livrable
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <span
                            class="rounded-full px-3 py-1 text-xs font-medium transition-colors duration-200"
                            :class="selectedValue === 'step'
                                ? 'bg-blue-200 text-blue-800'
                                : 'bg-gray-200 text-gray-600'"
                        >
                            Suivi d'avancement
                        </span>
                        <span
                            class="rounded-full px-3 py-1 text-xs font-medium transition-colors duration-200"
                            :class="selectedValue === 'step'
                                ? 'bg-blue-200 text-blue-800'
                                : 'bg-gray-200 text-gray-600'"
                        >
                            Date d'exécution
                        </span>
                    </div>
                </div>
            </div>

            <!-- Carte Facturation -->
            <div
                @click="selectType('billing')"
                class="relative cursor-pointer rounded-xl border-1 p-6 transition-all duration-200 hover:shadow-md"
                :class="selectedValue === 'billing'
                    ? 'border-purple-500 bg-purple-50 shadow-sm'
                    : 'border-gray-200 bg-white hover:border-gray-300'"
            >
                <!-- Indicateur de sélection -->
                <div class="absolute top-4 right-4">
                    <div
                        class="h-5 w-5 rounded-full border-2 transition-all duration-200"
                        :class="selectedValue === 'billing'
                            ? 'border-purple-500 bg-purple-500'
                            : 'border-gray-300 bg-white'"
                    >
                        <div
                            v-if="selectedValue === 'billing'"
                            class="absolute inset-1 rounded-full bg-white"
                        ></div>
                    </div>
                </div>

                <!-- Contenu de la carte -->
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-lg transition-colors duration-200"
                            :class="selectedValue === 'billing'
                                ? 'bg-purple-100 text-purple-600'
                                : 'bg-gray-100 text-gray-600'"
                        >
                            <Icon name="banknote" class="h-6 w-6" />
                        </div>
                        <div>
                            <h4
                                class="font-semibold transition-colors duration-200"
                                :class="selectedValue === 'billing'
                                    ? 'text-purple-900'
                                    : 'text-gray-900'"
                            >
                                Facturation
                            </h4>
                            <p
                                class="text-sm transition-colors duration-200"
                                :class="selectedValue === 'billing'
                                    ? 'text-purple-700'
                                    : 'text-gray-600'"
                            >
                                Document financier
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <span
                            class="rounded-full px-3 py-1 text-xs font-medium transition-colors duration-200"
                            :class="selectedValue === 'billing'
                                ? 'bg-purple-200 text-purple-800'
                                : 'bg-gray-200 text-gray-600'"
                        >
                            Montant & échéance
                        </span>
                        <span
                            class="rounded-full px-3 py-1 text-xs font-medium transition-colors duration-200"
                            :class="selectedValue === 'billing'
                                ? 'bg-purple-200 text-purple-800'
                                : 'bg-gray-200 text-gray-600'"
                        >
                            Suivi paiement
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Message d'aide -->
        <div class="rounded-lg bg-gray-50 p-4 mt-6">
            <div class="flex items-start gap-3">
                <Icon name="info" class="mt-0.5 h-4 w-4 text-gray-600" />
                <div class="text-sm text-gray-700">
                    <p v-if="selectedValue === 'step'" class="font-medium text-gray-900 mb-1">
                        Mode Étape sélectionné
                    </p>
                    <p v-else-if="selectedValue === 'billing'" class="font-medium text-gray-900 mb-1">
                        Mode Facturation sélectionné
                    </p>
                    <p v-else class="font-medium text-gray-900 mb-1">
                        Sélectionnez un type d'événement
                    </p>
                    <p v-if="selectedValue === 'step'">
                        L'événement sera traité comme une tâche du projet avec suivi d'exécution et gestion des statuts.
                    </p>
                    <p v-else-if="selectedValue === 'billing'">
                        L'événement inclura des informations financières : montant, échéance et suivi de paiement.
                    </p>
                    <p v-else>
                        Choisissez "Étape" pour les tâches du projet ou "Facturation" pour les aspects financiers.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import Icon from '@/components/Icon.vue'

interface Props {
    modelValue?: string | null
}

interface Emits {
    (e: 'update:modelValue', value: string): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

const selectedValue = computed(() => props.modelValue || null)

const selectType = (type: string) => {
    emit('update:modelValue', type)
}
</script>
