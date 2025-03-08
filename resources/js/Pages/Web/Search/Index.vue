<template>
    <WebLayout>
        <v-container>
            <h1 class="text-h4 mb-6">
                Search Results for "{{ searchTerm }}"
            </h1>

            <div v-if="products.data.length > 0">
                <div class="d-flex align-center mb-4">
                    <span class="text-subtitle-1">{{ products.total }} results found</span>
                </div>

                <v-row>
                    <v-col v-for="product in products.data" :key="product.id" cols="12" sm="6" md="4" lg="3">
                        <ProductCard :product="product" />
                    </v-col>
                </v-row>

                <div class="mt-4" v-if="products.last_page > 1">
                    <Pagination :links="products.links" />
                </div>
            </div>

            <div v-else class="text-center py-12">
                <v-icon size="64" class="mb-4">mdi-search-off</v-icon>
                <h2 class="text-h5 mb-2">No products found</h2>
                <p class="text-subtitle-1">Try different keywords or browse our categories</p>
            </div>
        </v-container>
    </WebLayout>
</template>

<script setup>
    import WebLayout from '@/Layouts/WebLayout.vue';
    import ProductCard from '@/Components/ProductCard.vue';
    import Pagination from '@/Components/Pagination.vue';

    const props = defineProps({
        products: Object,
        searchTerm: String
    });
</script>
