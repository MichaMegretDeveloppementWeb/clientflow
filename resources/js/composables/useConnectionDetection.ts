import { ref, onMounted } from 'vue'

/**
 * Composable pour détecter la qualité de connexion
 * et adapter l'UX en conséquence
 */

export function useConnectionDetection() {
    const isSlowConnection = ref(false)
    const connectionType = ref('unknown')
    const isOnline = ref(true)

    onMounted(() => {
        // Détecter le type de connexion si disponible
        if ('connection' in navigator) {
            const connection = (navigator as any).connection || (navigator as any).mozConnection || (navigator as any).webkitConnection
            
            if (connection) {
                connectionType.value = connection.effectiveType || connection.type || 'unknown'
                
                // Connexions lentes : 2g, slow-2g
                isSlowConnection.value = ['slow-2g', '2g'].includes(connection.effectiveType)
                
                // Écouter les changements de connexion
                connection.addEventListener('change', () => {
                    connectionType.value = connection.effectiveType || connection.type || 'unknown'
                    isSlowConnection.value = ['slow-2g', '2g'].includes(connection.effectiveType)
                })
            }
        }

        // Détecter le statut en ligne/hors ligne
        isOnline.value = navigator.onLine
        
        window.addEventListener('online', () => {
            isOnline.value = true
        })
        
        window.addEventListener('offline', () => {
            isOnline.value = false
        })
    })

    // Délai de skeleton adaptatif selon la connexion
    const getOptimalSkeletonDelay = () => {
        if (!isOnline.value) return 100 // Très court si hors ligne
        if (isSlowConnection.value) return 2000 // Plus long pour connexions lentes
        if (connectionType.value === '3g') return 1000
        return 600 // Par défaut pour 4g/wifi
    }

    // Messages adaptatifs
    const getLoadingMessage = () => {
        if (!isOnline.value) return 'Connexion hors ligne détectée'
        if (isSlowConnection.value) return 'Connexion lente détectée, patience...'
        return 'Chargement en cours'
    }

    return {
        isSlowConnection,
        connectionType,
        isOnline,
        getOptimalSkeletonDelay,
        getLoadingMessage
    }
}