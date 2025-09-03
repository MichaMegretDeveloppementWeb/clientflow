// Utilitaires pour l'optimisation des assets (images, icônes, etc.)

interface AssetCacheEntry {
    blob: Blob;
    timestamp: number;
    url: string;
    size: number;
    hits: number;
}

interface ImageOptimizationOptions {
    quality?: number;
    format?: 'webp' | 'avif' | 'jpeg' | 'png';
    width?: number;
    height?: number;
    progressive?: boolean;
}

// eslint-disable-next-line @typescript-eslint/no-unused-vars
interface IconOptimizationOptions {
    size?: number;
    strokeWidth?: number;
    color?: string;
    cache?: boolean;
}

class AssetOptimizer {
    private cache = new Map<string, AssetCacheEntry>();
    private readonly maxCacheSize = 50 * 1024 * 1024; // 50MB
    private readonly maxCacheAge = 3600000; // 1 heure
    private currentCacheSize = 0;

    // Optimiser et mettre en cache une image
    async optimizeImage(src: string, options: ImageOptimizationOptions = {}): Promise<string> {
        const cacheKey = this.generateCacheKey(src, options);
        
        // Vérifier le cache
        const cached = this.getFromCache(cacheKey);
        if (cached) {
            return cached.url;
        }

        try {
            // Charger l'image originale
            const response = await fetch(src);
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            
            const blob = await response.blob();
            const optimizedBlob = await this.processImage(blob, options);
            
            // Créer l'URL optimisée
            const optimizedUrl = URL.createObjectURL(optimizedBlob);
            
            // Mettre en cache
            this.addToCache(cacheKey, {
                blob: optimizedBlob,
                timestamp: Date.now(),
                url: optimizedUrl,
                size: optimizedBlob.size,
                hits: 1
            });
            
            return optimizedUrl;
        } catch (error) {
            console.error('Erreur lors de l\'optimisation de l\'image:', error);
            return src; // Retourner l'URL originale en cas d'erreur
        }
    }

    // Traiter une image avec Canvas
    private async processImage(blob: Blob, options: ImageOptimizationOptions): Promise<Blob> {
        return new Promise((resolve, reject) => {
            const img = new Image();
            
            img.onload = () => {
                try {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    
                    if (!ctx) {
                        throw new Error('Impossible de créer le contexte Canvas');
                    }
                    
                    // Calculer les nouvelles dimensions
                    const { width, height } = this.calculateDimensions(
                        img.width,
                        img.height,
                        options.width,
                        options.height
                    );
                    
                    canvas.width = width;
                    canvas.height = height;
                    
                    // Dessiner l'image redimensionnée
                    ctx.drawImage(img, 0, 0, width, height);
                    
                    // Convertir en blob avec les options
                    canvas.toBlob(
                        (optimizedBlob) => {
                            if (optimizedBlob) {
                                resolve(optimizedBlob);
                            } else {
                                reject(new Error('Échec de la conversion en blob'));
                            }
                        },
                        this.getMimeType(options.format),
                        (options.quality || 80) / 100
                    );
                } catch (error) {
                    reject(error);
                }
            };
            
            img.onerror = () => reject(new Error('Échec du chargement de l\'image'));
            img.src = URL.createObjectURL(blob);
        });
    }

    // Calculer les dimensions optimales
    private calculateDimensions(
        originalWidth: number,
        originalHeight: number,
        targetWidth?: number,
        targetHeight?: number
    ): { width: number; height: number } {
        if (!targetWidth && !targetHeight) {
            return { width: originalWidth, height: originalHeight };
        }
        
        const aspectRatio = originalWidth / originalHeight;
        
        if (targetWidth && targetHeight) {
            return { width: targetWidth, height: targetHeight };
        }
        
        if (targetWidth) {
            return { width: targetWidth, height: Math.round(targetWidth / aspectRatio) };
        }
        
        if (targetHeight) {
            return { width: Math.round(targetHeight * aspectRatio), height: targetHeight };
        }
        
        return { width: originalWidth, height: originalHeight };
    }

    // Obtenir le type MIME approprié
    private getMimeType(format?: string): string {
        const mimeTypes: Record<string, string> = {
            'webp': 'image/webp',
            'avif': 'image/avif',
            'jpeg': 'image/jpeg',
            'png': 'image/png'
        };
        
        return mimeTypes[format || 'jpeg'] || 'image/jpeg';
    }

