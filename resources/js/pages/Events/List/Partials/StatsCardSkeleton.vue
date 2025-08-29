<template>
    <Card class="h-full cursor-pointer rounded-xl border border-gray-200/60 bg-white shadow-sm">
        <CardContent class="flex h-full flex-col p-2.5 sm:p-3">
            <div class="flex flex-1 items-start justify-between">
                <div class="flex h-full flex-1 flex-col justify-between">
                    <div class="mb-1.5 flex items-center gap-2">
                        <!-- Icon skeleton -->
                        <div class="rounded-md bg-gray-100 p-1.5">
                            <div class="h-3.5 w-3.5 animate-pulse rounded-sm bg-gray-200"></div>
                        </div>
                        <!-- Title skeleton -->
                        <div class="h-3 w-16 animate-pulse rounded bg-gray-200 sm:h-3.5 sm:w-20"></div>
                    </div>
                    <div class="mt-auto">
                        <!-- Value skeleton -->
                        <div class="h-6 w-16 animate-pulse rounded bg-gray-200 sm:h-7 sm:w-20"></div>
                    </div>
                </div>
            </div>
        </CardContent>
    </Card>
</template>

<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card'

/**
 * Skeleton pour les cartes de statistiques des événements
 * Réplique exactement la structure de StatsCard
 * Harmonisé avec le design uniforme des skeletons
 */
</script>