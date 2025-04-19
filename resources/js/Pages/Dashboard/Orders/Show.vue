<template>

    <Head :title="`Order #${order.uuid.slice(-8).toUpperCase()}`" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Order #{{ order.uuid.slice(-8).toUpperCase() }}
                </h2>
                <v-spacer></v-spacer>
                <Link :href="route('dashboard.orders.edit', order.uuid)" class="text-decoration-none">
                <v-btn color="warning" prepend-icon="mdi-pencil" class="mx-2">
                    Edit
                </v-btn>
                </Link>
                <Link :href="route('dashboard.orders.index')">
                <v-btn color="secondary" prepend-icon="mdi-arrow-left" variant="outlined">
                    Back to Orders
                </v-btn>
                </Link>
            </div>
        </template>

        <v-container fluid class="py-8">
            <v-row>
                <!-- Order Status Panel -->
                <v-col cols="12">
                    <v-card class="mb-4">
                        <v-card-text class="d-flex flex-wrap justify-space-between align-center">
                            <div class="d-flex align-center">
                                <v-icon size="large" color="primary" class="mr-2">mdi-shopping</v-icon>
                                <div>
                                    <div class="text-h6 mb-1">Order #{{ order.uuid.slice(-8).toUpperCase() }}</div>
                                    <div class="text-caption">{{ formatDate(order.created_at) }}</div>
                                </div>
                            </div>

                            <div class="d-flex align-center my-2">
                                <div class="mr-4">
                                    <div class="text-caption mb-1">Order Status</div>
                                    <v-chip :color="getOrderStatusColor(order.status)" size="small">
                                        {{ formatOrderStatus(order.status) }}
                                    </v-chip>
                                </div>
                                <div>
                                    <div class="text-caption mb-1">Payment Status</div>
                                    <v-chip :color="getPaymentStatusColor(order.payment_status)" size="small">
                                        {{ formatPaymentStatus(order.payment_status) }}
                                    </v-chip>
                                </div>
                            </div>

                            <div class="text-right">
                                <div class="text-caption mb-1">Total Amount</div>
                                <div class="text-h6">${{ formatCurrency(order.total_amount) }}</div>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>

                <!-- Customer Info -->
                <v-col cols="12" md="4">
                    <v-card class="mb-4 h-100">
                        <v-card-title>
                            <v-icon class="mr-2">mdi-account</v-icon>
                            Customer Information
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <h3 class="text-subtitle-1 font-weight-bold mb-2">{{ order.user.name }}</h3>
                            <p class="mb-2">{{ order.user.email }}</p>

                            <v-divider class="my-4"></v-divider>

                            <h3 class="text-subtitle-1 font-weight-bold mb-2">Shipping Address</h3>
                            <p class="mb-2" style="white-space: pre-line">{{ order.shipping_address }}</p>

                            <v-divider class="my-4"></v-divider>

                            <h3 class="text-subtitle-1 font-weight-bold mb-2">Phone Number</h3>
                            <p class="mb-2 d-flex align-center">
                                <v-icon size="small" color="primary" class="mr-2">mdi-phone</v-icon>
                                {{ order.phone }}
                            </p>
                        </v-card-text>
                    </v-card>
                </v-col>

                <!-- Order Info -->
                <v-col cols="12" md="4">
                    <v-card class="mb-4 h-100">
                        <v-card-title>
                            <v-icon class="mr-2">mdi-information-outline</v-icon>
                            Order Details
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <v-list>
                                <v-list-item>
                                    <v-list-item-title>Order ID</v-list-item-title>
                                    <v-list-item-subtitle>{{ order.uuid }}</v-list-item-subtitle>
                                </v-list-item>

                                <v-list-item>
                                    <v-list-item-title>Payment Method</v-list-item-title>
                                    <v-list-item-subtitle>{{ formatPaymentMethod(order.payment_method)
                                        }}</v-list-item-subtitle>
                                </v-list-item>

                                <v-list-item>
                                    <v-list-item-title>Created Date</v-list-item-title>
                                    <v-list-item-subtitle>{{ formatDate(order.created_at) }}</v-list-item-subtitle>
                                </v-list-item>

                                <v-list-item>
                                    <v-list-item-title>Last Updated</v-list-item-title>
                                    <v-list-item-subtitle>{{ formatDate(order.updated_at) }}</v-list-item-subtitle>
                                </v-list-item>
                            </v-list>
                        </v-card-text>
                    </v-card>
                </v-col>

                <!-- Actions Card -->
                <v-col cols="12" md="4">
                    <v-card class="mb-4">
                        <v-card-title>
                            <v-icon class="mr-2">mdi-cog-outline</v-icon>
                            Actions
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <v-row>
                                <v-col>
                                    <Link :href="route('dashboard.orders.invoice', order.uuid)"
                                        class="text-decoration-none">
                                    <v-btn color="info" block prepend-icon="mdi-printer" class="mb-3">
                                        Print Invoice
                                    </v-btn>
                                    </Link>

                                    <Link :href="route('dashboard.orders.edit', order.uuid)"
                                        class="text-decoration-none">
                                    <v-btn color="warning" block prepend-icon="mdi-pencil" class="mb-3">
                                        Edit Order
                                    </v-btn>
                                    </Link>

                                    <v-btn color="error" block prepend-icon="mdi-delete" @click="confirmDelete">
                                        Delete Order
                                    </v-btn>
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </v-card>
                </v-col>

                <!-- Order Items Table -->
                <v-col cols="12">
                    <v-card>
                        <v-card-title>
                            <v-icon class="mr-2">mdi-cart-outline</v-icon>
                            Order Items ({{ order.order_items.length }})
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text class="pa-0">
                            <v-table>
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Subtotal</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in order.order_items" :key="item.id">
                                        <td>
                                            <div class="d-flex align-center">
                                                <v-img :src="item.product?.primary_image_url" width="50" height="50"
                                                    class="rounded mr-3"></v-img>
                                                <div>
                                                    {{ item.product?.name }}
                                                </div>
                                            </div>
                                        </td>
                                        <td>${{ formatCurrency(item.price) }}</td>
                                        <td>{{ item.quantity }}</td>
                                        <td>${{ formatCurrency(item.price * item.quantity) }}</td>
                                        <td class="text-center">
                                            <Link :href="route('dashboard.products.show', item.product.uuid)"
                                                class="text-decoration-none">
                                            <v-btn icon size="x-small" color="info" title="View Product" rounded="lg">
                                                <v-icon>mdi-eye</v-icon>
                                            </v-btn>
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-right font-weight-bold">Total:</td>
                                        <td colspan="2" class="font-weight-bold">${{ formatCurrency(order.total_amount)
                                            }}</td>
                                    </tr>
                                </tfoot>
                            </v-table>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Delete Confirmation Dialog -->
            <v-dialog v-model="deleteDialog" max-width="500px">
                <v-card>
                    <v-card-title class="text-h5">Delete Order</v-card-title>
                    <v-card-text>
                        Are you sure you want to delete this order? This action cannot be undone.
                        <p class="mt-4 font-weight-bold">Order #{{ order.uuid.slice(-8).toUpperCase() }} - ${{
                            formatCurrency(order.total_amount) }}</p>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue-darken-1" variant="text" @click="closeDeleteDialog">Cancel</v-btn>
                        <v-btn color="error" :loading="deleting" @click="deleteOrder">Delete</v-btn>
                        <v-spacer></v-spacer>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-container>
    </DashboardLayout>
