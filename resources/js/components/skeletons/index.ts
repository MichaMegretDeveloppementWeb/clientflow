// Export central pour tous les composants skeleton

export { default as SkeletonLoader } from './SkeletonLoader.vue';
export { default as SkeletonTable } from './SkeletonTable.vue';
export { default as SkeletonCard } from './SkeletonCard.vue';
export { default as SkeletonChart } from './SkeletonChart.vue';

// Configurations prédéfinies pour les différentes pages
export const SkeletonConfigs = {
    // Configuration pour la liste des clients
    clientsTable: {
        columns: [
            { key: 'name', width: '2fr', hasAvatar: true, cellType: 'text' as const },
            { key: 'company', width: '1.5fr', cellType: 'text' as const },
            { key: 'projects', width: '1fr', cellType: 'number' as const },
            { key: 'revenue', width: '1fr', cellType: 'number' as const },
            { key: 'status', width: '1fr', cellType: 'badge' as const },
            { key: 'actions', width: '100px', cellType: 'actions' as const, actionCount: 3 }
        ],
        rowCount: 8
    },

    // Configuration pour la liste des projets
    projectsTable: {
        columns: [
            { key: 'name', width: '2fr', cellType: 'text' as const },
            { key: 'client', width: '1.5fr', cellType: 'text' as const },
            { key: 'budget', width: '1fr', cellType: 'number' as const },
            { key: 'progress', width: '1.5fr', cellType: 'text' as const }, // Barre de progression
            { key: 'deadline', width: '1fr', cellType: 'text' as const },
            { key: 'status', width: '1fr', cellType: 'badge' as const },
            { key: 'actions', width: '100px', cellType: 'actions' as const, actionCount: 2 }
        ],
        rowCount: 6
    },

    // Configuration pour la liste des événements
    eventsTable: {
        columns: [
            { key: 'name', width: '2fr', cellType: 'text' as const },
            { key: 'project', width: '1.5fr', cellType: 'text' as const },
            { key: 'type', width: '1fr', cellType: 'badge' as const },
            { key: 'amount', width: '1fr', cellType: 'number' as const },
            { key: 'date', width: '1fr', cellType: 'text' as const },
            { key: 'status', width: '1fr', cellType: 'badge' as const },
            { key: 'actions', width: '100px', cellType: 'actions' as const, actionCount: 2 }
        ],
        rowCount: 10
    },

    // Configuration pour les cartes du dashboard
    dashboardCards: {
        statsCard: {
            hasTitle: true,
            hasStats: true,
            hasFooter: true,
            statsCount: 1,
            size: 'sm' as const,
            textLines: 0
        },
        recentActivityCard: {
            hasTitle: true,
            hasActions: true,
            textLines: 5,
            size: 'md' as const,
            actionCount: 1
        },
        projectCard: {
            hasHeader: true,
            hasAvatar: true,
            hasSubtitle: true,
            hasTags: true,
            hasActions: true,
            textLines: 2,
            tagCount: 2,
            actionCount: 2,
            size: 'md' as const
        }
    },

    // Configuration pour les graphiques
    charts: {
        revenueChart: {
            chartType: 'line' as const,
            chartHeight: '300px',
            hasSubtitle: false,
            hasControls: true,
            hasMetrics: true,
            hasLegend: false,
            controlCount: 2,
            metricCount: 1
        },
        projectsChart: {
            chartType: 'bar' as const,
            chartHeight: '250px',
            hasSubtitle: true,
            hasControls: false,
            hasMetrics: false,
            hasLegend: true,
            legendItems: 4
        },
        pieChart: {
            chartType: 'pie' as const,
            chartHeight: '300px',
            hasSubtitle: false,
            hasControls: false,
            hasMetrics: false,
            hasLegend: true,
            legendItems: 5
        }
    }
};

// Hooks et composables pour skeleton loading
export interface SkeletonState {
    isLoading: boolean;
    error: string | null;
}

// Fonction utilitaire pour créer des configurations de skeleton adaptatives
export function createSkeletonConfig(baseConfig: any, overrides: any = {}) {
    return {
        ...baseConfig,
        ...overrides
    };
}

// Types pour les configurations
export interface SkeletonTableColumn {
    key: string;
    width?: string;
    headerWidth?: string;
    cellWidth?: string;
    cellType?: 'text' | 'badge' | 'actions' | 'number';
    hasAvatar?: boolean;
    actionCount?: number;
}

export interface SkeletonTableConfig {
    columns: SkeletonTableColumn[];
    rowCount?: number;
    showPagination?: boolean;
    animated?: boolean;
}

export interface SkeletonCardConfig {
    hasImage?: boolean;
    hasHeader?: boolean;
    hasTitle?: boolean;
    hasSubtitle?: boolean;
    hasAvatar?: boolean;
    hasStats?: boolean;
    hasTags?: boolean;
    hasActions?: boolean;
    hasFooter?: boolean;
    size?: 'sm' | 'md' | 'lg';
    textLines?: number;
    statsCount?: number;
    tagCount?: number;
    actionCount?: number;
    animated?: boolean;
}

export interface SkeletonChartConfig {
    chartType?: 'line' | 'bar' | 'pie' | 'area' | 'scatter' | 'generic';
    chartHeight?: string;
    hasSubtitle?: boolean;
    hasControls?: boolean;
    hasMetrics?: boolean;
    hasLegend?: boolean;
    hasFooter?: boolean;
    controlCount?: number;
    metricCount?: number;
    animated?: boolean;
}