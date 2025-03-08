<template>

    <Head :title="title || 'Categories'" />
    <WebLayout>
        <v-container>
            <Breadcrumbs :items="breadcrumbs" />

            <!-- Filtered Products View -->
            <template v-if="products">
                <h1 class="text-h4 mb-6">{{ title }}</h1>
                <v-row>
                    <v-col v-for="product in products.data" :key="product.id" cols="12" sm="6" md="3">
                        <ProductCard :product="product" />
                    </v-col>
                </v-row>

                <!-- Pagination -->
                <div class="mt-6 d-flex justify-center">
                    <Pagination :links="products.links" />
                </div>
            </template>

            <!-- Categories View -->
            <template v-else>
                <div v-for="category in mainCategories" :key="category.id" class="mb-8">
                    <ProductList :title="category.name" :products="category.products"
                        :view-all-link="{ name: 'categories.show', params: { slug: category.slug }}"
                        :background="category.id % 2 === 0 ? 'grey-lighten-4' : ''" />
                </div>
            </template>
        </v-container>
    </WebLayout>
</template>

<script setup>
    import WebLayout from '@/Layouts/WebLayout.vue'
    import ProductList from '@/Components/ProductList.vue'
    import ProductCard from '@/Components/ProductCard.vue'
    import Pagination from '@/Components/Pagination.vue'
    import Breadcrumbs from '@/Components/Breadcrumbs.vue'
    import { Head } from '@inertiajs/vue3'

    defineProps({
        mainCategories: {
            type: Array,
            default: () => []
        },
        products: {
            type: Object,
            default: null
        },
        title: {
            type: String,
            default: 'Categories'
        },
        breadcrumbs: {
            type: Array,
            required: true
        },
        filters: {
            type: Object,
            default: () => ({})
        }
    })
</script>

<style scoped></style>
