<template>
    <WebLayout>

        <Head :title="`Order #${order.uuid.slice(-8).toUpperCase()}`" />

        <v-container>
            <v-row>
                <v-col cols="12">
                    <!-- Back Button -->
                    <Link :href="route('orders.index')" class="text-decoration-none mb-6 d-block">
                    <v-btn variant="text" prepend-icon="mdi-arrow-left" color="primary">
                        Back to Orders
                    </v-btn>
                    </Link>

                    <!-- Order Header -->
                    <v-card class="mb-6">
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

                <!-- Order Progress -->
                <v-col cols="12">
                    <v-card class="mb-6">
                        <v-card-title>
                            <v-icon class="mr-2">mdi-truck-delivery</v-icon>
                            Order Progress
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <v-stepper :value="getOrderStepValue(order.status)" class="elevation-0">
                                <v-stepper-header>
                                    <v-stepper-item value="1" title="Order Placed"
                                        :complete="['pending', 'processing', 'completed'].includes(order.status)"
                                        :color="getOrderStatusColor(order.status)">
                                    </v-stepper-item>

                                    <v-divider></v-divider>

                                    <v-stepper-item value="2" title="Processing"
                                        :complete="['processing', 'completed'].includes(order.status)"
                                        :color="getOrderStatusColor(order.status)">
                                    </v-stepper-item>

                                    <v-divider></v-divider>

                                    <v-stepper-item value="3" title="Completed" :complete="order.status === 'completed'"
                                        :color="getOrderStatusColor(order.status)">
                                    </v-stepper-item>
                                </v-stepper-header>
                            </v-stepper>
                        </v-card-text>
                    </v-card>
                </v-col>

                <!-- Order Items -->
                <v-col cols="12" md="8">
                    <v-card>
                        <v-card-title>
                            <v-icon class="mr-2">mdi-cart-outline</v-icon>
                            Order Items
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <v-table>
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th class="text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in order.order_items" :key="item.id">
                                        <td>
                                            <div class="d-flex align-center">
                                                <v-img :src="item.product?.primary_image_url" width="50" height="50"
                                                    class="rounded mr-3" cover></v-img>
                                                <div>
                                                    <div class="font-weight-medium">{{ item.product?.name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>${{ formatCurrency(item.price) }}</td>
                                        <td>{{ item.quantity }}</td>
                                        <td class="text-right">${{ formatCurrency(item.price * item.quantity) }}</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-right font-weight-bold">Total:</td>
                                        <td class="text-right">${{ formatCurrency(order.total_amount) }}</td>
                                    </tr>
                                </tfoot>
                            </v-table>
                        </v-card-text>
                    </v-card>
                </v-col>

                <!-- Order Information -->
                <v-col cols="12" md="4">
                    <!-- Shipping Information -->
                    <v-card class="mb-4">
                        <v-card-title>
                            <v-icon class="mr-2">mdi-map-marker</v-icon>
                            Shipping Address
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <p class="white-space-pre-line mb-4">{{ order.shipping_address }}</p>

                            <div class="d-flex align-center">
                                <v-icon size="small" color="primary" class="mr-2">mdi-phone</v-icon>
                                <span>{{ order.phone }}</span>
                            </div>
                        </v-card-text>
                    </v-card>

                    <!-- Payment Information -->
                    <v-card>
                        <v-card-title>
                            <v-icon class="mr-2">mdi-credit-card</v-icon>
                            Payment Information
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <div class="d-flex justify-space-between mb-4">
                                <span>Payment Method</span>
                                <span class="font-weight-medium">{{ formatPaymentMethod(order.payment_method) }}</span>
                            </div>
                            <div class="d-flex justify-space-between">
                                <span>Payment Status</span>
                                <v-chip :color="getPaymentStatusColor(order.payment_status)" size="small">
                                    {{ formatPaymentStatus(order.payment_status) }}
                                </v-chip>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </WebLayout>
</template>

<script setup>
    import WebLayout from '@/Layouts/WebLayout.vue';
    import { Head, Link } from '@inertiajs/vue3';

    const props = defineProps({
        order: Object,
    });

    // Format currency
    function formatCurrency(amount) {
        return parseFloat(amount).toFixed(2);
    }

    // Format date
    function formatDate(dateString) {
        const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
        return new Date(dateString).toLocaleDateString(undefined, options);
    }

    // Format order status
    function formatOrderStatus(status) {
        return status.charAt(0).toUpperCase() + status.slice(1);
    }

    // Format payment status
    function formatPaymentStatus(status) {
        return status.charAt(0).toUpperCase() + status.slice(1);
    }

    // Format payment method
    function formatPaymentMethod(method) {
        switch (method) {
            case 'cash': return 'Cash on Delivery';
            case 'card': return 'Credit/Debit Card';
            case 'bank_transfer': return 'Bank Transfer';
            default: return method;
        }
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

    // Get order stepper value
    function getOrderStepValue(status) {
        switch (status) {
            case 'pending': return '1';
            case 'processing': return '2';
            case 'completed': return '3';
            case 'cancelled': return '1'; // Stay at step 1 for cancelled orders
            default: return '1';
        }
    }
</script>

<style scoped>
    .white-space-pre-line {
        white-space: pre-line;
    }
</style>
