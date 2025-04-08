<template>

    <Head title="Checkout" />
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
                    <v-stepper alt-labels :value="1">
                        <v-stepper-header>
                            <v-stepper-item value="1" title="Shipping" complete></v-stepper-item>
                            <v-divider></v-divider>
                            <v-stepper-item value="2" title="Payment"></v-stepper-item>
                            <v-divider></v-divider>
                            <v-stepper-item value="3" title="Confirmation"></v-stepper-item>
                        </v-stepper-header>
                    </v-stepper>
                </v-col>
            </v-row>

            <v-row>
                <!-- Shipping Form -->
                <v-col cols="12" md="8">
                    <v-card class="mb-4">
                        <v-card-title>
                            <v-icon start class="mr-2">mdi-map-marker</v-icon>
                            Shipping Information
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <v-form ref="formRef" @submit.prevent="submitShipping">
                                <v-row>
                                    <v-col cols="12">
                                        <v-textarea v-model="form.shipping_address" label="Shipping Address*"
                                            variant="outlined"
                                            :error-messages="formErrors.shipping_address || errors.shipping_address"
                                            rows="3" counter required></v-textarea>
                                    </v-col>

                                    <v-col cols="12">
                                        <v-text-field v-model="form.phone" label="Phone Number*" variant="outlined"
                                            :error-messages="formErrors.phone || errors.phone" required></v-text-field>
                                    </v-col>

                                    <v-col cols="12" class="d-flex justify-end">
                                        <v-btn color="primary" size="large" type="submit" :loading="processing">
                                            Continue to Payment
                                            <v-icon end>mdi-arrow-right</v-icon>
                                        </v-btn>
                                    </v-col>
                                </v-row>
                            </v-form>
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
    import { Head, useForm, Link } from '@inertiajs/vue3';
    import { ref } from 'vue';

    const props = defineProps({
        cartItems: Array,
        cartTotal: Number,
        user: Object,
        errors: Object
    });

    const processing = ref(false);
    const formErrors = ref({});
    const formRef = ref(null);

    // Form for shipping details
    const form = useForm({
        shipping_address: props.user?.address || '',
        phone: props.user?.phone || '',
    });

    // Format price to 2 decimal places
    const formatPrice = (price) => {
        return parseFloat(price).toFixed(2);
    };

    // Submit shipping form
    const submitShipping = () => {
        // Reset form errors
        formErrors.value = {};
        let isValid = true;

        // Check shipping_address
        if (!form.shipping_address || form.shipping_address.trim() === '') {
            formErrors.value.shipping_address = 'Shipping address is required';
            isValid = false;
        }

        // Check phone
        if (!form.phone || form.phone.trim() === '') {
            formErrors.value.phone = 'Phone number is required';
            isValid = false;
        }

        // If validation fails, don't submit
        if (!isValid) {
            return;
        }

        // Form is valid, proceed with submission
        processing.value = true;
        form.post(route('checkout.payment'), {
            onSuccess: () => {
                console.log('Success: Form submitted successfully');
            },
            onError: (errors) => {
                console.error('Validation errors:', errors);
                formErrors.value = errors;
            },
            onFinish: () => {
                processing.value = false;
            },
            preserveScroll: true
        });
    };

</script>
