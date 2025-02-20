<template>
    <section :class="['product-section py-12', background]">
        <v-container>
            <div class="d-flex align-center justify-space-between mb-6">
                <h2 class="text-h5">{{ title }}</h2>
                <v-btn v-if="viewAllLink" variant="text" color="primary" :to="viewAllLink">
                    View All
                </v-btn>
            </div>
            <v-row>
                <v-col v-for="product in products" :key="product.id" cols="12" sm="6" md="3">
                    <ProductCard :product="product" />
                </v-col>
            </v-row>
        </v-container>
    </section>
</template>

<script setup>
    import ProductCard from '@/Components/ProductCard.vue';

    defineProps({
        title: String,
        products: Array,
        viewAllLink: String,
        background: {
            type: String,
            default: ''
        }
    });
</script>
