<script setup lang="ts">
import TopNavbar from '@/components/TopNavbar.vue'
import Breadcrumbs from '@/components/Breadcrumbs.vue'
import type { BreadcrumbItemType } from '@/types'

interface Props {
    breadcrumbs?: BreadcrumbItemType[]
}

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
})
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Navbar fixe en haut -->
        <TopNavbar />

        <!-- Breadcrumbs si présents -->
        <div v-if="props.breadcrumbs.length > 1" class="border-b bg-white">
            <div class="mx-auto w-full max-w-full min-[900px]:max-w-[min(90%,100em)] px-4 py-3 sm:px-6 lg:px-8">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </div>
        </div>

        <!-- Contenu principal -->
        <main class="flex-1">
            <div class="mx-auto w-full max-w-full min-[1000px]:max-w-[min(95%,100em)] px-4 py-6 sm:px-6 lg:px-8 pt-16">
                <slot />
            </div>
        </main>
    </div>
</template>
