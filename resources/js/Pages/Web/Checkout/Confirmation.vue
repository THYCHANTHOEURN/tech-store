<template>

    <Head title="Order Confirmation" />
    <WebLayout>
        <v-container>
            <v-row>
                <v-col cols="12">
                    <h1 class="text-h4 mb-6">Order Confirmation</h1>
                </v-col>
            </v-row>

            <!-- Success Message -->
            <v-row class="mb-6">
                <v-col cols="12">
                    <v-alert type="success" variant="tonal" border="start" prominent>
                        <v-alert-title>Thank you for your order!</v-alert-title>
                        <p>Your order has been placed successfully. We will process it as soon as possible.</p>
                        <p class="mb-0 mt-2">
                            Order ID: <strong>{{ order.uuid }}</strong>
                        </p>
                    </v-alert>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12" md="8">
                    <!-- Order Details -->
                    <v-card class="mb-4">
                        <v-card-title>
                            <v-icon start class="mr-2">mdi-clipboard-text</v-icon>
                            Order Details
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <v-row>
                                <v-col cols="12" sm="6">
                                    <p><strong>Order Date:</strong> {{ formatDate(order.created_at) }}</p>
                                    <p><strong>Payment Method:</strong> {{ formatPaymentMethod(order.payment_method) }}
                                    </p>
                                </v-col>
                                <v-col cols="12" sm="6">
                                    <p>
                                        <strong>Order Status:</strong>
                                        <v-chip :color="getOrderStatusColor(order.status)" size="small" class="ml-1">
                                            {{ formatOrderStatus(order.status) }}
                                        </v-chip>
                                    </p>
                                    <p>
                                        <strong>Payment Status:</strong>
                                        <v-chip :color="getPaymentStatusColor(order.payment_status)" size="small"
                                            class="ml-1">
                                            {{ formatPaymentStatus(order.payment_status) }}
                                        </v-chip>
                                    </p>
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </v-card>

                    <!-- Shipping Information -->
                    <v-card class="mb-4">
                        <v-card-title>
                            <v-icon start class="mr-2">mdi-map-marker</v-icon>
                            Shipping Information
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <p class="white-space-pre-line">{{ order.shipping_address }}</p>
                            <div class="mt-2 d-flex align-center">
                                <v-icon color="primary" class="mr-2">mdi-phone</v-icon>
                                <span>{{ order.phone }}</span>
                            </div>
                        </v-card-text>
                    </v-card>

                    <!-- Order Items -->
                    <v-card>
                        <v-card-title>
                            <v-icon start class="mr-2">mdi-cart-outline</v-icon>
                            Order Items
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-list>
                            <v-list-item v-for="(item, index) in order.order_items" :key="index">
                                <template v-slot:prepend>
                                    <v-avatar size="48">
                                        <v-img :src="item.product.primary_image_url" cover></v-img>
                                    </v-avatar>
                                </template>
                                <v-list-item-title>{{ item.product.name }}</v-list-item-title>
                                <v-list-item-subtitle>
                                    Quantity: {{ item.quantity }} × ${{ formatPrice(item.price) }}
                                </v-list-item-subtitle>
                                <template v-slot:append>
                                    <div class="text-right">
                                        <span class="text-body-1 font-weight-bold">${{ formatPrice(item.price *
                                            item.quantity) }}</span>
                                    </div>
                                </template>
                            </v-list-item>
                        </v-list>
                        <v-divider></v-divider>
                        <v-card-text>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="text-body-1">Subtotal:</span>
                                <span class="text-body-1">${{ formatPrice(order.total_amount) }}</span>
                            </div>
                            <div class="d-flex justify-space-between">
                                <span class="text-h6 font-weight-bold">Total:</span>
                                <span class="text-h6 font-weight-bold">${{ formatPrice(order.total_amount) }}</span>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>

                <v-col cols="12" md="4">
                    <v-card>
                        <v-card-title>
                            <v-icon start class="mr-2">mdi-help-circle</v-icon>
                            Need Help?
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <p class="text-body-1">If you have any questions about your order, please contact our
                                customer service:</p>
                            <div class="mt-4 d-flex align-center">
                                <v-icon color="primary" class="mr-2">mdi-email</v-icon>
                                <a href="mailto:support@techstore.com"
                                    class="text-decoration-none text-primary">support@techstore.com</a>
                            </div>
                            <div class="mt-2 d-flex align-center">
                                <v-icon color="primary" class="mr-2">mdi-phone</v-icon>
                                <a href="tel:+1234567890" class="text-decoration-none text-primary">+1 (234) 567-890</a>
                            </div>
                        </v-card-text>
                        <v-card-actions>
                            <v-btn color="primary" block :href="route('index')">
                                Continue Shopping
                            </v-btn>
                        </v-card-actions>
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
        order: Object
    });

    // Format price to 2 decimal places
    const formatPrice = (price) => {
        return parseFloat(price).toFixed(2);
    };

    // Format order status
    const formatOrderStatus = (status) => {
        return status.charAt(0).toUpperCase() + status.slice(1);
    };

    // Format payment status
    const formatPaymentStatus = (status) => {
        return status.charAt(0).toUpperCase() + status.slice(1);
    };

    // Format payment method
    const formatPaymentMethod = (method) => {
        switch (method) {
            case 'cash': return 'Cash on Delivery';
            case 'card': return 'Credit/Debit Card';
            case 'bank_transfer': return 'Bank Transfer';
            default: return method;
        }
    };

    // Format date
    const formatDate = (dateString) => {
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return new Date(dateString).toLocaleDateString(undefined, options);
    };

    // Get order status color
    const getOrderStatusColor = (status) => {
        switch (status) {
            case 'pending': return 'warning';
            case 'processing': return 'info';
            case 'completed': return 'success';
            case 'cancelled': return 'error';
            default: return 'grey';
        }
    };

    // Get payment status color
    const getPaymentStatusColor = (status) => {
        switch (status) {
            case 'paid': return 'success';
            case 'pending': return 'warning';
            case 'unpaid': return 'grey';
            case 'failed': return 'error';
            default: return 'grey';
        }
    };
</script>

<style scoped>
    .white-space-pre-line {
        white-space: pre-line;
    }
</style>
