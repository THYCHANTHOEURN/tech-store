<template>

    <Head :title="category.name" />
    <WebLayout>
        <v-container>
            <Breadcrumbs :items="breadcrumbs" />

            <h1 class="text-h4 mb-6">{{ category.name }}</h1>

            <!-- Child Categories Section -->
            <template v-if="childCategories.length > 0">
                <v-row class="mb-8">
                    <v-col v-for="child in childCategories" :key="child.id" cols="12" sm="6" md="3">
                        <v-card :href="route('categories.show', { slug: child.slug })" :title="child.name"
                            :text="child.description" class="h-100">
                            <v-img v-if="child.image_url" :src="child.image_url" height="200" cover />
                        </v-card>
                    </v-col>
                </v-row>
            </template>

            <!-- Products Grid -->
            <v-row>
                <v-col v-for="product in products.data" :key="product.id" cols="12" sm="6" md="3">
                    <ProductCard :product="product" />
                </v-col>
            </v-row>

            <!-- Pagination -->
            <div class="mt-6 d-flex justify-center">
                <Pagination :links="products.links" />
            </div>
        </v-container>
    </WebLayout>
</template>

<script setup>
    import WebLayout from '@/Layouts/WebLayout.vue'
    import ProductCard from '@/Components/ProductCard.vue'
    import Pagination from '@/Components/Pagination.vue'
    import Breadcrumbs from '@/Components/Breadcrumbs.vue'
    import { Head } from '@inertiajs/vue3'

    defineProps({
        category: {
            type: Object,
            required: true
        },
        products: {
            type: Object,
            required: true
        },
        childCategories: {
            type: Array,
            default: () => []
        },
        breadcrumbs: {
            type: Array,
            required: true
        }
    })
</script>
