<template>
    <Link
        :href="link"
        class="group h-full sm:col-span-2 lg:col-span-1"
    >
        <Card class="h-full rounded-xl border border-gray-200/60 bg-white shadow-sm transition-all duration-300 group-hover:-translate-y-0.5 group-hover:border-emerald-200 group-hover:shadow-md py-0">
            <CardContent class="flex h-full flex-col p-4 sm:p-6">
                <div class="flex flex-1 items-start justify-between">
                    <div class="flex h-full flex-1 flex-col justify-between">
                        <!-- Header -->
                        <div class="mb-3 flex items-center gap-2">
                            <div class="rounded-lg bg-emerald-50 p-2 transition-transform group-hover:scale-110">
                                <OptimizedIcon name="send" :size="16" class="text-emerald-600" preload />
                            </div>
                            <span class="text-sm font-medium text-gray-600">{{ title }}</span>
                        </div>

                        <!-- Valeur totale -->
                        <div class="mt-auto space-y-1">
                            <div class="md:text-2xl text-xl font-bold text-gray-900">
                                {{ formatCurrency(details.sent) }}
                            </div>

                            <!-- Détail des paiements -->
                            <div class="mt-3 grid gap-1 grid-cols-3">
                                <!-- Payé -->
                                <Link
                                    :href="route('events.index', {
                                        event_type: 'billing',
                                        status: 'sent',
                                        payment_status: 'paid',
                                        from: 'dashboard'
                                    })"
                                    class="text-left transition-colors hover:bg-emerald-50 p-1 rounded"
                                    @click.stop
                                >
                                    <span class="text-xs text-emerald-600 font-medium block">
                                        {{ formatCurrency(details.paid) }}
                                    </span>
                                    <span class="text-xs text-gray-500">Payé</span>
                                </Link>

                                <!-- À payer -->
                                <Link
                                    :href="route('events.index', {
                                        event_type: 'billing',
                                        status: 'sent',
                                        payment_status: 'pending',
                                        from: 'dashboard'
                                    })"
                                    class="text-left transition-colors hover:bg-amber-50 p-1 rounded"
                                    @click.stop
                                >
                                    <span class="text-xs text-amber-600 font-medium block">
                                        {{ formatCurrency(details.upcoming) }}
                                    </span>
                                    <span class="text-xs text-gray-500">À payer</span>
                                </Link>

                                <!-- En retard -->
                                <Link
                                    :href="route('events.index', {
                                        event_type: 'billing',
                                        status: 'sent',
                                        payment_status: 'overdue',
                                        from: 'dashboard'
                                    })"
                                    class="text-left transition-colors hover:bg-red-50 p-1 rounded"
                                    @click.stop
                                >
                                    <span class="text-xs text-red-600 font-medium block">
                                        {{ formatCurrency(details.overdue) }}
                                    </span>
                                    <span class="text-xs text-gray-500">En retard</span>
                                </Link>
                            </div>

                            <!-- Barre de progression des paiements -->
                            <div class="mt-3 space-y-1">
                                <div class="flex justify-between text-xs text-gray-500">
                                    <span>Taux de paiement</span>
                                    <span>{{ formatPercentage(details.payment_rate) }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                    <div
                                        class="bg-emerald-500 h-1.5 rounded-full transition-all duration-500"
                                        :style="{ width: `${details.payment_rate}%` }"
                                    ></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </CardContent>
        </Card>
    </Link>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3'
import { Card, CardContent } from '@/components/ui/card'
import OptimizedIcon from '@/components/OptimizedIcon.vue'
import type { BillingCardDetails } from '@/types/dashboard/billing'

interface Props {
    title: string
    details: BillingCardDetails
    link: string
}

defineProps<Props>()

// Utilitaires de formatage
const formatCurrency = (amount: number): string => {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
    }).format(amount || 0).replace(/[\u202F\u00A0]/g, '\u2004')
}

const formatPercentage = (value: number): string => {
    return `${Math.round(value || 0)}%`
}
</script>

<style scoped>
/* Animation de la barre de progression */
.bg-emerald-500 {
    transition: width 0.5s ease-in-out;
}

/* Hover effects pour les sous-liens */
.grid a {
    transition: background-color 0.2s ease;
}
</style>
