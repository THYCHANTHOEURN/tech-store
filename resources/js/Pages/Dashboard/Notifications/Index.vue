<template>

    <Head title="Notifications" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Notifications
                </h2>
                <v-spacer></v-spacer>
                <v-btn v-if="hasUnread" color="primary" variant="outlined" @click="markAllAsRead"
                    prepend-icon="mdi-check-all">
                    Mark all as read
                </v-btn>
            </div>
        </template>

        <v-container fluid class="py-8">
            <v-row>
                <v-col cols="12">
                    <v-card>
                        <v-card-title class="d-flex align-center">
                            <v-icon class="mr-2">mdi-bell-outline</v-icon>
                            All Notifications
                        </v-card-title>

                        <v-divider></v-divider>

                        <v-card-text v-if="notifications.data.length === 0" class="text-center py-8">
                            <v-icon size="large" color="grey" class="mb-4">mdi-bell-off-outline</v-icon>
                            <p class="text-h6 text-grey">No notifications yet</p>
                        </v-card-text>

                        <template v-else>
                            <v-list two-line>
                                <v-list-item v-for="notification in notifications.data" :key="notification.id"
                                    :class="{ 'unread': !notification.read_at }" :value="notification.id">
                                    <template v-slot:prepend>
                                        <v-avatar color="primary" class="mr-3">
                                            <v-icon color="white">mdi-shopping</v-icon>
                                        </v-avatar>
                                    </template>

                                    <Link :href="route('dashboard.orders.show', notification.data.order_uuid)"
                                        class="text-decoration-none" @click="markAsRead(notification.id)">
                                    <v-list-item-title class="text-subtitle-1">
                                        New Order #{{ notification.data.order_uuid.slice(-8).toUpperCase() }}
                                    </v-list-item-title>
                                    <v-list-item-subtitle>
                                        ${{ formatCurrency(notification.data.total_amount) }} - {{
                                            notification.data.customer_name }}
                                    </v-list-item-subtitle>
                                    <div class="text-caption mt-1 text-grey">
                                        {{ formatDate(notification.created_at) }}
                                    </div>
                                    </Link>

                                    <template v-slot:append>
                                        <v-btn icon variant="text" color="grey" @click="markAsRead(notification.id)"
                                            v-if="!notification.read_at">
                                            <v-icon>mdi-check</v-icon>
                                        </v-btn>
                                    </template>
                                </v-list-item>
                            </v-list>

                            <!-- Pagination -->
                            <div class="d-flex justify-center py-4">
                                <v-pagination v-if="notifications.last_page > 1" v-model="page"
                                    :length="notifications.last_page" total-visible="7" @update:model-value="changePage"
                                    rounded></v-pagination>
                            </div>
                        </template>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </DashboardLayout>
</template>

<script setup>
    import { ref, computed } from 'vue';
    import { Head, Link, router } from '@inertiajs/vue3';
    import DashboardLayout from '@/Layouts/DashboardLayout.vue';

    const props = defineProps({
        notifications: Object
    });

    const page = ref(props.notifications.current_page || 1);
    const hasUnread = computed(() => props.notifications.data.some(n => !n.read_at));

    // Format functions
    const formatCurrency = (amount) => {
        return parseFloat(amount).toFixed(2);
    };

    const formatDate = (dateString) => {
        const options = { year: 'numeric', month: 'short', day: 'numeric', hour: 'numeric', minute: 'numeric' };
        return new Date(dateString).toLocaleDateString(undefined, options);
    };

    // Notification functions
    const markAsRead = (id) => {
        router.post(route('dashboard.notifications.read', id), {}, {
            preserveScroll: true
        });
    };

    const markAllAsRead = () => {
        router.post(route('dashboard.notifications.readAll'), {}, {
            preserveScroll: true
        });
    };

    // Pagination
    const changePage = (newPage) => {
        router.get(route('dashboard.notifications.index', { page: newPage }), {}, {
            preserveState: true,
            replace: true
        });
    };
</script>

<style scoped>
    .unread {
        background-color: rgb(232, 240, 254);
    }
</style>
