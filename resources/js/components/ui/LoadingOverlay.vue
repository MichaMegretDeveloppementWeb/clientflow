<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition-opacity duration-200 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-150 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="isVisible"
                class="fixed inset-0 z-50 bg-white/80 backdrop-blur-sm"
            >
                <!-- Barre de progression en haut -->
                <div class="absolute top-0 left-0 h-1 bg-gradient-to-r from-blue-500 to-purple-600">
                    <div
                        class="h-full bg-gradient-to-r from-blue-600 to-purple-700 transition-all duration-300 ease-out"
                        :style="{ width: `${progress}%` }"
                    />
                </div>

                <!-- Contenu centré -->
                <div class="flex min-h-screen items-center justify-center">
                    <div class="text-center">
                        <!-- Logo ou icône avec animation -->
                        <div class="mb-6 flex justify-center">
                            <div class="relative">
                                <div class="h-12 w-12 animate-spin rounded-full border-4 border-blue-200 border-t-blue-600"></div>
                                <div class="absolute inset-0 h-12 w-12 animate-ping rounded-full border-4 border-blue-400 opacity-20"></div>
                            </div>
                        </div>

                        <!-- Message -->
                        <h3 class="mb-2 text-lg font-semibold text-gray-900">
                            {{ title }}
                        </h3>
                        <p class="text-sm text-gray-600">
                            {{ message }}
                        </p>

                        <!-- Points animés -->
                        <div class="mt-4 flex justify-center space-x-1">
                            <div
                                v-for="i in 3"
                                :key="i"
                                class="h-2 w-2 animate-bounce rounded-full bg-blue-500"
                                :style="{ animationDelay: `${i * 0.1}s` }"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup lang="ts">
import { computed } from 'vue'

interface Props {
    isVisible?: boolean
    progress?: number
    title?: string
    message?: string
}

const props = withDefaults(defineProps<Props>(), {
    isVisible: false,
    progress: 0,
    title: 'Chargement en cours',
    message: 'Veuillez patienter pendant que nous préparons vos données...'
})
</script>