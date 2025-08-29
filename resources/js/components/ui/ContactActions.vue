<template>
    <div class="flex items-center gap-1">
        <!-- Action email -->
        <button
            v-if="email"
            @click.stop="handleEmailClick"
            class="p-1.5 rounded-md text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-all duration-200"
            :title="`Envoyer un email à ${email}`"
        >
            <Icon name="mail" class="h-4 w-4" />
        </button>
        
        <!-- Action téléphone -->
        <button
            v-if="phone"
            @click.stop="handlePhoneClick"
            class="p-1.5 rounded-md text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-all duration-200"
            :title="`Appeler ${formatPhoneNumber(phone)}`"
        >
            <Icon name="phone" class="h-4 w-4" />
        </button>
    </div>
</template>

<script setup lang="ts">
import Icon from '@/components/Icon.vue'

interface Props {
    email?: string
    phone?: string
}

const props = defineProps<Props>()

const formatPhoneNumber = (phone: string): string => {
    if (!phone || phone.length < 10) return phone
    return phone.replace(/(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/, '$1 $2 $3 $4 $5')
}

const handleEmailClick = () => {
    if (props.email) {
        window.location.href = `mailto:${props.email}`
    }
}

const handlePhoneClick = () => {
    if (props.phone) {
        window.location.href = `tel:${props.phone}`
    }
}
</script>