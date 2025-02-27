<template>

    <Head title="Home" />
    <WebLayout>
        <v-container class="px-0" fluid>
            <!-- Hero Section -->
            <v-container>
                <v-row>

                    <!-- slider section -->
                    <v-col cols="12" md="9">
                        <v-card>
                            <v-carousel cycle height="450" hide-delimiter-background show-arrows="hover">
                                <v-carousel-item v-for="(slide, i) in sliderImages" :key="i" :src="slide.image_url"
                                    cover>
                                    <div class="slider-content pa-8">
                                        <h2 class="text-h4 font-weight-bold">{{ slide.title }}</h2>
                                        <p class="mt-4 text-body-1">{{ slide.description }}</p>
                                        <Link :href="slide.link">
                                        <v-btn color="primary" size="large" class="mt-6">
                                            Shop Now
                                        </v-btn>
                                        </Link>
                                    </div>
                                </v-carousel-item>
                            </v-carousel>
                        </v-card>
                    </v-col>

                    <!-- side banners -->
                    <v-col cols="12" md="3" class="d-none d-md-block">
                        <v-card>
                            <v-row>
                                <v-col v-for="(banner, index) in sideBanners" :key="index" cols="12" md="12">
                                    <v-img :src="banner.image_url" cover height="450"></v-img>
                                </v-col>
                            </v-row>
                        </v-card>
                    </v-col>

                </v-row>
            </v-container>

            <!-- Information Section -->
            <InformationSection />

            <!-- Categories Section -->
            <section class="py-12 bg-grey-lighten-4">
                <v-container>
                    <h2 class="text-h5 mb-6">Shop By Category</h2>
                    <v-row>
                        <v-col v-for="category in featuredCategories" :key="category.id" cols="6" sm="4" md="2">
                            <v-hover v-slot="{ isHovering, props }">
                                <Link :href="route('categories.show', category.slug)">
                                <v-card v-bind="props" :elevation="isHovering ? 4 : 1">
                                    <v-img :src="category.image_url" height="150" cover>
                                        <div class="category-overlay">
                                            <span class="text-subtitle-1 font-weight-bold">{{ category.name }}</span>
                                            <small class="d-block mt-1">{{ category.products_count }} Products</small>
                                        </div>
                                    </v-img>
                                </v-card>
                                </Link>
                            </v-hover>
                        </v-col>
                    </v-row>
                </v-container>
            </section>

            <!-- Promo Banners -->
            <BannerSection :banners="promoBanners" />

            <!-- Featured Products -->
            <ProductList title="Featured Products" :products="featuredProducts"
                :view-all-link="{ name: 'categories.index', params: { featured: true } }" />

            <!-- New Arrivals -->
            <ProductList title="New Arrivals" :products="newArrivals"
                :view-all-link="{ name: 'categories.index', params: { sort: 'newest' } }"
                background="bg-grey-lighten-4" />

            <!-- Best Sellers -->
            <ProductList title="Best Sellers" :products="bestSellers"
                :view-all-link="{ name: 'categories.index', params: { sort: 'best-selling' } }" />

        </v-container>
    </WebLayout>
</template>

<script setup>
    import WebLayout from '@/Layouts/WebLayout.vue'
    import BannerSection from '@/Components/BannerSection.vue'
    import ProductList from '@/Components/ProductList.vue'
    import InformationSection from '@/Components/InformationSection.vue'
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
        /*background: rgba(0, 0, 0, 0.192);*/
        border-radius: 8px;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        color: white;
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
