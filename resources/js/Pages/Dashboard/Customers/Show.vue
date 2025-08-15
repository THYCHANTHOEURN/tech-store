<template>

    <Head :title="customer.name" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ $t('Customer Details') }}: {{ customer.name }}
                </h2>
                <v-spacer></v-spacer>
                <Link :href="route('dashboard.customers.edit', customer.uuid)">
                <v-btn color="warning" prepend-icon="mdi-pencil" class="mr-2">
                    {{ $t('Edit') }}
                </v-btn>
                </Link>
                <Link :href="route('dashboard.customers.index')">
                <v-btn color="secondary" prepend-icon="mdi-arrow-left" variant="outlined">
                    {{ $t('Back to Customers') }}
                </v-btn>
                </Link>
            </div>
        </template>

        <v-container fluid class="py-8">
            <v-row>
                <!-- Customer Info Card -->
                <v-col cols="12" md="6">
                    <v-card class="mb-4">
                        <v-card-title class="d-flex align-center">
                            <v-icon class="mr-2">mdi-account</v-icon>
                            {{ $t('Customer Information') }}
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <v-list>
                                <v-list-item>
                                    <template v-slot:prepend>
                                        <v-icon color="primary" class="mr-2">mdi-badge-account</v-icon>
                                    </template>
                                    <v-list-item-title>{{ $t('Name') }}</v-list-item-title>
                                    <v-list-item-subtitle>{{ customer.name }}</v-list-item-subtitle>
                                </v-list-item>

                                <v-list-item>
                                    <template v-slot:prepend>
                                        <v-icon color="primary" class="mr-2">mdi-email</v-icon>
                                    </template>
                                    <v-list-item-title>{{ $t('Email') }}</v-list-item-title>
                                    <v-list-item-subtitle>{{ customer.email }}</v-list-item-subtitle>
                                </v-list-item>

                                <v-list-item>
                                    <template v-slot:prepend>
                                        <v-icon color="primary" class="mr-2">mdi-check-decagram</v-icon>
                                    </template>
                                    <v-list-item-title>{{ $t('Email Verification') }}</v-list-item-title>
                                    <v-list-item-subtitle>
                                        <v-chip :color="customer.email_verified_at ? 'success' : 'error'" size="small"
                                            class="text-uppercase">
                                            {{ customer.email_verified_at ? 'Verified' : 'Unverified' }}
                                        </v-chip>
                                    </v-list-item-subtitle>
                                </v-list-item>

                                <v-list-item v-if="customer.phone">
                                    <template v-slot:prepend>
                                        <v-icon color="primary" class="mr-2">mdi-phone</v-icon>
                                    </template>
                                    <v-list-item-title>{{ $t('Phone') }}</v-list-item-title>
                                    <v-list-item-subtitle>{{ customer.phone }}</v-list-item-subtitle>
                                </v-list-item>

                                <v-list-item v-if="customer.address">
                                    <template v-slot:prepend>
                                        <v-icon color="primary" class="mr-2">mdi-map-marker</v-icon>
                                    </template>
                                    <v-list-item-title>{{ $t('Address') }}</v-list-item-title>
                                    <v-list-item-subtitle style="white-space: pre-line">{{ customer.address
                                        }}</v-list-item-subtitle>
                                </v-list-item>

                                <v-list-item>
                                    <template v-slot:prepend>
                                        <v-icon color="primary" class="mr-2">mdi-calendar</v-icon>
                                    </template>
                                    <v-list-item-title>{{ $t('Registration Date') }}</v-list-item-title>
                                    <v-list-item-subtitle>{{ formatDate(customer.created_at) }}</v-list-item-subtitle>
                                </v-list-item>
                            </v-list>
                        </v-card-text>
                    </v-card>
                </v-col>

                <!-- Customer Stats Card -->
                <v-col cols="12" md="6">
                    <v-card class="mb-4">
                        <v-card-title class="d-flex align-center">
                            <v-icon class="mr-2">mdi-chart-box</v-icon>
                            {{ $t('Customer Stats') }}
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <v-row>
                                <v-col cols="12" md="6">
                                    <v-card class="pa-3" color="blue-lighten-5">
                                        <div class="text-center">
                                            <div class="text-h6 font-weight-bold">{{ stats.total_orders }}</div>
                                            <div class="text-subtitle-2">{{ $t('Total Orders') }}</div>
                                        </div>
                                    </v-card>
                                </v-col>

                                <v-col cols="12" md="6">
                                    <v-card class="pa-3" color="green-lighten-5">
                                        <div class="text-center">
                                            <div class="text-h6 font-weight-bold">${{ formatCurrency(stats.total_spent)
                                                }}</div>
                                            <div class="text-subtitle-2">{{ $t('Total Spent') }}</div>
                                        </div>
                                    </v-card>
                                </v-col>

                                <v-col cols="12" md="6">
                                    <v-card class="pa-3" color="purple-lighten-5">
                                        <div class="text-center">
                                            <div class="text-h6 font-weight-bold">{{ stats.wishlist_items }}</div>
                                            <div class="text-subtitle-2">{{ $t('Wishlist Items') }}</div>
                                        </div>
                                    </v-card>
                                </v-col>

                                <v-col cols="12" md="6">
                                    <v-card class="pa-3" color="amber-lighten-5">
                                        <div class="text-center">
                                            <div class="text-h6 font-weight-bold">{{ stats.cart_items }}</div>
                                            <div class="text-subtitle-2">{{ $t('Cart Items') }}</div>
                                        </div>
                                    </v-card>
                                </v-col>
                            </v-row>

                            <v-divider class="my-4"></v-divider>

                            <div class="d-flex justify-space-between">
                                <Link :href="route('dashboard.customers.edit', customer.uuid)">
                                <v-btn color="primary" variant="outlined" prepend-icon="mdi-pencil" class="mr-2">
                                    {{ $t('Edit Customer') }}
                                </v-btn>
                                </Link>
                                <v-btn color="error" variant="outlined" prepend-icon="mdi-delete"
                                    @click="confirmDelete">
                                    {{ $t('Delete Customer') }}
                                </v-btn>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>

                <!-- Recent Orders -->
                <v-col cols="12">
                    <v-card>
                        <v-card-title class="d-flex align-center">
                            <v-icon class="mr-2">mdi-shopping</v-icon>
                            {{ $t('Recent Orders') }}
                            <v-spacer></v-spacer>
                            <Link v-if="customer.orders.length"
                                :href="route('dashboard.orders.create', { user_id: customer.id })">
                            <v-btn color="primary" prepend-icon="mdi-plus" size="small">
                                Create Order
                            </v-btn>
                            </Link>
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <div v-if="customer.orders.length === 0" class="text-center py-4">
                                <v-icon size="large" color="grey" class="mb-2">mdi-cart-off</v-icon>
                                <p class="text-subtitle-1 text-grey">No orders found for this customer.</p>
                                <Link :href="route('dashboard.orders.create', { user_id: customer.id })">
                                <v-btn color="primary" class="mt-2" prepend-icon="mdi-plus">
                                    Create First Order
                                </v-btn>
                                </Link>
                            </div>
                            <v-table v-else>
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Payment</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="order in customer.orders" :key="order.id">
                                        <td>
                                            <span class="font-weight-medium text-primary">#{{
                                                order.uuid.slice(-8).toUpperCase()
                                                }}</span>
                                        </td>
                                        <td>{{ formatDate(order.created_at) }}</td>
                                        <td>${{ formatCurrency(order.total_amount) }}</td>
                                        <td>
                                            <v-chip :color="getOrderStatusColor(order.status)" size="small">
                                                {{ formatOrderStatus(order.status) }}
                                            </v-chip>
                                        </td>
                                        <td>
                                            <v-chip :color="getPaymentStatusColor(order.payment_status)" size="small">
                                                {{ formatPaymentStatus(order.payment_status) }}
                                            </v-chip>
                                        </td>
                                        <td class="text-center">
                                            <Link :href="route('dashboard.orders.show', order.uuid)">
                                            <v-btn icon variant="text" color="blue">
                                                <v-icon>mdi-eye</v-icon>
                                            </v-btn>
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </v-table>
                            <div v-if="stats.total_orders > customer.orders.length" class="text-center mt-4">
                                <Link :href="route('dashboard.orders.index', { search: customer.email })">
                                <v-btn color="secondary" variant="text">
                                    View All {{ stats.total_orders }} Orders
                                </v-btn>
                                </Link>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>

        <!-- Delete Confirmation Dialog -->
        <v-dialog v-model="deleteDialog" max-width="500">
            <v-card>
                <v-card-title class="text-h5">
                    Confirm Delete
                </v-card-title>
                <v-card-text>
                    Are you sure you want to delete the customer "{{ customer.name }}"? This action cannot be undone.
                    <p class="mt-4 text-error font-weight-bold" v-if="stats.total_orders > 0">
                        Warning: This customer has {{ stats.total_orders }} order(s) associated with their account.
                    </p>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue-darken-1" variant="text" @click="closeDeleteDialog">
                        Cancel
                    </v-btn>
                    <v-btn color="error" variant="flat" @click="deleteCustomer" :loading="deleting">
                        Delete
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </DashboardLayout>
</template>