    // Précharger des images de manière intelligente
    async preloadImages(urls: string[], options: ImageOptimizationOptions = {}): Promise<string[]> {
        const preloadPromises = urls.map(async (url) => {
            try {
                return await this.optimizeImage(url, options);
            } catch (error) {
                console.warn(`Échec du préchargement de ${url}:`, error);
                return url;
            }
        });
        
        return Promise.allSettled(preloadPromises).then(results => 
            results.map((result, index) => 
                result.status === 'fulfilled' ? result.value : urls[index]
            )
        );
    }

    // Créer un placeholder SVG
    createPlaceholder(width: number, height: number, color: string = '#f3f4f6'): string {
        const svg = `
            <svg width="${width}" height="${height}" xmlns="http://www.w3.org/2000/svg">
                <rect width="100%" height="100%" fill="${color}"/>
                <text x="50%" y="50%" text-anchor="middle" dy=".3em" fill="#9ca3af" font-family="sans-serif" font-size="14">
                    ${width}×${height}
                </text>
            </svg>
        `;
        
        return `data:image/svg+xml;base64,${btoa(svg)}`;
    }

    // Créer un placeholder avec effet de chargement
    createLoadingPlaceholder(width: number, height: number): string {
        const svg = `
            <svg width="${width}" height="${height}" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="shimmer" x1="0%" y1="0%" x2="100%" y2="0%">
                        <stop offset="0%" style="stop-color:#f3f4f6;stop-opacity:1" />
                        <stop offset="50%" style="stop-color:#e5e7eb;stop-opacity:1" />
                        <stop offset="100%" style="stop-color:#f3f4f6;stop-opacity:1" />
                        <animateTransform
                            attributeName="gradientTransform"
                            type="translate"
                            values="-100 0;100 0;-100 0"
                            dur="2s"
                            repeatCount="indefinite"/>
                    </linearGradient>
                </defs>
                <rect width="100%" height="100%" fill="url(#shimmer)"/>
            </svg>
        `;
        
        return `data:image/svg+xml;base64,${btoa(svg)}`;
    }

    // Gestion du cache
    private generateCacheKey(src: string, options: any): string {
        return `${src}_${JSON.stringify(options)}`;
    }

    private getFromCache(key: string): AssetCacheEntry | null {
        const entry = this.cache.get(key);
        
        if (!entry) return null;
        
        // Vérifier l'âge
        if (Date.now() - entry.timestamp > this.maxCacheAge) {
            this.removeFromCache(key);
            return null;
        }
        
        // Incrémenter les hits
        entry.hits++;
        return entry;
    }

    private addToCache(key: string, entry: AssetCacheEntry): void {
        // Vérifier si on dépasse la taille maximale
        if (this.currentCacheSize + entry.size > this.maxCacheSize) {
            this.cleanupCache();
        }
        
        this.cache.set(key, entry);
        this.currentCacheSize += entry.size;
    }

    private removeFromCache(key: string): void {
        const entry = this.cache.get(key);
        if (entry) {
            URL.revokeObjectURL(entry.url);
            this.currentCacheSize -= entry.size;
            this.cache.delete(key);
        }
    }

    private cleanupCache(): void {
        // Supprimer les entrées les plus anciennes et les moins utilisées
        const entries = Array.from(this.cache.entries());
        
        // Trier par âge et nombre de hits
        entries.sort(([,a], [,b]) => {
            const scoreA = a.hits / (Date.now() - a.timestamp);
            const scoreB = b.hits / (Date.now() - b.timestamp);
            return scoreA - scoreB;
        });
        
        // Supprimer la moitié des entrées
        const toRemove = entries.slice(0, Math.ceil(entries.length / 2));
        toRemove.forEach(([key]) => this.removeFromCache(key));
    }

    // Métriques du cache
    getCacheMetrics() {
        const totalEntries = this.cache.size;
        const totalSize = this.currentCacheSize;
        const avgHits = totalEntries > 0 
            ? Array.from(this.cache.values()).reduce((sum, entry) => sum + entry.hits, 0) / totalEntries
            : 0;
        
        return {
            entries: totalEntries,
            size: totalSize,
            sizeFormatted: this.formatBytes(totalSize),
            maxSize: this.maxCacheSize,
            maxSizeFormatted: this.formatBytes(this.maxCacheSize),
            usage: (totalSize / this.maxCacheSize) * 100,
            averageHits: avgHits
        };
    }

