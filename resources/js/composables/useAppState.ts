import { ref, computed } from 'vue';

interface Notification {
    id: string;
    type: 'success' | 'error' | 'warning' | 'info';
    title: string;
    message?: string;
    duration?: number;
}

// État global simple sans Pinia
const isLoading = ref(false);
const sidebarCollapsed = ref(false);
const notifications = ref<Notification[]>([]);
const theme = ref<'light' | 'dark' | 'system'>('system');

export function useAppState() {
    // Getters
    const hasNotifications = computed(() => notifications.value.length > 0);
    const unreadNotificationsCount = computed(() => notifications.value.length);

    // Actions simples
    const setLoading = (loading: boolean) => {
        isLoading.value = loading;
    };

    const toggleSidebar = () => {
        sidebarCollapsed.value = !sidebarCollapsed.value;
        localStorage.setItem('sidebar-collapsed', sidebarCollapsed.value.toString());
    };

    const setSidebarCollapsed = (collapsed: boolean) => {
        sidebarCollapsed.value = collapsed;
        localStorage.setItem('sidebar-collapsed', collapsed.toString());
    };

    const addNotification = (notification: Omit<Notification, 'id'>) => {
        const id = Date.now().toString() + Math.random().toString(36).substr(2, 9);
        const newNotification: Notification = {
            id,
            duration: 5000,
            ...notification
        };
        
        notifications.value.push(newNotification);
        
        if (newNotification.duration && newNotification.duration > 0) {
            setTimeout(() => {
                removeNotification(id);
            }, newNotification.duration);
        }
        
        return id;
    };

    const removeNotification = (id: string) => {
        const index = notifications.value.findIndex(n => n.id === id);
        if (index > -1) {
            notifications.value.splice(index, 1);
        }
    };

    const clearNotifications = () => {
        notifications.value = [];
    };

    const setTheme = (newTheme: 'light' | 'dark' | 'system') => {
        theme.value = newTheme;
        localStorage.setItem('theme', newTheme);
    };

    // Initialisation simple
    const initialize = () => {
        const savedSidebarState = localStorage.getItem('sidebar-collapsed');
        if (savedSidebarState !== null) {
            sidebarCollapsed.value = savedSidebarState === 'true';
        }

        const savedTheme = localStorage.getItem('theme');
        if (savedTheme && ['light', 'dark', 'system'].includes(savedTheme)) {
            theme.value = savedTheme as 'light' | 'dark' | 'system';
        }
    };

    // Notifications helpers
    const notifySuccess = (title: string, message?: string) => {
        return addNotification({ type: 'success', title, message });
    };

    const notifyError = (title: string, message?: string) => {
        return addNotification({ type: 'error', title, message, duration: 0 });
    };

    const notifyWarning = (title: string, message?: string) => {
        return addNotification({ type: 'warning', title, message });
    };

    const notifyInfo = (title: string, message?: string) => {
        return addNotification({ type: 'info', title, message });
    };

    return {
        // État
        isLoading,
        sidebarCollapsed,
        notifications,
        theme,

        // Getters
        hasNotifications,
        unreadNotificationsCount,

        // Actions
        setLoading,
        toggleSidebar,
        setSidebarCollapsed,
        addNotification,
        removeNotification,
        clearNotifications,
        setTheme,
        initialize,

        // Notifications
        notifySuccess,
        notifyError,
        notifyWarning,
        notifyInfo
    };
}