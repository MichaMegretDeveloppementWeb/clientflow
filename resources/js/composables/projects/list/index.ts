/**
 * Exports unifiés pour les composables de liste projets
 * Architecture orchestrée avec séparation des responsabilités
 */

// Orchestrateur principal
export { useProjectListManager } from './useProjectListManager'

// Composables spécialisés
export { useProjectFilters } from './useProjectFilters'
export { useProjectPagination } from './useProjectPagination'
export { useProjectActions } from './useProjectActions'

// Types
export type * from '@/types/projects/list'