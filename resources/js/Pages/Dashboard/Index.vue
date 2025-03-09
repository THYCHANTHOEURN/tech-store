<template>

    <Head title="Admin Dashboard" />

    <DashboardLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template>

        <v-container fluid class="py-8">
            <!-- Statistics Cards -->
            <v-row>
                <v-col cols="12" sm="6" md="3">
                    <v-card color="primary" class="white--text">
                        <v-card-text>
                            <div class="text-h6 white--text">Products</div>
                            <div class="text-h4 white--text">{{ stats.products || 0 }}</div>
                            <v-icon large color="white">mdi-package-variant-closed</v-icon>
                        </v-card-text>
                    </v-card>
                </v-col>

                <v-col cols="12" sm="6" md="3">
                    <v-card color="success" class="white--text">
                        <v-card-text>
                            <div class="text-h6 white--text">Orders</div>
                            <div class="text-h4 white--text">{{ stats.orders || 0 }}</div>
                            <v-icon large color="white">mdi-cart</v-icon>
                        </v-card-text>
                    </v-card>
                </v-col>

                <v-col cols="12" sm="6" md="3">
                    <v-card color="warning" class="white--text">
                        <v-card-text>
                            <div class="text-h6 white--text">Customers</div>
                            <div class="text-h4 white--text">{{ stats.customers || 0 }}</div>
                            <v-icon large color="white">mdi-account-group</v-icon>
                        </v-card-text>
                    </v-card>
                </v-col>

                <v-col cols="12" sm="6" md="3">
                    <v-card color="info" class="white--text">
                        <v-card-text>
                            <div class="text-h6 white--text">Revenue</div>
                            <div class="text-h4 white--text">${{ stats.revenue || 0 }}</div>
                            <v-icon large color="white">mdi-currency-usd</v-icon>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Recent Orders -->
            <v-row class="mt-8">
                <v-col cols="12" md="7">
                    <v-card class="elevation-2">
                        <v-card-title>
                            Recent Orders
                            <v-spacer></v-spacer>
                            <v-btn color="primary" text to="/dashboard/orders">
                                View All
                                <v-icon small class="ml-1">mdi-arrow-right</v-icon>
                            </v-btn>
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-data-table :headers="orderHeaders" :items="recentOrders" :items-per-page="5"
                            hide-default-footer class="elevation-0">
                            <template v-slot:item.status="{ item }">
                                <v-chip :color="getStatusColor(item.status)" small dark>
                                    {{ item.status }}
                                </v-chip>
                            </template>
                            <template v-slot:item.total_amount="{ item }">
                                ${{ item.total_amount }}
                            </template>
                            <template v-slot:item.created_at="{ item }">
                                {{ formatDate(item.created_at) }}
                            </template>
                        </v-data-table>
                    </v-card>
                </v-col>

                <v-col cols="12" md="5">
                    <v-card class="elevation-2">
                        <v-card-title>
                            Popular Products
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-list two-line>
                            <Link v-for="(product, index) in popularProducts" :key="index"
                                :href="route('dashboard.products.show', product.uuid)" class="text-decoration-none">
                            <v-list-item>
                                <v-list-item-avatar>
                                    <v-img :src="product.primary_image_url" alt="Product Image"></v-img>
                                </v-list-item-avatar>
                                <v-list-item-content>
                                    <v-list-item-title>{{ product.name }}</v-list-item-title>
                                    <v-list-item-subtitle>
                                        Sold: {{ product.sold }} | Stock: {{ product.stock }}
                                    </v-list-item-subtitle>
                                </v-list-item-content>
                                <v-list-item-action>
                                    <v-chip color="primary" small>${{ product.price }}</v-chip>
                                </v-list-item-action>
                            </v-list-item>
                            </Link>
                        </v-list>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </DashboardLayout>
</template>

<script setup>
    import DashboardLayout from '@/Layouts/DashboardLayout.vue';
    import { Head, Link } from '@inertiajs/vue3';
    import { defineProps } from 'vue';

    // Get data from controller via Inertia props
    const props = defineProps({
        stats: Object,
        recentOrders: Array,
        popularProducts: Array
    });

    /**
     * Table headers for recent orders
     */
    const orderHeaders = [
        { title: 'Order ID', value: 'uuid', sortable: false },
        { title: 'Customer', value: 'customer' },
        { title: 'Status', value: 'status' },
        { title: 'Amount', value: 'total_amount' },
        { title: 'Date', value: 'created_at' }
    ];

    /**
     * Get status color based on order status
     *
     * @param {string} status
     * @returns {string}
     */
    const getStatusColor = (status) => {
        switch (status) {
            case 'completed': return 'success';
            case 'processing': return 'info';
            case 'pending': return 'warning';
            case 'cancelled': return 'error';
            default: return 'grey';
        }
    };

    /**
     * Format date string to human-readable format
     *
     * @param {string} dateString
     * @returns {string}
     */
    const formatDate = (dateString) => {
        return new Date(dateString).toLocaleDateString();
    };
</script>
