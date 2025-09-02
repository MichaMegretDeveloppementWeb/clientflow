/**
 * Utilitaires pour la comparaison intelligente des statistiques
 * Utilisé pour éviter les rechargements inutiles des cartes de stats
 */

/**
 * Compare deux objets de statistiques pour détecter les changements
 * @param oldStats - Anciennes statistiques
 * @param newStats - Nouvelles statistiques
 * @returns true si les stats sont identiques, false si elles ont changé
 */
export function areStatsEqual(oldStats: any, newStats: any): boolean {
    // Si l'un des deux est null/undefined et pas l'autre
    if (!oldStats && !newStats) return true
    if (!oldStats || !newStats) return false
    
    // Obtenir toutes les clés des deux objets
    const oldKeys = Object.keys(oldStats)
    const newKeys = Object.keys(newStats)
    
    // Si le nombre de propriétés est différent
    if (oldKeys.length !== newKeys.length) return false
    
    // Comparer chaque propriété
    for (const key of oldKeys) {
        if (oldStats[key] !== newStats[key]) {
            return false
        }
    }
    
    return true
}

/**
 * Compare spécifiquement les statistiques de clients
 * @param oldStats - Anciennes stats clients
 * @param newStats - Nouvelles stats clients
 * @returns true si identiques
 */
export function areClientStatsEqual(oldStats: any, newStats: any): boolean {
    if (!oldStats && !newStats) return true
    if (!oldStats || !newStats) return false
    
    return oldStats.total === newStats.total &&
           oldStats.with_projects === newStats.with_projects &&
           oldStats.without_projects === newStats.without_projects
}

/**
 * Compare spécifiquement les statistiques de projets
 * @param oldStats - Anciennes stats projets
 * @param newStats - Nouvelles stats projets
 * @returns true si identiques
 */
export function areProjectStatsEqual(oldStats: any, newStats: any): boolean {
    if (!oldStats && !newStats) return true
    if (!oldStats || !newStats) return false
    
    return oldStats.total === newStats.total &&
           oldStats.active === newStats.active &&
           oldStats.completed === newStats.completed &&
           oldStats.on_hold === newStats.on_hold
}

/**
 * Compare spécifiquement les statistiques d'événements
 * @param oldStats - Anciennes stats événements
 * @param newStats - Nouvelles stats événements
 * @returns true si identiques
 */
export function areEventStatsEqual(oldStats: any, newStats: any): boolean {
    if (!oldStats && !newStats) return true
    if (!oldStats || !newStats) return false
    
    return oldStats.total === newStats.total &&
           oldStats.todo === newStats.todo &&
           oldStats.done === newStats.done &&
           oldStats.overdue === newStats.overdue
}