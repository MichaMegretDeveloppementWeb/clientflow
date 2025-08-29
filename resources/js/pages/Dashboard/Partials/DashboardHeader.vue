<template>
    <div class="border-b border-gray-100 pb-8">
        <!-- Main header content -->
        <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="mb-2 flex items-center gap-3">
                    <div class="rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 p-2 shadow-sm">
                        <OptimizedIcon name="bar-chart-3" :size="24" color="white" preload />
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                            Tableau de bord
                        </h1>
                    </div>
                </div>
                <p class="max-w-2xl leading-relaxed text-gray-600">
                    Bienvenue dans votre espace de gestion.
                </p>
            </div>

            <div class="flex flex-col gap-2 sm:flex-row">

                <!-- Actions rapides dropdown -->
                <div class="relative" data-dropdown="actions-rapides">
                    <Button
                        @click="showActionsDropdown = !showActionsDropdown"
                        class="w-full border-0 bg-gradient-to-r from-blue-600 to-blue-700 shadow-sm hover:from-blue-700 hover:to-blue-800 sm:w-auto"
                        :class="{ 'ring-2 ring-blue-500 ring-offset-2': showActionsDropdown }"
                        :disabled="isLoading"
                    >
                        <OptimizedIcon name="zap" :size="16" class="mr-2" preload />
                        Actions rapides
                        <OptimizedIcon
                            name="chevron-down"
                            :size="16"
                            class="ml-2 transition-transform"
                            :class="{ 'rotate-180': showActionsDropdown }"
                            preload
                        />
                    </Button>

                    <!-- Dropdown menu -->
                    <Transition
                        enter-active-class="transition ease-out duration-200"
                        enter-from-class="transform opacity-0 scale-95"
                        enter-to-class="transform opacity-100 scale-100"
                        leave-active-class="transition ease-in duration-75"
                        leave-from-class="transform opacity-100 scale-100"
                        leave-to-class="transform opacity-0 scale-95"
                    >
                        <div
                            v-if="showActionsDropdown"
                            class="absolute right-0 z-50 mt-2 w-56 rounded-xl border border-gray-100 bg-white py-2 shadow-lg"
                            @click.stop
                        >
                            <QuickActionItem
                                :href="route('clients.create')"
                                icon="user-plus"
                                title="Nouveau client"
                                description="Ajouter un nouveau client"
                                color="blue"
                                @click="closeDropdown"
                            />

                            <QuickActionItem
                                :href="route('projects.create')"
                                icon="folder-plus"
                                title="Nouveau projet"
                                description="Créer un nouveau projet"
                                color="purple"
                                @click="closeDropdown"
                            />

                            <QuickActionItem
                                :href="route('events.create')"
                                icon="calendar"
                                title="Nouvel événement"
                                description="Créer un événement"
                                color="emerald"
                                @click="closeDropdown"
                            />

                            <div class="my-2 border-t border-gray-100"></div>

                            <QuickActionItem
                                :href="route('clients.index')"
                                icon="user"
                                title="Voir les clients"
                                description="Liste des clients"
                                color="blue"
                                @click="closeDropdown"
                            />

                            <QuickActionItem
                                :href="route('projects.index')"
                                icon="folder-kanban"
                                title="Voir les projets"
                                description="Gérer tous les projets"
                                color="purple"
                                @click="closeDropdown"
                            />

                            <QuickActionItem
                                :href="route('events.index')"
                                icon="calendar"
                                title="Voir les événements"
                                description="Consulter le planning"
                                color="emerald"
                                @click="closeDropdown"
                            />
                        </div>
                    </Transition>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted, defineComponent, h, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import OptimizedIcon from '@/components/OptimizedIcon.vue';
import RefreshButton from '@/components/RefreshButton.vue';

// Émissions
defineEmits<{
    refresh: [];
}>();

// État local
const showActionsDropdown = ref(false);
const isLoading = ref(false);

// Composant d'action rapide
const QuickActionItem = defineComponent({
    props: {
        href: String,
        icon: String,
        title: String,
        description: String,
        color: String
    },
    setup(props, { emit }) {
        const colorClasses = computed(() => {
            const colors = {
                blue: 'bg-blue-50 hover:bg-blue-100 text-blue-600',
                purple: 'bg-purple-50 hover:bg-purple-100 text-purple-600',
                emerald: 'bg-emerald-50 hover:bg-emerald-100 text-emerald-600'
            };
            return colors[props.color as keyof typeof colors] || colors.blue;
        });

        return () => h(Link, {
            href: props.href,
            class: 'group flex items-center px-4 py-3 text-sm text-gray-700 transition-colors hover:bg-gray-50',
            onClick: () => emit('click')
        }, [
            h('div', {
                class: `mr-3 flex h-8 w-8 items-center justify-center rounded-lg transition-colors ${colorClasses.value}`
            }, [
                h(OptimizedIcon, {
                    name: props.icon,
                    size: 16,
                    preload: true
                })
            ]),
            h('div', [
                h('div', { class: 'font-medium' }, props.title),
                h('div', { class: 'text-xs text-gray-500' }, props.description)
            ])
        ]);
    }
});

// Fermer le dropdown
const closeDropdown = () => {
    showActionsDropdown.value = false;
};

// Gestionnaire de clic externe
const handleClickOutside = (event: Event) => {
    const target = event.target as Element;
    if (!target.closest('[data-dropdown="actions-rapides"]')) {
        closeDropdown();
    }
};

// Lifecycle
onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<style scoped>
/* Animations pour le dropdown */
.dropdown-enter-active,
.dropdown-leave-active {
    transition: all 0.2s ease;
}

.dropdown-enter-from,
.dropdown-leave-to {
    opacity: 0;
    transform: translateY(-10px) scale(0.95);
}
</style>
