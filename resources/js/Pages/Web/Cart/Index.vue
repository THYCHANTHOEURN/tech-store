<template>

    <Head title="Shopping Cart" />
    <WebLayout>
        <v-container>
            <h1 class="text-h4 mb-6">Shopping Cart</h1>

            <v-row v-if="cartItems.length">
                <v-col cols="12" md="8">
                    <v-list>
                        <v-list-item v-for="item in cartItems" :key="item.id" class="mb-4">
                            <template v-slot:prepend>
                                <v-img :src="item.product.primary_image_url" width="100" height="100" cover></v-img>
                            </template>

                            <v-list-item-title>{{ item.product.name }}</v-list-item-title>
                            <v-list-item-subtitle>
                                ${{ item.product.sale_price || item.product.price }} × {{ item.quantity }}
                            </v-list-item-subtitle>

                            <template v-slot:append>
                                <div class="text-right">
                                    <div class="text-h6">${{ (item.product.sale_price || item.product.price) *
                                        item.quantity }}</div>
                                    <v-btn color="error" size="small" variant="text" @click="removeFromCart(item.id)"
                                        prepend-icon="mdi-delete" class="mt-2">
                                        Remove
                                    </v-btn>
                                </div>
                            </template>
                        </v-list-item>
                    </v-list>
                </v-col>

                <v-col cols="12" md="4">
                    <v-card>
                        <v-card-text>
                            <h2 class="text-h6 mb-4">Order Summary</h2>
                            <div class="d-flex justify-space-between mb-4">
                                <span>Subtotal</span>
                                <span>${{ cartTotal }}</span>
                            </div>
                            <v-btn color="primary" block>Proceed to Checkout</v-btn>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <v-row v-else>
                <v-col cols="12" class="text-center">
                    <p class="text-h6 mb-4">Your cart is empty</p>
                    <Link :href="route('index')" class="text-decoration-none">
                    <v-btn color="primary">Continue Shopping</v-btn>
                    </Link>
                </v-col>
            </v-row>
        </v-container>
    </WebLayout>
</template>

<script setup>
    import { Link, router } from '@inertiajs/vue3';
    import WebLayout from '@/Layouts/WebLayout.vue';
    import { computed } from 'vue';

    const props = defineProps({
        cartItems: {
            type: Array,
            default: () => [],
        },
    });

    const cartTotal = computed(() => {
        return props.cartItems.reduce((total, item) => {
            return total + (item.quantity * (item.product.sale_price || item.product.price));
        }, 0);
    });

    const removeFromCart = (cartItemId) => {
        router.delete(route('cart.destroy', cartItemId), {
            preserveScroll: true,
            onSuccess: () => {
                // This will keep the response fresh with updated data
            },
            onError: (errors) => {
                console.error('Failed to remove item from cart:', errors);
            },
        });
    };
</script>
