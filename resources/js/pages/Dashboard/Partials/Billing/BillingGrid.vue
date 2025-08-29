<template>
    <div
        v-if="!isLoading && cards.length > 0"
        class="grid gap-4 billing-grid mt-10"
    >
        <template v-for="card in cards" :key="card.id">
            <!-- Card normale sans détails -->
            <BillingCard
                v-if="!card.details"
                :class="getBillingCardClass(card.id)"
                :title="card.title"
                :value="card.value"
                :icon="card.icon"
                :color="card.color"
                :link="card.link"
                :description="card.description"
            />

            <!-- Card avec détails (Factures envoyées) -->
            <BillingDetailCard
                v-else
                :class="getBillingCardClass(card.id)"
                :title="card.title"
                :details="card.details"
                :link="card.link"
            />
        </template>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div v-for="i in 3" :key="i" class="bg-white rounded-lg shadow p-6">
            <div class="animate-pulse">
                <div class="h-4 w-24 bg-gray-200 rounded mb-3"></div>
                <div class="h-8 w-32 bg-gray-200 rounded mb-2"></div>
                <div class="h-3 w-20 bg-gray-200 rounded"></div>
            </div>
        </div>
    </div>

    <!-- Error State -->
    <div v-if="error" class="p-4 bg-red-50 border border-red-200 rounded-lg">
        <p class="text-red-600">{{ error }}</p>
    </div>
</template>

<script setup lang="ts">
import { onMounted } from 'vue'
import BillingCard from './BillingCard.vue'
import BillingDetailCard from './BillingDetailCard.vue'
import { useBilling } from '@/composables/dashboard/useBilling'

const {
    cards,
    isLoading,
    error,
    loadBillingData,
} = useBilling()

const getBillingCardClass = (cardId: string): string => {
    // Sur mobile : Total facturé et Factures à envoyer côte à côte (row 1)
    // Factures envoyées en dessous (row 2)
    switch (cardId) {
        case 'total_billed':
            return 'billing-card-total-billed'
        case 'to_send':
            return 'billing-card-to-send'
        case 'sent_details':
            return 'billing-card-sent-details'
        default:
            return ''
    }
}

onMounted(() => {
    loadBillingData()
})
</script>

<style scoped>
/* Disposition responsive des cartes billing */

/* Par défaut (mobile et petit écran) : 2 colonnes avec disposition spéciale */
.billing-grid {
    @apply grid-cols-2;
    grid-template-rows: auto auto;
}

/* Total facturé : position 1,1 */
.billing-card-total-billed {
    grid-column: 1;
    grid-row: 1;
}

/* Factures à envoyer : position 2,1 */
.billing-card-to-send {
    grid-column: 2;
    grid-row: 1;
}

/* Factures envoyées : position 1-2,2 (s'étend sur 2 colonnes) */
.billing-card-sent-details {
    grid-column: 1 / -1;
    grid-row: 2;
}

/* Sur grand écran : 3 colonnes normales */
@media (min-width: 1024px) {
    .billing-grid {
        @apply grid-cols-3;
        grid-template-rows: auto;
    }

    .billing-card-total-billed,
    .billing-card-to-send,
    .billing-card-sent-details {
        grid-column: auto;
        grid-row: auto;
    }
}
</style>
