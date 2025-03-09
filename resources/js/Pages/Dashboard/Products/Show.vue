<template>

    <Head :title="product.name" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Product Details: {{ product.name }}
                </h2>
                <v-spacer></v-spacer>
                <Link :href="route('dashboard.products.edit', product.uuid)">
                    <v-btn color="warning" prepend-icon="mdi-pencil" class="mr-2">
                        Edit
                    </v-btn>
                </Link>
                <Link :href="route('dashboard.products.index')">
                    <v-btn color="secondary" prepend-icon="mdi-arrow-left" variant="outlined">
                        Back to Products
                    </v-btn>
                </Link>
            </div>
        </template>

        <v-container fluid class="py-8">
            <v-row>
                <!-- Product Images -->
                <v-col cols="12" md="5">
                    <v-card>
                        <v-card-title>
                            <div class="d-flex align-center">
                                <span>Product Images</span>
                                <v-chip v-if="product.stock <= 5 && product.stock > 0" color="warning" class="ml-2"
                                    size="small">
                                    Low Stock
                                </v-chip>
                                <v-chip v-if="product.stock === 0" color="error" class="ml-2" size="small">
                                    Out of Stock
                                </v-chip>
                            </div>
                        </v-card-title>
                        <v-window v-model="currentImage">
                            <v-window-item v-for="(image, i) in product.product_images" :key="i">
                                <v-img :src="image.image_url" height="400" cover class="bg-grey-lighten-2"></v-img>
                            </v-window-item>
                        </v-window>

                        <!-- Image thumbnails -->
                        <v-card-text v-if="product.product_images && product.product_images.length > 1">
                            <div class="d-flex overflow-x-auto gap-2 py-2">
                                <v-img v-for="(image, i) in product.product_images" :key="i" :src="image.image_url"
                                    width="60" height="60" cover class="rounded cursor-pointer"
                                    :class="{ 'border border-primary': currentImage === i }"
                                    @click="currentImage = i"></v-img>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>

                <!-- Product Information -->
                <v-col cols="12" md="7">
                    <v-card>
                        <v-card-title class="d-flex justify-space-between align-center">
                            <span>Product Information</span>
                            <div>
                                <v-chip :color="product.status ? 'success' : 'error'" size="small" class="mr-2">
                                    {{ product.status ? 'Active' : 'Inactive' }}
                                </v-chip>
                                <v-chip v-if="product.featured" color="primary" size="small">
                                    Featured
                                </v-chip>
                            </div>
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <v-row>
                                <v-col cols="12" md="6">
                                    <v-list>
                                        <v-list-item>
                                            <v-list-item-title>SKU</v-list-item-title>
                                            <v-list-item-subtitle>{{ product.sku }}</v-list-item-subtitle>
                                        </v-list-item>

                                        <v-list-item>
                                            <v-list-item-title>Category</v-list-item-title>
                                            <v-list-item-subtitle>{{ product.category?.name || 'N/A'
                                                }}</v-list-item-subtitle>
                                        </v-list-item>

                                        <v-list-item>
                                            <v-list-item-title>Brand</v-list-item-title>
                                            <v-list-item-subtitle>{{ product.brand?.name || 'N/A'
                                                }}</v-list-item-subtitle>
                                        </v-list-item>
                                    </v-list>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-list>
                                        <v-list-item>
                                            <v-list-item-title>Regular Price</v-list-item-title>
                                            <v-list-item-subtitle>${{ product.price }}</v-list-item-subtitle>
                                        </v-list-item>

                                        <v-list-item v-if="product.sale_price">
                                            <v-list-item-title>Sale Price</v-list-item-title>
                                            <v-list-item-subtitle class="text-error">${{ product.sale_price
                                                }}</v-list-item-subtitle>
                                        </v-list-item>

                                        <v-list-item>
                                            <v-list-item-title>Stock</v-list-item-title>
                                            <v-list-item-subtitle>{{ product.stock }} units</v-list-item-subtitle>
                                        </v-list-item>
                                    </v-list>
                                </v-col>
                            </v-row>

                            <v-divider class="my-4"></v-divider>

                            <div>
                                <h3 class="text-h6 mb-2">Description</h3>
                                <div v-if="product.description" class="product-description">
                                    <p>{{ product.description }}</p>
                                </div>
                                <div v-else class="text-grey">No description provided</div>
                            </div>
                        </v-card-text>
                    </v-card>

                    <!-- Reviews Section -->
                    <v-card class="mt-4" v-if="product.reviews && product.reviews.length > 0">
                        <v-card-title>Customer Reviews</v-card-title>
                        <v-divider></v-divider>
                        <v-list>
                            <v-list-item v-for="(review, index) in product.reviews" :key="index">
                                <v-list-item-title>
                                    <div class="d-flex align-center">
                                        <div>
                                            <v-rating v-model="review.rating" color="amber" size="small" readonly
                                                half-increments></v-rating>
                                        </div>
                                        <div class="ml-2 text-body-2">
                                            by {{ review.user?.name || 'Anonymous' }}
                                        </div>
                                    </div>
                                </v-list-item-title>
                                <v-list-item-subtitle>{{ review.comment }}</v-list-item-subtitle>
                            </v-list-item>
                        </v-list>
                    </v-card>

                    <v-card class="mt-4" v-else>
                        <v-card-text class="text-center text-grey">
                            No reviews yet
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </DashboardLayout>
</template>

<script setup>
    import DashboardLayout from '@/Layouts/DashboardLayout.vue';
    import { Head, Link } from '@inertiajs/vue3';
    import { ref } from 'vue';

    const props = defineProps({
        product: Object
    });

    const currentImage = ref(0);
</script>

<style scoped>
    .product-description {
        white-space: pre-line;
    }

    .border-primary {
        border: 2px solid var(--v-primary-base) !important;
    }

    .cursor-pointer {
        cursor: pointer;
    }
</style>
