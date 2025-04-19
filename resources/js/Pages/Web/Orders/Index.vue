<template>
    <WebLayout>

        <Head title="My Orders" />

        <v-container>
            <v-row>
                <v-col cols="12">
                    <h1 class="text-h4 font-weight-bold mb-6">My Orders</h1>

                    <div v-if="orders.data.length > 0">
                        <v-card class="mb-6" v-for="order in orders.data" :key="order.id">
                            <v-card-text>
                                <v-row align="center">
                                    <v-col cols="12" md="6">
                                        <div class="d-flex align-center">
                                            <v-icon size="large" color="primary" class="mr-2">mdi-shopping</v-icon>
                                            <div>
                                                <div class="text-h6 mb-1">Order #{{ order.uuid.slice(-8).toUpperCase()
                                                    }}</div>
                                                <div class="text-caption">{{ formatDate(order.created_at) }}</div>
                                            </div>
                                        </div>
                                    </v-col>

                                    <v-col cols="12" md="2">
                                        <div class="text-caption mb-1">Total Amount</div>
                                        <div class="text-subtitle-1 font-weight-bold">${{
                                            formatCurrency(order.total_amount) }}</div>
                                    </v-col>

                                    <v-col cols="12" md="2">
                                        <div class="text-caption mb-1">Status</div>
                                        <v-chip :color="getOrderStatusColor(order.status)" size="small">
                                            {{ formatOrderStatus(order.status) }}
                                        </v-chip>
                                    </v-col>

                                    <v-col cols="12" md="2" class="text-right">
                                        <Link :href="route('orders.show', order.uuid)" class="text-decoration-none">
                                        <v-btn color="primary" size="small" variant="text">
                                            View Details
                                            <v-icon end>mdi-chevron-right</v-icon>
                                        </v-btn>
                                        </Link>
                                    </v-col>
                                </v-row>
                            </v-card-text>
                        </v-card>

                        <!-- Pagination -->
                        <div class="d-flex justify-center mt-6">
                            <v-pagination v-model="page" :length="orders.last_page" @update:model-value="changePage"
                                rounded :total-visible="7"></v-pagination>
                        </div>
                    </div>

                    <v-card v-else>
                        <v-card-text class="text-center py-10">
                            <v-icon size="64" color="grey-lighten-1" class="mb-4">mdi-receipt-text-outline</v-icon>
                            <h3 class="text-h6 font-weight-bold mb-2">No Orders Yet</h3>
                            <p class="mb-4">You haven't placed any orders yet.</p>
                            <Link :href="route('index')" class="text-decoration-none">
                            <v-btn color="primary">
                                Browse Products
                            </v-btn>
                            </Link>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </WebLayout>
</template>

<script setup>
    import WebLayout from '@/Layouts/WebLayout.vue';
    import { Head, Link, router } from '@inertiajs/vue3';
    import { ref } from 'vue';

    const props = defineProps({
        orders: Object
    });

    const page = ref(props.orders.current_page);

    // Format currency
    function formatCurrency(amount) {
        return parseFloat(amount).toFixed(2);
    }

    // Format date
    function formatDate(dateString) {
        const options = { year: 'numeric', month: 'short', day: 'numeric' };
        return new Date(dateString).toLocaleDateString(undefined, options);
    }

    // Format order status
    function formatOrderStatus(status) {
        return status.charAt(0).toUpperCase() + status.slice(1);
    }

    // Get order status color
    function getOrderStatusColor(status) {
        switch (status) {
            case 'pending': return 'warning';
            case 'processing': return 'info';
            case 'completed': return 'success';
            case 'cancelled': return 'error';
            default: return 'grey';
        }
    }

    // Change page
    function changePage(newPage) {
        router.get(route('orders.index', { page: newPage }), {}, {
            preserveState: true,
            replace: true
        });
    }
</script>
