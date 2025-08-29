<template>
    <div class="border-t border-gray-200/70 bg-gradient-to-r from-gray-50/50 to-white px-6 py-4 sm:px-8">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
            <!-- Pagination info -->
            <div class="text-center text-sm text-gray-600 sm:text-left">
                <span class="inline-flex items-center gap-1">
                    <span class="font-semibold text-gray-900">{{ pageInfo.start }}</span>
                    <span class="text-gray-400">à</span>
                    <span class="font-semibold text-gray-900">{{ pageInfo.end }}</span>
                    <span class="text-gray-400">sur</span>
                    <span class="rounded-md bg-blue-50 px-2 py-0.5 text-xs font-bold text-blue-700">
                        {{ pageInfo.total }}
                    </span>
                    <span class="text-gray-500">résultats</span>
                </span>
            </div>

            <!-- Pagination controls -->
            <div class="flex items-center justify-center gap-3">
                <!-- Previous button -->
                <Button
                    variant="outline"
                    size="sm"
                    :disabled="!canGoPrev"
                    @click="$emit('page-change', paginationState.currentPage - 1)"
                    class="group rounded-lg border-gray-300 bg-white px-3 py-2 text-gray-700 shadow-sm transition-all duration-200 hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700 hover:shadow-md disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:border-gray-300 disabled:hover:bg-white disabled:hover:text-gray-700 disabled:hover:shadow-sm"
                >
                    <Icon name="chevron-left" class="h-4 w-4 transition-transform group-hover:-translate-x-0.5" />
                    <span class="ml-1.5 hidden font-medium sm:inline">Précédent</span>
                </Button>

                <!-- Page numbers -->
                <div class="hidden items-center gap-1.5 sm:flex">
                    <template v-for="(page, index) in visiblePages" :key="index">
                        <Button
                            v-if="page === -1"
                            variant="ghost"
                            size="sm"
                            disabled
                            class="h-9 w-9 cursor-default text-gray-400"
                        >
                            <Icon name="more-horizontal" class="h-4 w-4" />
                        </Button>
                        <Button
                            v-else
                            :variant="page === paginationState.currentPage ? 'default' : 'outline'"
                            size="sm"
                            class="h-9 w-9 rounded-lg font-medium shadow-sm transition-all duration-200"
                            :class="page === paginationState.currentPage
                                ? 'border-blue-500 bg-gradient-to-b from-blue-500 to-blue-600 text-white shadow-blue-500/25 hover:from-blue-600 hover:to-blue-700 hover:shadow-blue-600/30'
                                : 'border-gray-300 bg-white text-gray-700 hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700 hover:shadow-md'"
                            @click="$emit('page-change', page)"
                        >
                            {{ page }}
                        </Button>
                    </template>
                </div>

                <!-- Mobile page indicator -->
                <div class="flex items-center gap-2.5 text-sm font-medium text-gray-600 sm:hidden">
                    <span class="text-gray-500">Page</span>
                    <Input
                        :value="paginationState.currentPage"
                        type="number"
                        :min="1"
                        :max="paginationState.lastPage"
                        class="h-9 w-16 rounded-lg border-gray-300 bg-white text-center text-sm font-semibold text-gray-900 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                        @change="handlePageInput"
                    />
                    <span class="text-gray-500">sur</span>
                    <span class="rounded-md bg-gray-100 px-2 py-1 text-xs font-bold text-gray-700">
                        {{ paginationState.lastPage }}
                    </span>
                </div>

                <!-- Next button -->
                <Button
                    variant="outline"
                    size="sm"
                    :disabled="!canGoNext"
                    @click="$emit('page-change', paginationState.currentPage + 1)"
                    class="group rounded-lg border-gray-300 bg-white px-3 py-2 text-gray-700 shadow-sm transition-all duration-200 hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700 hover:shadow-md disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:border-gray-300 disabled:hover:bg-white disabled:hover:text-gray-700 disabled:hover:shadow-sm"
                >
                    <span class="mr-1.5 hidden font-medium sm:inline">Suivant</span>
                    <Icon name="chevron-right" class="h-4 w-4 transition-transform group-hover:translate-x-0.5" />
                </Button>
            </div>

            <!-- Page size selector (optional) -->
            <div v-if="showPageSizeSelector" class="flex items-center gap-3 text-sm">
                <span class="text-gray-600">Afficher</span>
                <Select
                    :model-value="String(paginationState.perPage)"
                    @update:model-value="handlePageSizeChange"
                >
                    <SelectTrigger class="h-9 w-20 rounded-lg border-gray-300 bg-white shadow-sm transition-colors hover:border-blue-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20">
                        <SelectValue />
                    </SelectTrigger>
                    <SelectContent class="rounded-lg border-gray-200 shadow-lg">
                        <SelectItem
                            v-for="size in pageSizeOptions"
                            :key="size"
                            :value="String(size)"
                            class="rounded-md font-medium hover:bg-blue-50 hover:text-blue-700 focus:bg-blue-50 focus:text-blue-700"
                        >
                            {{ size }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <span class="text-gray-600">par page</span>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select'
import Icon from '@/components/Icon.vue'
import type { PaginationState } from '@/types/projects/list'

/**
 * Composant de pagination
 * Responsabilités :
 * - Navigation entre les pages
 * - Affichage des infos de pagination
 * - Changement de taille de page (optionnel)
 */

interface Props {
    paginationState: PaginationState
    pageInfo: {
        start: number
        end: number
        total: number
    }
    visiblePages: number[]
    canGoNext: boolean
    canGoPrev: boolean
    showPageSizeSelector?: boolean
    pageSizeOptions?: number[]
}

const props = withDefaults(defineProps<Props>(), {
    showPageSizeSelector: false,
    pageSizeOptions: () => [10, 15, 25, 50, 100]
})

const emit = defineEmits<{
    'page-change': [page: number]
    'page-size-change': [size: number]
}>()

const handlePageInput = (event: Event) => {
    const target = event.target as HTMLInputElement
    const page = parseInt(target.value, 10)

    if (page >= 1 && page <= props.paginationState.lastPage) {
        emit('page-change', page)
    }
}

const handlePageSizeChange = (value: string) => {
    const size = parseInt(value, 10)
    emit('page-size-change', size)
}
</script>