<script setup>
    import DashboardLayout from '@/Layouts/DashboardLayout.vue';
    import { Head, router, Link } from '@inertiajs/vue3';
    import { ref } from 'vue';

    const props = defineProps({
        customer: Object,
        stats: Object,
    });

    // Delete dialog
    const deleteDialog = ref(false);
    const deleting = ref(false);

    // Format date
    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        const options = { year: 'numeric', month: 'short', day: 'numeric' };
        return new Date(dateString).toLocaleDateString(undefined, options);
    }

    // Format currency
    function formatCurrency(amount) {
        return amount ? Number(amount).toFixed(2) : '0.00';
    }

    // Format order status
    function formatOrderStatus(status) {
        return status ? status.charAt(0).toUpperCase() + status.slice(1) : '';
    }

    // Format payment status
    function formatPaymentStatus(status) {
        return status ? status.charAt(0).toUpperCase() + status.slice(1) : '';
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

    // Get payment status color
    function getPaymentStatusColor(status) {
        switch (status) {
            case 'paid': return 'success';
            case 'pending': return 'warning';
            case 'unpaid': return 'grey';
            case 'failed': return 'error';
            default: return 'grey';
        }
    }

    // Delete customer
    const confirmDelete = () => {
        deleteDialog.value = true;
    };

    const closeDeleteDialog = () => {
        deleteDialog.value = false;
    };

    const deleteCustomer = () => {
        deleting.value = true;
        router.delete(route('dashboard.customers.destroy', props.customer.uuid), {
            onSuccess: () => {
                // Will be redirected to index page by controller
            },
            onError: () => {
                closeDeleteDialog();
            },
            onFinish: () => {
                deleting.value = false;
            }
        });
    };
</script>
