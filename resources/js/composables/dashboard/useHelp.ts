import { ref, computed } from 'vue'
import type { Help, HelpResponse } from '@/types/dashboard/help'
import { route } from 'ziggy-js'

export function useHelp() {
    const help = ref<Help | null>(null)
    const isLoading = ref(false)
    const error = ref<string | null>(null)

    const hasHelp = computed(() => help.value !== null)

    const loadHelp = async (): Promise<void> => {
        isLoading.value = true
        error.value = null

        try {
            const response = await fetch(route('dashboard.help'), {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`)
            }

            const data: HelpResponse = await response.json()
            help.value = data.help
        } catch (err) {
            console.error('Error loading help data:', err)
            error.value = err instanceof Error ? err.message : 'Failed to load help data'
            help.value = null
        } finally {
            isLoading.value = false
        }
    }

    const refreshHelp = async (): Promise<void> => {
        await loadHelp()
    }

    const handleTipClick = (tipRoute: string): void => {
        window.location.href = route(tipRoute)
    }

    const handleActionClick = (actionId: string): void => {
        switch (actionId) {
            case 'guide':
                // TODO: Ouvrir le guide d'onboarding
                console.log('Opening guide...')
                break
            case 'demo':
                // TODO: Ouvrir la vidéo de démo
                console.log('Opening demo...')
                break
            default:
                console.warn(`Unknown action: ${actionId}`)
        }
    }

    return {
        help,
        isLoading,
        error,
        hasHelp,
        loadHelp,
        refreshHelp,
        handleTipClick,
        handleActionClick,
    }
}