<template>
    <!-- Cards de métriques principales (style Stripe) -->
    <div v-if="project.budget" class="grid gap-4 grid-cols-1 md:gap-6 xl:grid-cols-2 2xl:grid-cols-4 mb-6">
        <!-- Budget Initial -->
        <Card class="border-0 bg-white shadow-sm ring-1 ring-gray-200/60">
            <CardContent class="p-4 sm:p-6">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Budget initial</p>
                        <p class="text-lg sm:text-2xl font-bold text-gray-900 leading-tight">{{ statsData?.budget.formatted }}</p>
                    </div>
                    <div class="rounded-full bg-blue-50 p-2 sm:p-3 flex-shrink-0">
                        <Icon name="target" class="h-4 w-4 sm:h-6 sm:w-6 text-blue-600" />
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- Total Facturé -->
        <Card class="border-0 bg-white shadow-sm ring-1 ring-gray-200/60">
            <CardContent class="p-4 pb-2 sm:p-6 sm:pb-2">
                <div class="flex items-start justify-between gap-3 mb-6 sm:mb-6">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Total facturé</p>
                        <p class="text-lg sm:text-2xl font-bold text-gray-900 leading-tight">{{ statsData?.totalBilled.formatted }}</p>
                    </div>
                    <div class="rounded-full bg-purple-50 p-2 sm:p-3 flex-shrink-0">
                        <Icon name="calculator" class="h-4 w-4 sm:h-6 sm:w-6 text-purple-600" />
                    </div>
                </div>
                <!-- Progress indicator -->
                <div class="mb-3">
                    <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                        <span class="truncate">{{ statsData?.budgetUsage.percentage }}% du budget</span>
                        <span v-if="statsData?.budgetUsage.isExceeded" class="text-red-600 font-medium text-xs whitespace-nowrap ml-2">Dépassé</span>
                    </div>
                    <div class="h-1.5 bg-gray-200 rounded-full overflow-hidden">
                        <div
                            class="h-full transition-all duration-300"
                            :class="progressBarClasses"
                            :style="{ width: (statsData?.budgetUsage.percentage || 0) + '%' }"
                        ></div>
                    </div>
                </div>
                <!-- À envoyer -->
                <div v-if="statsData?.billsToSend?.amount && statsData.billsToSend.amount > 0" class="flex justify-between">
                    <span class="text-gray-500 text-xs">À envoyer</span>
                    <span class="text-orange-600 font-medium text-xs">{{ statsData?.billsToSend?.formatted }}</span>
                </div>
            </CardContent>
        </Card>

        <!-- Total Payé -->
        <Card class="border-0 bg-white shadow-sm ring-1 ring-gray-200/60">
            <CardContent class="p-4 sm:p-6">
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Total payé</p>
                        <p class="text-lg sm:text-2xl font-bold text-gray-900 leading-tight">{{ statsData?.totalPaid.formatted }}</p>
                    </div>
                    <div class="rounded-full bg-emerald-50 p-2 sm:p-3 flex-shrink-0">
                        <Icon name="check-circle" class="h-4 w-4 sm:h-6 sm:w-6 text-emerald-600" />
                    </div>
                </div>
                <!-- Détail payé/impayé -->
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-500 text-xs">Paiements à venir</span>
                        <span class="text-blue-600 font-medium text-xs">{{ statsData?.upcomingPayments?.formatted }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 text-xs">Impayé</span>
                        <span class="text-red-600 font-medium text-xs">{{ statsData?.overdueUnpaid?.formatted }}</span>
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- Solde Budget -->
        <Card class="border-0 bg-white shadow-sm ring-1 ring-gray-200/60">
            <CardContent class="p-4 sm:p-6">
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs sm:text-sm font-medium text-gray-600 mb-1">Solde budget</p>
                        <p class="text-lg sm:text-2xl font-bold leading-tight" :class="remainingBudgetClasses">
                            {{ statsData?.remainingBudget.formatted }}
                        </p>
                    </div>
                    <div class="rounded-full p-2 sm:p-3 flex-shrink-0" :class="remainingBudgetIconClasses">
                        <Icon name="banknote" class="h-4 w-4 sm:h-6 sm:w-6" :class="remainingBudgetIconColorClasses" />
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>

<script setup lang="ts">
import Icon from '@/components/Icon.vue'
import { Card, CardContent } from '@/components/ui/card'
import { useProjectDetailStats } from '@/composables/projects/detail/useProjectDetailStats'
import { computed } from 'vue'

interface Props {
    project: any
    financialStats: any
}

const props = defineProps<Props>()

// Créer une ref réactive pour financialStats
const financialStatsRef = computed(() => props.financialStats)

// Utiliser le composable pour la logique des statistiques avec formatage côté front
const {
    statsData,
    remainingBudgetClasses,
    remainingBudgetIconClasses,
    remainingBudgetIconColorClasses,
    progressBarClasses
} = useProjectDetailStats(financialStatsRef)

</script>
