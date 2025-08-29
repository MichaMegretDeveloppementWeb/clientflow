/**
 * Index des types pour la liste clients
 * Point d'entrée central pour tous les types liés à la liste clients
 */

export * from './filters'
export * from './pagination'
export * from './components'

// Réexport des types de base nécessaires
export type { ClientDTO, ClientFormData } from '../../models'