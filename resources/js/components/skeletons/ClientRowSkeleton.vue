<template>
    <div class="animate-pulse p-6">
        <div class="flex items-center justify-between">
            <!-- Avatar/Icon skeleton -->
            <div class="flex items-center gap-4">
                <div class="h-10 w-10 rounded-full bg-gray-200"></div>
                
                <!-- Content skeleton -->
                <div class="space-y-2">
                    <div class="h-4 w-48 rounded bg-gray-200"></div>
                    <div class="h-3 w-32 rounded bg-gray-200"></div>
                </div>
            </div>
            
            <!-- Stats skeleton -->
            <div class="hidden gap-8 lg:flex">
                <div class="h-3 w-24 rounded bg-gray-200"></div>
                <div class="h-3 w-20 rounded bg-gray-200"></div>
                <div class="h-3 w-28 rounded bg-gray-200"></div>
            </div>
            
            <!-- Actions skeleton -->
            <div class="flex items-center gap-2">
                <div class="h-8 w-8 rounded bg-gray-200"></div>
                <div class="h-8 w-8 rounded bg-gray-200"></div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
/**
 * Skeleton loader pour une ligne de client
 * Utilisé pendant le chargement des données
 */
</script>