    private formatBytes(bytes: number): string {
        if (bytes === 0) return '0 B';
        
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        
        return `${parseFloat((bytes / Math.pow(k, i)).toFixed(2))} ${sizes[i]}`;
    }

    // Nettoyer complètement le cache
    clearCache(): void {
        this.cache.forEach((entry) => {
            URL.revokeObjectURL(entry.url);
        });
        this.cache.clear();
        this.currentCacheSize = 0;
    }
}

// Instance globale
export const assetOptimizer = new AssetOptimizer();

// Utilitaires pour la détection de support de formats
export const formatSupport = {
    // Vérifier le support WebP
    async supportsWebP(): Promise<boolean> {
        return new Promise((resolve) => {
            const webP = new Image();
            webP.onload = webP.onerror = () => {
                resolve(webP.height === 2);
            };
            webP.src = 'data:image/webp;base64,UklGRjoAAABXRUJQVlA4IC4AAACyAgCdASoCAAIALmk0mk0iIiIiIgBoSygABc6WWgAA/veff/0PP8bA//LwYAAA';
        });
    },

    // Vérifier le support AVIF
    async supportsAvif(): Promise<boolean> {
        return new Promise((resolve) => {
            const avif = new Image();
            avif.onload = avif.onerror = () => {
                resolve(avif.height === 2);
            };
            avif.src = 'data:image/avif;base64,AAAAIGZ0eXBhdmlmAAAAAGF2aWZtaWYxbWlhZk1BMUIAAADybWV0YQAAAAAAAAAoaGRscgAAAAAAAAAAcGljdAAAAAAAAAAAAAAAAGxpYmF2aWYAAAAADnBpdG0AAAAAAAEAAAAeaWxvYwAAAABEAAABAAEAAAABAAABGgAAAB0AAAAoaWluZgAAAAAAAQAAABppbmZlAgAAAAABAABhdjAxQ29sb3IAAAAAamlwcnAAAABLaXBjbwAAABRpc3BlAAAAAAAAAAIAAAACAAAAEHBpeGkAAAAAAwgICAAAAAxhdjFDgQ0MAAAAABNjb2xybmNseAACAAIAAYAAAAAXaXBtYQAAAAAAAAABAAEEAQKDBAAAACVtZGF0EgAKCBgABogQEAwgMg8f8D///8WfhwB8+ErK42A=';
        });
    },

    // Obtenir le meilleur format supporté
    async getBestFormat(): Promise<'avif' | 'webp' | 'jpeg'> {
        if (await this.supportsAvif()) return 'avif';
        if (await this.supportsWebP()) return 'webp';
        return 'jpeg';
    }
};

// Générateur d'URLs responsives
export function generateResponsiveUrls(
    baseSrc: string,
    sizes: number[] = [320, 640, 768, 1024, 1280, 1600],
    options: ImageOptimizationOptions = {}
): Array<{ src: string; width: number }> {
    return sizes.map(width => ({
        src: appendImageParams(baseSrc, { ...options, width }),
        width
    }));
}

// Ajouter des paramètres d'optimisation à une URL d'image
export function appendImageParams(src: string, params: ImageOptimizationOptions): string {
    if (!src || src.startsWith('data:')) return src;
    
    try {
        const url = new URL(src, window.location.origin);
        
        if (params.quality) url.searchParams.set('q', params.quality.toString());
        if (params.format) url.searchParams.set('f', params.format);
        if (params.width) url.searchParams.set('w', params.width.toString());
        if (params.height) url.searchParams.set('h', params.height.toString());
        if (params.progressive) url.searchParams.set('progressive', 'true');
        
        return url.toString();
    } catch {
        return src;
    }
}

// Précharger des images critiques
export async function preloadCriticalImages(urls: string[]): Promise<void> {
    const preloadPromises = urls.map(url => {
        return new Promise<void>((resolve, reject) => {
            const img = new Image();
            img.onload = () => resolve();
            img.onerror = () => reject(new Error(`Failed to preload ${url}`));
            img.src = url;
        });
    });
    
    try {
        await Promise.allSettled(preloadPromises);
    } catch (error) {
        console.warn('Some critical images failed to preload:', error);
    }
}

export default assetOptimizer;