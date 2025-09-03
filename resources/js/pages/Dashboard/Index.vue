<template>
    <Head title="Dashboard" />

    <AppLayout>
        <div class="space-y-8">
            <!-- Page Header -->
            <DashboardHeader @refresh="handleRefresh" />

            <!-- Main Content Grid - Tâches à venir en priorité -->
            <div class="flex flex-col gap-6 lg:gap-8 lg:flex-row">

                <!-- Tâches urgentes -->
                <UrgentTasksCard />

                <!-- Activité récente -->
                <RecentActivityCard />

            </div>

            <!-- Stats Grid -->
            <StatsGrid />

            <!-- Cartes de facturation -->
            <BillingGrid />


            <!-- Graphique chiffre d'affaires -->
            <LazyWrapper
                :loader="() => import('./Partials/Chart/RevenueChart.vue')"
                eager
            >
                <template #loading>
                    <SkeletonChart
                        chart-type="line"
                        chart-height="300px"
                        :has-controls="true"
                        :has-metrics="true"
                    />
                </template>
                <template #error="{ retry }">
                    <Card class="border border-red-200 bg-red-50 p-6">
                        <div class="text-center">
                            <OptimizedIcon name="alert-circle" :size="48" class="mx-auto text-red-400 mb-4" />
                            <h3 class="text-lg font-medium text-red-900 mb-2">
                                Erreur de chargement du graphique
                            </h3>
                            <p class="text-sm text-red-600 mb-4">
                                Impossible de charger le graphique des revenus.
                            </p>
                            <Button @click="retry" variant="outline" size="sm">
                                <OptimizedIcon name="refresh-cw" :size="16" class="mr-2" />
                                Réessayer
                            </Button>
                        </div>
                    </Card>
                </template>
            </LazyWrapper>

            <!-- Statistiques rapides -->
            <QuickStatsGrid />

            <!-- Section d'aide -->
<!--            <HelpGrid />-->
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Card } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import OptimizedIcon from '@/components/OptimizedIcon.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import LazyWrapper from '@/components/LazyWrapper.vue';
import { SkeletonChart } from '@/components/skeletons';

// Imports directs pour les composants critiques
import DashboardHeader from './Partials/DashboardHeader.vue';
import UrgentTasksCard from './Partials/Tasks/UrgentTasksCard.vue';
import RecentActivityCard from './Partials/Activities/RecentActivityCard.vue';
import StatsGrid from './Partials/Stats/StatsGrid.vue';
import BillingGrid from './Partials/Billing/BillingGrid.vue';
import QuickStatsGrid from './Partials/QuickStats/QuickStatsGrid.vue';

// Initialisation simplifiée
// Supprimé le préchargement d'icônes inutile

// Gestionnaire de rafraîchissement global (optionnel)
const handleRefresh = () => {
    // Les composants autonomes gèrent leur propre rafraîchissement
    // On peut ajouter ici une notification globale si nécessaire
    window.location.reload();
};

// Note: Le préchargement des composants n'est plus nécessaire
// car ils sont maintenant chargés avec Suspense et defineAsyncComponent
</script>

<style scoped>
/* Animations pour les transitions */
.dashboard-enter-active,
.dashboard-leave-active {
    transition: all 0.3s ease;
}

.dashboard-enter-from,
.dashboard-leave-to {
    opacity: 0;
    transform: translateY(10px);
}

/* Optimisations de performance */
.dashboard-grid {
    contain: layout style paint;
}

/* Animations de hover personnalisées */
.hover-lift {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}
</style>