</template>

<script setup>
    import DashboardLayout from '@/Layouts/DashboardLayout.vue';
    import { Head, Link, router } from '@inertiajs/vue3';
    import { ref } from 'vue';

    const props = defineProps({
        order: Object,
        orderStatuses: Array,
        paymentStatuses: Array
    });

    const deleteDialog = ref(false);
    const deleting = ref(false);

    /**
     * Format order status
     *
     * @param status
     * @return {string}
     */
    function formatOrderStatus(status) {
        return status.charAt(0).toUpperCase() + status.slice(1);
    }

    /**
     * Format payment status
     *
     * @param status
     * @return {string}
     */
    function formatPaymentStatus(status) {
        return status.charAt(0).toUpperCase() + status.slice(1);
    }

    /**
     * Format payment method
     *
     * @param method
     * @return {string}
     */
    function formatPaymentMethod(method) {
        switch (method) {
            case 'cash': return 'Cash on Delivery';
            case 'card': return 'Credit Card';
            case 'bank_transfer': return 'Bank Transfer';
            default: return method;
        }
    }

    /**
     * Get color for order status
     *
     * @param status
     * @return {string}
     */
    function getOrderStatusColor(status) {
        switch (status) {
            case 'pending': return 'warning';
            case 'processing': return 'info';
            case 'completed': return 'success';
            case 'cancelled': return 'error';
            default: return 'grey';
        }
    }

    /**
     * Get color for payment status
     *
     * @param status
     * @return {string}
     */
    function getPaymentStatusColor(status) {
        switch (status) {
            case 'paid': return 'success';
            case 'pending': return 'warning';
            case 'unpaid': return 'grey';
            case 'failed': return 'error';
            default: return 'grey';
        }
    }

    /**
     * Format currency
     *
     * @param amount
     * @return {string}
     */
    function formatCurrency(amount) {
        return amount ? Number(amount).toFixed(2) : '0.00';
    }

    /**
     * Format date
     *
     * @param dateString
     * @return {string}
     */
    function formatDate(dateString) {
        const options = { year: 'numeric', month: 'short', day: 'numeric' };
        return new Date(dateString).toLocaleDateString(undefined, options);
    }

    /**
     * Confirm delete order
     */
    const confirmDelete = () => {
        deleteDialog.value = true;
    };

    /**
     * Close delete confirmation dialog
     */
    const closeDeleteDialog = () => {
        deleteDialog.value = false;
    };

    /**
     * Delete order
     *
     * @returns {void}
     */
    const deleteOrder = () => {
        deleting.value = true;
        router.delete(route('dashboard.orders.destroy', props.order.uuid), {
            onSuccess: () => {
                // Will redirect to orders index page
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
