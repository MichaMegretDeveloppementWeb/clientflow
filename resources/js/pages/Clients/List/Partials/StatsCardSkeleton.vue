<template>
    <Card class="h-full border-none">
        <CardContent class="flex h-full flex-col p-2.5 sm:p-3">
            <div class="flex flex-1 items-start justify-between">
                <div class="flex h-full flex-1 flex-col justify-between">
                    <div class="mb-1.5 flex items-center gap-2">
                        <!-- Icon skeleton -->
                        <div class="rounded-md bg-gray-100 p-1.5">
                            <Skeleton width="0.875rem" height="0.875rem" className="rounded-sm" />
                        </div>
                        <!-- Title skeleton -->
                        <Skeleton width="4rem" height="0.75rem" className="sm:w-20" />
                    </div>
                    <div class="mt-auto">
                        <!-- Value skeleton -->
                        <Skeleton width="3rem" height="1.5rem" className="sm:h-8 sm:w-16" />
                    </div>
                </div>
            </div>
        </CardContent>
    </Card>
</template>

<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card'
import Skeleton from '@/components/ui/Skeleton.vue'

/**
 * Skeleton pour les cartes de statistiques
 * Réplique exactement la structure de StatsCard
 */
</script>
