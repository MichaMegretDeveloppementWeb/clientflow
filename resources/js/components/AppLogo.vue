<script setup lang="ts">
import stoneLogo from '../../images/stone_logo.webp'
import { useSidebar } from '@/components/ui/sidebar/utils'

const { state } = useSidebar()
</script>

<template>
    <div class="flex aspect-square size-8 items-center justify-center overflow-hidden">
        <img
            :src="stoneLogo"
            alt="Stone Logo"
            class="size-6 object-contain"
        />
    </div>
    <div v-show="state === 'expanded'" class="ml-1 grid flex-1 text-left text-sm">
        <span class="mb-0.5 truncate leading-tight font-semibold">Stone</span>
    </div>
</template>
