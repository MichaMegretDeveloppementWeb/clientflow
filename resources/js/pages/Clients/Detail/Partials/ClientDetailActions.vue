<template>
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <Button variant="outline" as-child class="border-gray-300 hover:bg-gray-50">
                <Link :href="route('clients.edit', client.id)">
                    <Icon name="edit" class="mr-2 h-4 w-4" />
                    Modifier
                </Link>
            </Button>
            <Button as-child class="bg-primary text-primary-foreground hover:bg-primary/90">
                <Link :href="route('projects.create', { client_id: client.id })">
                    <Icon name="plus" class="mr-2 h-4 w-4" />
                    Nouveau projet
                </Link>
            </Button>
        </div>
    </div>
</template>

<script setup lang="ts">
import Icon from '@/components/Icon.vue'
import { Button } from '@/components/ui/button'
import { Link } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import type { Client } from '@/types/clients/detail'

interface Props {
    client: Client
}

defineProps<Props>()
</script>