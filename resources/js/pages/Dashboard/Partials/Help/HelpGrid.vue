<template>
    <Card class="border-0 bg-gradient-to-br from-blue-50 to-indigo-50 shadow-sm">
        <CardHeader class="pb-4">
            <CardTitle class="flex items-center gap-2 text-blue-900">
                <OptimizedIcon name="lightbulb" :size="20" preload />
                Premiers pas
            </CardTitle>
        </CardHeader>
        
        <CardContent v-if="isLoading" class="space-y-4">
            <div class="animate-pulse">
                <div class="h-4 bg-blue-200 rounded mb-4"></div>
                <div class="flex flex-col gap-2 sm:flex-row">
                    <div class="h-8 bg-blue-200 rounded flex-1"></div>
                    <div class="h-8 bg-blue-200 rounded flex-1"></div>
                </div>
                <div class="mt-4 space-y-3">
                    <div class="h-4 bg-blue-200 rounded w-1/2"></div>
                    <div class="space-y-2">
                        <div class="h-8 bg-blue-200 rounded"></div>
                        <div class="h-8 bg-blue-200 rounded"></div>
                        <div class="h-8 bg-blue-200 rounded"></div>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-blue-200">
                    <div class="flex justify-between items-center mb-2">
                        <div class="h-3 bg-blue-200 rounded w-20"></div>
                        <div class="h-3 bg-blue-200 rounded w-10"></div>
                    </div>
                    <div class="w-full bg-blue-200 rounded-full h-2"></div>
                </div>
            </div>
        </CardContent>

        <CardContent v-else-if="error" class="space-y-4">
            <div class="text-center py-8">
                <OptimizedIcon name="alert-circle" :size="48" class="mx-auto text-red-400 mb-4" />
                <h4 class="text-sm font-medium text-red-900 mb-2">Erreur de chargement</h4>
                <p class="text-sm text-red-600 mb-4">{{ error }}</p>
                <Button @click="refreshHelp" variant="outline" size="sm">
                    <OptimizedIcon name="refresh-cw" :size="16" class="mr-2" />
                    Réessayer
                </Button>
            </div>
        </CardContent>

        <CardContent v-else-if="hasHelp" class="space-y-4">
            <p class="text-sm text-blue-800">
                Découvrez comment tirer le meilleur parti de ClientFlow pour optimiser votre gestion.
            </p>
            
            <!-- Actions principales -->
            <div class="flex flex-col gap-2 sm:flex-row">
                <Button 
                    v-for="action in help.actions"
                    :key="action.id"
                    size="sm" 
                    :variant="action.variant"
                    class="border-blue-200 text-blue-700 hover:bg-blue-100"
                    @click="handleActionClick(action.action)"
                >
                    <OptimizedIcon :name="action.icon" :size="16" class="mr-2" />
                    {{ action.text }}
                </Button>
            </div>

            <!-- Conseils rapides -->
            <div class="mt-4 space-y-3">
                <h4 class="text-sm font-medium text-blue-900">Conseils rapides :</h4>
                
                <div class="space-y-2">
                    <HelpTipItem
                        v-for="tip in help.tips"
                        :key="tip.id"
                        :icon="tip.icon"
                        :text="tip.text"
                        :completed="tip.completed"
                        @click="handleTipClick(tip.route)"
                    />
                </div>
            </div>

            <!-- Progression de l'onboarding -->
            <div class="mt-4 pt-4 border-t border-blue-200">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-xs text-blue-600 font-medium">Configuration</span>
                    <span class="text-xs text-blue-600">{{ help.onboarding.progress_formatted }}</span>
                </div>
                
                <div class="w-full bg-blue-200 rounded-full h-2">
                    <div 
                        class="bg-blue-500 h-2 rounded-full transition-all duration-500"
                        :style="{ width: `${help.onboarding.progress}%` }"
                    ></div>
                </div>
            </div>
        </CardContent>

        <CardContent v-else class="space-y-4">
            <div class="text-center py-8">
                <OptimizedIcon name="lightbulb" :size="48" class="mx-auto text-gray-400 mb-4" />
                <h4 class="text-sm font-medium text-gray-900 mb-2">Aucune donnée</h4>
                <p class="text-sm text-gray-600">Aucune aide disponible</p>
            </div>
        </CardContent>
    </Card>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import OptimizedIcon from '@/components/OptimizedIcon.vue'
import HelpTipItem from './HelpTipItem.vue'
import { useHelp } from '@/composables/dashboard/useHelp'

const {
    help,
    isLoading,
    error,
    hasHelp,
    loadHelp,
    refreshHelp,
    handleTipClick,
    handleActionClick,
} = useHelp()

onMounted(async () => {
    await loadHelp()
})
</script>

<style scoped>
/* Animation de la barre de progression */
.bg-blue-500 {
    transition: width 0.5s ease-out;
}

/* Gradient background optimisé */
.bg-gradient-to-br {
    background-attachment: fixed;
}
</style>