<template>
    <v-dialog v-model="dialog" max-width="900px">
        <v-card>
            <v-card-text class="pa-0">
                <v-container fluid class="pa-0">
                    <v-row no-gutters>
                        <!-- Product Image -->
                        <v-col cols="12" md="6" class="bg-grey-lighten-4">
                            <v-img :src="product.primary_image_url" height="400" cover class="h-100">
                                <div class="product-badges">
                                    <v-chip v-if="product.sale_price" color="error" size="small" class="mb-2">
                                        Sale
                                    </v-chip>
                                </div>
                            </v-img>
                        </v-col>

                        <!-- Product Details -->
                        <v-col cols="12" md="6" class="pa-6">
                            <v-btn icon="mdi-close" variant="text" size="small" class="float-right"
                                @click="dialog = false" />

                            <div class="mb-4">
                                <v-chip :color="product.stock > 0 ? 'success' : 'error'" size="small">
                                    {{ product.stock > 0 ? 'In Stock' : 'Out of Stock' }}
                                </v-chip>
                            </div>

                            <h2 class="text-h5 mb-2">{{ product.name }}</h2>

                            <!-- Price Section -->
                            <div class="d-flex align-center mb-4">
                                <span class="text-h6" :class="{ 'text-decoration-line-through': product.sale_price }">
                                    ${{ product.price }}
                                </span>
                                <span v-if="product.sale_price" class="text-h6 error--text ml-2">
                                    ${{ product.sale_price }}
                                </span>
                            </div>

                            <p class="text-body-1 mb-6">{{ product.description }}</p>

                            <!-- Quantity and Add to Cart -->
                            <div class="d-flex flex-column gap-4">
                                <div class="d-flex align-center gap-4">
                                    <v-text-field v-model="quantity" type="number" label="Quantity" min="1"
                                        :max="product.stock" hide-details density="compact" style="max-width: 100px" />
                                    <v-btn color="primary" :disabled="product.stock === 0" @click="handleAddToCart" prepend-icon="mdi-cart-plus">
                                        Add to Cart
                                    </v-btn>
                                </div>

                                <Link :href="route('products.show', { slug: product.slug })"
                                    class="text-decoration-none">
                                <v-btn color="primary" variant="outlined" block>
                                    View Full Details
                                </v-btn>
                                </Link>
                            </div>

                            <!-- Product Meta -->
                            <v-divider class="my-6" />
                            <div class="product-meta">
                                <div class="d-flex gap-2 mb-2">
                                    <span class="font-weight-medium">SKU:</span>
                                    <span>{{ product.sku }}</span>
                                </div>
                                <div v-if="product.brand" class="d-flex gap-2 mb-2">
                                    <span class="font-weight-medium">Brand:</span>
                                    <span>{{ product.brand.name }}</span>
                                </div>
                                <div class="d-flex gap-2">
                                    <span class="font-weight-medium">Category:</span>
                                    <span>{{ product.category.name }}</span>
                                </div>
                            </div>
                        </v-col>
                    </v-row>
                </v-container>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script setup>
    import { Link } from '@inertiajs/vue3'
    import { computed, ref } from 'vue'

    const props = defineProps({
        modelValue: Boolean,
        product: {
            type: Object,
            required: true
        }
    })

    const emit = defineEmits(['update:modelValue', 'add-to-cart'])

    const dialog = computed({
        get: () => props.modelValue,
        set: (value) => emit('update:modelValue', value)
    })

    const quantity = ref(1)

    const handleAddToCart = () => {
        emit('add-to-cart', {
            productId: props.product.id,
            quantity: quantity.value
        })
        dialog.value = false
    }
</script>

<style scoped>
    .product-badges {
        position: absolute;
        top: 12px;
        right: 12px;
    }

    .product-meta {
        font-size: 0.875rem;
        color: rgba(0, 0, 0, 0.6);
    }
</style>
