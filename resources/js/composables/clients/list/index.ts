/**
 * Point d'entrée pour tous les composables de la liste clients
 * Facilite les imports et offre une interface centralisée
 */

export { useClientFilters } from './useClientFilters'
export { useClientPagination } from './useClientPagination'
export { useClientListManager } from './useClientListManager'

// Réexport des types pour convenance
export type * from '@/types/clients/list'