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
                                <v-btn :to="slide.link" color="primary" size="large" class="mt-6">Shop Now</v-btn>
                            </div>
                        </v-carousel-item>
                    </v-carousel>
                </v-col>
                <v-col cols="12" md="3" class="d-none d-md-block">
                    <v-row>
                        <v-col v-for="(banner, index) in sideBanners" :key="index" cols="12">
                            <v-img :src="banner.image" height="220" cover></v-img>
                        </v-col>
                    </v-row>
                </v-col>
            </v-row>

            <!-- Categories Section -->
            <section class="py-12 bg-grey-lighten-4">
                <v-container>
                    <h2 class="text-h5 mb-6">Shop By Category</h2>
                    <v-row>
                        <v-col v-for="category in featuredCategories" :key="category.id" cols="6" sm="4" md="2">
                            <v-hover v-slot="{ isHovering, props }">
                                <v-card v-bind="props" :elevation="isHovering ? 4 : 1"
                                    :to="route('categories.show', category.slug)">
                                    <v-img :src="category.image" height="150" cover>
                                        <div class="category-overlay">
                                            <span class="text-subtitle-1 font-weight-bold">{{ category.name }}</span>
                                            <small class="d-block mt-1">{{ category.products_count }} Products</small>
                                        </div>
                                    </v-img>
                                </v-card>
                            </v-hover>
                        </v-col>
                    </v-row>
                </v-container>
            </section>

            <!-- Banner Section -->
            <BannerSection :banners="promoBanners" />

            <!-- Featured Products -->
            <ProductList
                title="Featured Products"
                :products="featuredProducts"
                :view-all-link="route('categories.index', { featured: true })"
            />

            <!-- New Arrivals -->
            <ProductList
                title="New Arrivals"
                :products="newArrivals"
                :view-all-link="route('categories.index', { sort: 'newest' })"
                background="bg-grey-lighten-4"
            />

            <!-- Best Sellers -->
            <ProductList
                title="Best Sellers"
                :products="bestSellers"
                :view-all-link="route('categories.index', { sort: 'best-selling' })"
            />
        </v-container>
    </WebLayout>
</template>

<script setup>
import WebLayout from '@/Layouts/WebLayout.vue'
import BannerSection from '@/Components/BannerSection.vue'
import ProductList from '@/Components/ProductList.vue'
import { defineProps } from 'vue'

defineProps({
    sliderImages: Array,
    sideBanners: Array,
    featuredProducts: Array,
    featuredCategories: Array,
    newArrivals: Array,
    bestSellers: Array,
    promoBanners: Array
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
</style>
