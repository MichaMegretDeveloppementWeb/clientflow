import type { Event } from '@/types/projects/events'

/**
 * Composable pour les fonctions de formatage partagées
 * entre les composants de détail projet
 */
export function useProjectDetailFormatters() {
    
    /**
     * Format currency avec espaces fines personnalisées
     */
    const formatCurrency = (amount: number | string | null | undefined): string => {
        if (amount === null || amount === undefined) return '0,00 €'
        const num = typeof amount === 'string' ? parseFloat(amount) : amount
        if (isNaN(num)) return '0,00 €'
        return new Intl.NumberFormat('fr-FR', {
            style: 'currency',
            currency: 'EUR',
        }).format(num).replace(/[\u202F\u00A0]/g, '\u2004')
    }

    /**
     * Format date au format français
     */
    const formatDate = (date: string | null | undefined): string => {
        if (!date) return ''
        const d = new Date(date)
        if (isNaN(d.getTime())) return ''
        return d.toLocaleDateString('fr-FR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
        })
    }

    /**
     * Tronque le texte avec ellipses
     */
    const truncateText = (text: string, maxLength: number): string => {
        if (!text) return ''
        return text.length > maxLength ? `${text.slice(0, maxLength)}...` : text
    }

    /**
     * Calcule les jours jusqu'à une date
     */
    const getDaysUntil = (date: string | null | undefined): number => {
        if (!date) return 0
        const today = new Date()
        today.setHours(0, 0, 0, 0)
        const targetDate = new Date(date)
        if (isNaN(targetDate.getTime())) return 0
        targetDate.setHours(0, 0, 0, 0)
        const diffTime = targetDate.getTime() - today.getTime()
        return Math.floor(diffTime / (1000 * 60 * 60 * 24))
    }

    /**
     * Obtient la date pertinente d'un événement selon son type
     */
    const getEventDate = (event: Event | null | undefined): string | null => {
        if (!event) return null
        if (event.event_type === 'step') {
            return event.execution_date || null
        } else if (event.event_type === 'billing') {
            return event.send_date || null
        }
        return null
    }

    return {
        formatCurrency,
        formatDate,
        truncateText,
        getDaysUntil,
        getEventDate
    }
}