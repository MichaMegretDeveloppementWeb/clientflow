import { ref, onMounted } from 'vue'

/**
 * Composable pour gérer les états de skeleton loading avec chargement différé
 * Affiche immédiatement les skeletons puis charge les vraies données en AJAX
 */

export function useSkeletonLoader(skeletonMode: boolean = false, initialDelay = 200, loadDataCallback?: () => Promise<void>) {
    const isInitialLoading = ref(skeletonMode)
    const isLoadingRealData = ref(skeletonMode)
    const isHydrated = ref(false)

    onMounted(() => {
        isHydrated.value = true

        // Si on est en mode skeleton, charger immédiatement les vraies données
        if (skeletonMode) {
            loadRealData()
        } else {
            // Mode normal : petit délai pour la transition
            setTimeout(() => {
                isInitialLoading.value = false
            }, initialDelay)
        }
    })

    const loadRealData = async () => {
        // Petit délai pour laisser les skeletons s'afficher
        await new Promise(resolve => setTimeout(resolve, 100))

        try {

            if (loadDataCallback) {
                // Utiliser le callback fourni (méthode loadClients du listState)
                await loadDataCallback()
            } else {
                console.warn('No load data callback provided to skeleton loader')
            }

            isInitialLoading.value = false
            isLoadingRealData.value = false

        } catch (error) {
            console.error('Erreur lors du chargement différé:', error)
            isLoadingRealData.value = false
            isInitialLoading.value = false
        }
    }

    const showSkeleton = (hasData: boolean, isLoading: boolean) => {
        // Afficher skeleton si :
        // 1. Chargement initial en mode skeleton
        // 2. En cours de chargement des vraies données
        // 3. État de chargement explicite lors des navigations
        return isInitialLoading.value || isLoadingRealData.value || isLoading
    }

    const showContent = (hasData: boolean, isLoading: boolean) => {
        // Afficher le contenu si on a des données et qu'on n'est pas en skeleton
        return hasData && !showSkeleton(hasData, isLoading)
    }

    const showEmpty = (hasData: boolean, isLoading: boolean) => {
        // Afficher l'état vide si on n'a pas de données et qu'on n'est pas en skeleton
        return !hasData && !showSkeleton(hasData, isLoading) && isHydrated.value
    }

    return {
        isInitialLoading,
        isHydrated,
        showSkeleton,
        showContent,
        showEmpty
    }
}
