<template>
    <v-hover v-slot="{ isHovering, props }">
        <v-card v-bind="props" :elevation="isHovering ? 4 : 1" :class="{ 'on-hover': isHovering }">
            <Link :href="route('products.show', { slug: product.slug })">
                <v-img :src="product.primary_image_url" height="200" cover>
                    <div class="product-overlay-left">
                        <v-btn icon color="primary" variant="tonal" @click.prevent="showQuickView">
                            <v-icon>mdi-eye</v-icon>
                        </v-btn>
                        <v-btn icon :color="isInWishlist ? 'error' : 'primary'" variant="tonal" class="mt-2" @click.prevent="toggleWishlist">
                            <v-icon>{{ isInWishlist ? 'mdi-heart' : 'mdi-heart-outline' }}</v-icon>
                        </v-btn>
                    </div>
                    <div class="product-overlay-right">
                        <v-chip v-if="product.sale_price" color="error" small class="mb-2">Sale</v-chip>
                    </div>
                </v-img>

                <v-card-item>
                    <v-chip :color="product.stock > 0 ? 'success' : 'error'" density="compact" size="small" variant="flat">
                        {{ product.stock > 0 ? 'In Stock' : 'Out of Stock' }}
                    </v-chip>
                    <v-card-title class="text-subtitle-1">{{ product.name }}</v-card-title>
                    <v-card-subtitle>
                        <div class="d-flex align-center">
                            <span :class="{ 'text-decoration-line-through': product.sale_price }" class="mr-2">
                                ${{ product.price }}
                            </span>
                            <span v-if="product.sale_price" class="error--text">${{ product.sale_price }}</span>
                        </div>
                    </v-card-subtitle>
                </v-card-item>
            </Link>

            <v-card-actions>
                <v-btn color="primary" variant="tonal" :disabled="product.stock === 0" block @click.prevent="addToCart"
                    prepend-icon="mdi-cart-plus">
                    Add to Cart
                </v-btn>
            </v-card-actions>
        </v-card>

        <!-- Quick View Dialog -->
        <ProductQuickView v-model="quickViewDialog" :product="product" @add-to-cart="addToCart" />
    </v-hover>
</template>

<script setup>
    import { Link } from '@inertiajs/vue3'
    import { ref, computed } from 'vue'
    import ProductQuickView from './ProductQuickView.vue'
    import { router, usePage } from '@inertiajs/vue3'

    const props = defineProps({
        product: {
            type: Object,
            required: true
        },
        wishlistItems: {
            type: Array,
            default: () => []
        }
    });

    const quickViewDialog = ref(false);

    // Check if product is in user's wishlist
    const isInWishlist = computed(() => {
        if (!usePage().props.auth.user) return false;

        const items = usePage().props.auth.wishlistItems || [];
        return items.some(item => item.product_id === props.product.id);
    });

    const addToCart = () => {
        if (!usePage().props.auth.user) {
            router.visit(route('login'), {
                only: ['auth']
            });
            return;
        }

        router.post(route('cart.store'), {
            product_id: props.product.id,
            quantity: 1
        }, {
            preserveScroll: true,
            onError: (errors) => {
                // Handle errors here
                console.error(errors);
            },
            onSuccess: (page) => {
                // Show success message
                if (page.props.flash.success) {
                    // Optionally show success message
                }
            }
        });
    };

    const toggleWishlist = () => {
        if (!usePage().props.auth.user) {
            router.visit(route('login'), {
                only: ['auth']
            });
            return;
        }

        router.post(route('wishlist.toggle'), {
            product_id: props.product.id
        }, {
            preserveScroll: true,
        });
    };

    const showQuickView = () => {
        quickViewDialog.value = true;
    };
</script>

<style scoped>
    .product-overlay-right {
        position: absolute;
        top: 10px;
        right: 10px;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
    }

    .product-overlay-left {
        position: absolute;
        top: 10px;
        left: 10px;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        z-index: 1;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .on-hover .product-overlay-left {
        opacity: 1;
    }

    .on-hover {
        transform: scale(1.02);
        transition: all 0.3s;
    }
</style>
