<template>
    <v-menu v-model="menu" :close-on-content-click="false" location="bottom">
        <template v-slot:activator="{ props }">
            <v-btn v-bind="props" icon variant="text" size="large">
                <v-badge :content="unreadCount" :value="unreadCount" color="error" overlap>
                    <v-icon>mdi-bell-outline</v-icon>
                </v-badge>
            </v-btn>
        </template>

        <v-card min-width="300" max-width="400" class="notification-menu">
            <v-card-title class="d-flex align-center">
                Notifications
                <v-spacer></v-spacer>
                <v-btn v-if="hasUnread" variant="text" density="comfortable" @click="markAllAsRead">
                    Mark all as read
                </v-btn>
            </v-card-title>

            <v-divider></v-divider>

            <v-list class="notification-list" max-height="400" style="overflow-y: auto">
                <v-list-item v-if="notificationItems.length === 0">
                    <v-list-item-title class="text-center py-4 text-grey">
                        No notifications
                    </v-list-item-title>
                </v-list-item>

                <v-list-item v-for="notification in notificationItems" :key="notification.id" :value="notification.id"
                    :class="{ 'unread': !notification.read_at }" @click="handleNotificationClick(notification)">
                    <template v-slot:prepend>
                        <v-avatar :color="getNotificationColor(notification)" size="36" class="mr-3">
                            <v-icon color="white">{{ getNotificationIcon(notification) }}</v-icon>
                        </v-avatar>
                    </template>

                    <div class="text-decoration-none">
                        <v-list-item-title class="text-subtitle-1">
                            {{ getNotificationTitle(notification) }}
                        </v-list-item-title>
                        <v-list-item-subtitle>
                            {{ getNotificationContent(notification) }}
                        </v-list-item-subtitle>
                        <div class="text-caption mt-1 text-grey">
                            {{ formatNotificationDate(notification.created_at) }}
                        </div>
                    </div>

                    <template v-slot:append>
                        <v-btn icon variant="text" size="x-small" color="grey"
                            @click.stop="markAsReadOnly(notification.id)" v-if="!notification.read_at">
                            <v-icon>mdi-check</v-icon>
                        </v-btn>
                    </template>
                </v-list-item>
            </v-list>

            <v-divider></v-divider>

            <v-card-actions class="justify-center">
                <Link :href="route('dashboard.notifications.index')" class="text-decoration-none">
                <v-btn variant="text" color="primary" block>View All Notifications</v-btn>
                </Link>
            </v-card-actions>
        </v-card>
    </v-menu>
</template>

<script setup>
    import { ref, computed, onMounted } from 'vue';
    import { Link, router } from '@inertiajs/vue3';
    import { useNotifications } from '@/Composables/useNotifications';
    import axios from 'axios';

    const props = defineProps({
        initialNotifications: {
            type: [Array, Object],
            default: () => []
        },
        initialUnreadCount: {
            type: Number,
            default: 0
        }
    });

    const menu = ref(false);

    // Extract notifications from either array or pagination object
    const extractNotifications = (source) => {
        if (!source) return [];
        if (Array.isArray(source)) return source;
        if (source.data && Array.isArray(source.data)) return source.data;
        return [];
    };

    const { notifications, unreadCount, fetchNotifications, markAsRead, markAllAsRead } = useNotifications(
        extractNotifications(props.initialNotifications),
        props.initialUnreadCount || 0
    );

    // Create a computed property for notifications that handles both array and object formats
    const notificationItems = computed(() => {
        return notifications.value || [];
    });

    const hasUnread = computed(() => unreadCount.value > 0);

    const formatCurrency = (amount) => {
        if (!amount) return '0.00';
        return parseFloat(amount).toFixed(2);
    };

    // Get notification title based on type
    const getNotificationTitle = (notification) => {
        if (!notification || !notification.data) return 'Notification';

        if (notification.data && notification.data.order_uuid) {
            return `New Order #${notification.data.order_uuid.slice(-8).toUpperCase()}`;
        }
        // Default title for other notification types
        return notification.data.title || 'Notification';
    };

    // Get notification content based on type
    const getNotificationContent = (notification) => {
        if (!notification || !notification.data) return '';

        if (notification.data && notification.data.order_uuid) {
            return `$${formatCurrency(notification.data.total_amount)} - ${notification.data.customer_name || 'Customer'}`;
        }
        // Default content for other notification types
        return notification.data.message || '';
    };

    // Get icon based on notification type
    const getNotificationIcon = (notification) => {
        if (!notification || !notification.data) return 'mdi-bell-outline';

        if (notification.data && notification.data.order_uuid) {
            return 'mdi-shopping';
        }
        // Default icon
        return 'mdi-bell-outline';
    };

    // Get color based on notification type
    const getNotificationColor = (notification) => {
        if (!notification || !notification.data) return 'info';

        if (notification.data && notification.data.order_uuid) {
            return 'primary';
        }
        return 'info';
    };

    // Handle notification click based on type
    const handleNotificationClick = (notification) => {
        if (!notification) return;

        if (!notification.read_at) {
            // Mark as read first
            markAsReadOnly(notification.id, () => {
                navigateToNotificationTarget(notification);
            });
        } else {
            // If already read, just navigate
            navigateToNotificationTarget(notification);
        }
    };

    // Navigate based on notification type
    const navigateToNotificationTarget = (notification) => {
        if (!notification || !notification.data) return;

        menu.value = false; // Close the dropdown

        if (notification.data && notification.data.order_uuid) {
            router.visit(route('dashboard.orders.show', notification.data.order_uuid));
        } else if (notification.data && notification.data.url) {
            router.visit(notification.data.url);
        }
    };

    // Add a function to mark a notification as read without navigating
    const markAsReadOnly = (id, callback = null) => {
        if (!id) return;

        axios.post(route('dashboard.notifications.read', id))
            .then(() => {
                // Update local state
                const index = notifications.value.findIndex(n => n.id === id);
                if (index !== -1) {
                    notifications.value[index].read_at = new Date().toISOString();
                    unreadCount.value = Math.max(0, unreadCount.value - 1);
                }

                // Execute callback if provided
                if (callback && typeof callback === 'function') {
                    callback();
                }
            })
            .catch(error => {
                console.error('Error marking notification as read:', error);
            });
    };

    const formatNotificationDate = (dateString) => {
        if (!dateString) return '';

        const date = new Date(dateString);
        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / (1000 * 60));
        const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
        const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));

        if (diffMins < 60) {
            return `${diffMins} minute${diffMins !== 1 ? 's' : ''} ago`;
        } else if (diffHours < 24) {
            return `${diffHours} hour${diffHours !== 1 ? 's' : ''} ago`;
        } else if (diffDays < 7) {
            return `${diffDays} day${diffDays !== 1 ? 's' : ''} ago`;
        } else {
            return date.toLocaleDateString();
        }
    };

    onMounted(() => {
        // Refresh notifications every minute
        const intervalId = setInterval(() => {
            fetchNotifications();
        }, 60000);

        // Clean up on unmount
        return () => clearInterval(intervalId);
    });
</script>

<style scoped>
    .notification-list {
        max-height: 400px;
        overflow-y: auto;
    }

    .notification-menu {
        padding: 0;
    }

    .unread {
        background-color: rgb(232, 240, 254);
    }
</style>
