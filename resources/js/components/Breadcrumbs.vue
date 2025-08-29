<script setup lang="ts">
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { Breadcrumb, BreadcrumbItem, BreadcrumbLink, BreadcrumbList, BreadcrumbPage, BreadcrumbSeparator, BreadcrumbEllipsis } from '@/components/ui/breadcrumb';
import { Link } from '@inertiajs/vue3';

interface BreadcrumbItemType {
    title: string;
    href?: string;
}

const props = defineProps<{
    breadcrumbs: BreadcrumbItemType[];
}>();

// Logique responsive pour déterminer le nombre maximum d'éléments
const screenWidth = ref(typeof window !== 'undefined' ? window.innerWidth : 1024);

const updateScreenWidth = () => {
    screenWidth.value = window.innerWidth;
};

onMounted(() => {
    window.addEventListener('resize', updateScreenWidth);
    updateScreenWidth();
});

onUnmounted(() => {
    window.removeEventListener('resize', updateScreenWidth);
});

const maxItems = computed(() => {
    if (screenWidth.value < 640) return 2; // mobile: très petit, seulement 2 éléments
    if (screenWidth.value < 768) return 3; // sm: 3 éléments
    if (screenWidth.value < 1024) return 4; // md: 4 éléments
    return props.breadcrumbs.length; // lg+: tous les éléments
});

// Logique de troncature intelligente pour mobile
const displayBreadcrumbs = computed(() => {
    if (props.breadcrumbs.length <= maxItems.value) {
        return { items: props.breadcrumbs, showEllipsis: false };
    }

    // Toujours afficher le premier et le dernier élément
    // Afficher les éléments du milieu selon l'espace disponible
    const firstItem = props.breadcrumbs[0];
    const lastItem = props.breadcrumbs[props.breadcrumbs.length - 1];
    
    if (maxItems.value === 2) {
        return {
            items: [firstItem, lastItem],
            showEllipsis: true,
            ellipsisIndex: 1
        };
    }
    
    if (maxItems.value === 3) {
        return {
            items: [firstItem, lastItem],
            showEllipsis: true,
            ellipsisIndex: 1
        };
    }

    // Pour maxItems >= 4, afficher premier + ellipsis + avant-dernier + dernier
    const secondToLastItem = props.breadcrumbs[props.breadcrumbs.length - 2];
    return {
        items: [firstItem, secondToLastItem, lastItem],
        showEllipsis: true,
        ellipsisIndex: 1
    };
});
</script>

<template>
    <Breadcrumb>
        <BreadcrumbList class="flex-wrap sm:flex-nowrap">
            <template v-for="(item, index) in displayBreadcrumbs.items" :key="index">
                <!-- Ellipsis avant cet élément si nécessaire -->
                <template v-if="displayBreadcrumbs.showEllipsis && index === displayBreadcrumbs.ellipsisIndex">
                    <BreadcrumbItem>
                        <BreadcrumbEllipsis />
                    </BreadcrumbItem>
                    <BreadcrumbSeparator />
                </template>

                <BreadcrumbItem>
                    <template v-if="index === displayBreadcrumbs.items.length - 1">
                        <BreadcrumbPage class="line-clamp-1 max-w-[150px] truncate sm:max-w-[200px] md:max-w-none" :title="item.title">
                            {{ item.title }}
                        </BreadcrumbPage>
                    </template>
                    <template v-else>
                        <BreadcrumbLink as-child>
                            <Link 
                                :href="item.href ?? '#'" 
                                class="line-clamp-1 max-w-[100px] truncate sm:max-w-[150px] md:max-w-[200px] lg:max-w-none hover:max-w-none hover:overflow-visible hover:whitespace-normal transition-all"
                                :title="item.title"
                            >
                                {{ item.title }}
                            </Link>
                        </BreadcrumbLink>
                    </template>
                </BreadcrumbItem>
                
                <BreadcrumbSeparator v-if="index !== displayBreadcrumbs.items.length - 1" />
            </template>
        </BreadcrumbList>
    </Breadcrumb>
</template>
