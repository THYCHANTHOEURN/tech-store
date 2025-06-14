<template>
    <Head title="Wishlist" />
    <WebLayout>
        <v-container>
            <h1 class="text-h4 mb-6">My Wishlist</h1>

            <v-row v-if="wishlistItems.length">
                <v-col cols="12">
                    <v-card>
                        <v-data-table
                            :headers="headers"
                            :items="wishlistItems"
                            class="elevation-1"
                        >
                            <!-- Product Image and Name -->
                            <template v-slot:item.product="{ item }">
                                <div class="d-flex align-center">
                                    <v-img
                                        :src="item.product.primary_image_url"
                                        width="50"
                                        height="50"
                                        cover
                                        class="mr-3"
                                    ></v-img>
                                    <Link :href="route('products.show', { slug: item.product.slug })" class="text-decoration-none">
                                        {{ item.product.name }}
                                    </Link>
                                </div>
                            </template>

                            <!-- Price -->
                            <template v-slot:item.price="{ item }">
                                <div>
                                    <span :class="{ 'text-decoration-line-through': item.product.sale_price }">
                                        ${{ item.product.price }}
                                    </span>
                                    <span v-if="item.product.sale_price" class="error--text ml-2">
                                        ${{ item.product.sale_price }}
                                    </span>
                                </div>
                            </template>

                            <!-- Stock Status -->
                            <template v-slot:item.stock="{ item }">
                                <v-chip
                                    :color="getStockColor(item.product)"
                                    size="small"
                                    :prepend-icon="getStockIcon(item.product)"
                                >
                                    {{ getStockStatusText(item.product) }}
                                </v-chip>
                            </template>

                            <!-- Actions -->
                            <template v-slot:item.actions="{ item }">
                                <div class="d-flex gap-2">
                                    <v-btn
                                        color="primary"
                                        :disabled="item.product.stock === 0"
                                        size="small"
                                        @click="addToCart(item.product.id)"
                                        prepend-icon="mdi-cart-plus"
                                    >
                                        Add to Cart
                                    </v-btn>
                                    <v-btn
                                        color="error"
                                        size="small"
                                        variant="text"
                                        @click="removeFromWishlist(item.id)"
                                        prepend-icon="mdi-delete"
                                    >
                                        Remove
                                    </v-btn>
                                </div>
                            </template>
                        </v-data-table>
                    </v-card>
                </v-col>
            </v-row>

            <v-row v-else>
                <v-col cols="12" class="text-center">
                    <p class="text-h6 mb-4">Your wishlist is empty</p>
                    <Link :href="route('index')" class="text-decoration-none">
                        <v-btn color="primary">Continue Shopping</v-btn>
                    </Link>
                </v-col>
            </v-row>
        </v-container>
    </WebLayout>
</template>

<script setup>
    import WebLayout from '@/Layouts/WebLayout.vue';
    import { Link, router } from '@inertiajs/vue3';
    import { ref, computed } from 'vue';

    const props = defineProps({
        wishlistItems: {
            type: Array,
            default: () => [],
        },
    });

    const headers = [
        { title: 'Product', key: 'product', align: 'start' },
        { title: 'Price', key: 'price' },
        { title: 'Status', key: 'stock' },
        { title: 'Actions', key: 'actions', sortable: false, align: 'end' },
    ];

    const addToCart = (productId) => {
        router.post(route('cart.store'), {
            product_id: productId,
            quantity: 1
        }, {
            preserveScroll: true
        });
    };

    const removeFromWishlist = (wishlistItemId) => {
        router.delete(route('wishlist.destroy', wishlistItemId), {
            preserveScroll: true,
            onSuccess: () => {
                // This will keep the response fresh with updated data
            }
        });
    };

    const getStockColor = (product) => {
        if (product.stock <= 0) return 'error'
        if (product.is_critical_stock) return 'warning'
        if (product.is_low_stock) return 'orange'
        return 'success'
    }

    const getStockIcon = (product) => {
        if (product.stock <= 0) return 'mdi-alert-circle'
        if (product.is_critical_stock) return 'mdi-alert'
        if (product.is_low_stock) return 'mdi-trending-down'
        return 'mdi-check-circle'
    }

    const getStockStatusText = (product) => {
        if (product.stock <= 0) return 'Out of Stock'
        if (product.is_critical_stock) return 'Critical Stock'
        if (product.is_low_stock) return 'Low Stock'
        return `${product.stock} in Stock`
    }
</script>
