<template>

    <Head title="Checkout - Payment" />
    <WebLayout>
        <v-container>
            <v-row>
                <v-col cols="12">
                    <h1 class="text-h4 mb-6">Checkout</h1>
                </v-col>
            </v-row>

            <!-- Checkout Steps -->
            <v-row class="mb-6">
                <v-col cols="12">
                    <v-stepper alt-labels :value="2">
                        <v-stepper-header>
                            <v-stepper-item value="1" title="Shipping" complete></v-stepper-item>
                            <v-divider></v-divider>
                            <v-stepper-item value="2" title="Payment" complete></v-stepper-item>
                            <v-divider></v-divider>
                            <v-stepper-item value="3" title="Confirmation"></v-stepper-item>
                        </v-stepper-header>
                    </v-stepper>
                </v-col>
            </v-row>

            <v-row>
                <!-- Payment Method Selection -->
                <v-col cols="12" md="8">
                    <v-card class="mb-4">
                        <v-card-title>
                            <v-icon start class="mr-2">mdi-credit-card</v-icon>
                            Payment Method
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <v-form ref="form" @submit.prevent="submitPayment">
                                <v-radio-group v-model="selectedPaymentMethod" :error-messages="errors.payment_method">
                                    <v-radio value="cash">
                                        <template v-slot:label>
                                            <div class="d-flex align-center">
                                                <v-icon class="mr-2">mdi-cash</v-icon>
                                                <div>
                                                    <strong>Cash on Delivery</strong>
                                                    <div class="text-caption">Pay when you receive the order</div>
                                                </div>
                                            </div>
                                        </template>
                                    </v-radio>

                                    <v-radio value="card">
                                        <template v-slot:label>
                                            <div class="d-flex align-center">
                                                <v-icon class="mr-2">mdi-credit-card</v-icon>
                                                <div>
                                                    <strong>Credit/Debit Card</strong>
                                                    <div class="text-caption">Pay securely with your card</div>
                                                </div>
                                            </div>
                                        </template>
                                    </v-radio>

                                    <v-radio value="bank_transfer">
                                        <template v-slot:label>
                                            <div class="d-flex align-center">
                                                <v-icon class="mr-2">mdi-bank</v-icon>
                                                <div>
                                                    <strong>Bank Transfer</strong>
                                                    <div class="text-caption">Pay via bank transfer</div>
                                                </div>
                                            </div>
                                        </template>
                                    </v-radio>
                                </v-radio-group>

                                <div class="d-flex justify-end mt-6">
                                    <v-btn color="secondary" class="mr-2" :href="route('checkout.index')">
                                        <v-icon start>mdi-arrow-left</v-icon>
                                        Back to Shipping
                                    </v-btn>
                                    <v-btn color="primary" type="submit" :loading="processing">
                                        Place Order
                                        <v-icon end>mdi-check</v-icon>
                                    </v-btn>
                                </div>
                            </v-form>
                        </v-card-text>
                    </v-card>

                    <!-- Shipping Information Review -->
                    <v-card>
                        <v-card-title>
                            <v-icon start class="mr-2">mdi-map-marker</v-icon>
                            Shipping Information
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <div class="text-body-1 mb-2">
                                <strong>Address:</strong> {{ shipping_address }}
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>

                <!-- Order Summary -->
                <v-col cols="12" md="4">
                    <v-card>
                        <v-card-title>
                            <v-icon start class="mr-2">mdi-cart-outline</v-icon>
                            Order Summary
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-list>
                            <v-list-item v-for="(item, index) in cartItems" :key="index">
                                <template v-slot:prepend>
                                    <v-avatar size="40">
                                        <v-img :src="item.product.primary_image_url" cover></v-img>
                                    </v-avatar>
                                </template>
                                <v-list-item-title class="text-body-2">
                                    {{ item.product.name }} x {{ item.quantity }}
                                </v-list-item-title>
                                <template v-slot:append>
                                    ${{ formatPrice((item.product.sale_price || item.product.price) * item.quantity) }}
                                </template>
                            </v-list-item>
                        </v-list>
                        <v-divider></v-divider>
                        <v-card-text>
                            <div class="d-flex justify-space-between mb-2">
                                <span class="text-body-1">Subtotal:</span>
                                <span class="text-body-1">${{ formatPrice(cartTotal) }}</span>
                            </div>
                            <div class="d-flex justify-space-between">
                                <span class="text-h6 font-weight-bold">Total:</span>
                                <span class="text-h6 font-weight-bold">${{ formatPrice(cartTotal) }}</span>
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
    import { Head, useForm } from '@inertiajs/vue3';
    import { ref, watch } from 'vue';

    const props = defineProps({
        cartItems: Array,
        cartTotal: Number,
        shipping_address: String,
        errors: Object
    });

    const processing = ref(false);
    const formRef = ref(null);

    // Use a separate reactive reference for the radio buttons
    const selectedPaymentMethod = ref('cash');

    // Form for payment method
    const form = useForm({
        payment_method: 'cash', // Default payment method
    });

    // Keep form in sync with selectedPaymentMethod
    watch(selectedPaymentMethod, (newValue) => {
        form.payment_method = newValue;
    });

    // Format price to 2 decimal places
    const formatPrice = (price) => {
        return parseFloat(price).toFixed(2);
    };

    // Submit payment form
    const submitPayment = () => {
        processing.value = true;
        form.post(route('checkout.process'), {
            onSuccess: () => {
                console.log('Order placed successfully');
            },
            onError: (errors) => {
                console.error('Error placing order:', errors);
            },
            onFinish: () => {
                processing.value = false;
            },
        });
    };
</script>
