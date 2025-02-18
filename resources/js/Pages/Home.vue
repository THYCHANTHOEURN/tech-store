<template>
    <WebLayout>
        <v-container class="px-0" fluid>
            <!-- Hero Section -->
            <v-row no-gutters>
                <v-col cols="12" md="9">
                    <v-carousel cycle height="450" hide-delimiter-background show-arrows="hover">
                        <v-carousel-item v-for="(slide, i) in sliderImages" :key="i" :src="slide.image" cover>
                            <div class="slider-content pa-8">
                                <h2 class="text-h4 font-weight-bold white--text">{{ slide.title }}</h2>
                                <p class="mt-4 text-body-1 white--text">{{ slide.description }}</p>
                                <v-btn color="primary" size="large" class="mt-6">Shop Now</v-btn>
                            </div>
                        </v-carousel-item>
                    </v-carousel>
                </v-col>
                <v-col cols="12" md="3" class="d-none d-md-block">
                    <v-row>
                        <v-col cols="12">
                            <v-img src="/images/banners/banner1.jpg" height="220" cover></v-img>
                        </v-col>
                        <v-col cols="12">
                            <v-img src="/images/banners/banner2.jpg" height="220" cover></v-img>
                        </v-col>
                    </v-row>
                </v-col>
            </v-row>

            <!-- Featured Categories -->
            <section class="py-12 bg-grey-lighten-4">
                <v-container>
                    <h2 class="text-h5 mb-6">Shop By Category</h2>
                    <v-row>
                        <v-col v-for="category in featuredCategories" :key="category.id" cols="6" sm="4" md="2">
                            <v-hover v-slot="{ isHovering, props }">
                                <v-card v-bind="props" :elevation="isHovering ? 4 : 1"
                                    :class="{ 'on-hover': isHovering }" :to="route('categories.show', category.slug)">
                                    <v-img :src="category.image" height="150" cover>
                                        <div class="category-overlay">
                                            <span class="text-subtitle-1 font-weight-bold">{{ category.name }}</span>
                                        </div>
                                    </v-img>
                                </v-card>
                            </v-hover>
                        </v-col>
                    </v-row>
                </v-container>
            </section>

            <!-- Featured Products -->
            <section class="py-12">
                <v-container>
                    <div class="d-flex align-center justify-space-between mb-6">
                        <h2 class="text-h5">Featured Products</h2>
                        <v-btn variant="text" color="primary">View All</v-btn>
                    </div>
                    <v-row>
                        <v-col v-for="product in featuredProducts" :key="product.id" cols="12" sm="6" md="3">
                            <v-hover v-slot="{ isHovering, props }">
                                <v-card v-bind="props" :elevation="isHovering ? 4 : 1"
                                    :class="{ 'on-hover': isHovering }">
                                    <v-img :src="product.image" height="200" cover></v-img>
                                    <v-card-item>
                                        <v-card-title class="text-subtitle-1">{{ product.name }}</v-card-title>
                                        <v-card-subtitle>${{ product.price }}</v-card-subtitle>
                                    </v-card-item>
                                    <v-card-actions>
                                        <v-spacer></v-spacer>
                                        <v-btn color="primary" variant="tonal" prepend-icon="mdi-cart">
                                            Add to Cart
                                        </v-btn>
                                    </v-card-actions>
                                </v-card>
                            </v-hover>
                        </v-col>
                    </v-row>
                </v-container>
            </section>

            <!-- New Arrivals -->
            <section class="py-12 bg-grey-lighten-4">
                <v-container>
                    <div class="d-flex align-center justify-space-between mb-6">
                        <h2 class="text-h5">New Arrivals</h2>
                        <v-btn variant="text" color="primary">View All</v-btn>
                    </div>
                    <v-row>
                        <v-col v-for="product in newArrivals" :key="product.id" cols="12" sm="6" md="3">
                            // ...similar product card as featured products...
                        </v-col>
                    </v-row>
                </v-container>
            </section>
        </v-container>
    </WebLayout>
</template>

<script setup>
    import WebLayout from '@/Layouts/WebLayout.vue'
    import { defineProps } from 'vue'

    defineProps({
        sliderImages: Array,
        featuredProducts: Array,
        featuredCategories: Array,
        newArrivals: Array
    })
</script>

<style scoped>
    .slider-content {
        position: absolute;
        max-width: 500px;
        background: rgba(0, 0, 0, 0.6);
        border-radius: 8px;
    }

    .category-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 1rem;
        background: rgba(0, 0, 0, 0.6);
        color: white;
        text-align: center;
    }

    .on-hover {
        transform: scale(1.02);
        transition: all 0.3s;
    }
</style>
