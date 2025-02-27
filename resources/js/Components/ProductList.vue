<template>
    <section :class="['product-section py-12', background]">
        <v-container>
            <div class="d-flex align-center justify-space-between mb-6">
                <h2 class="text-h5">{{ title }}</h2>
                <Link v-if="viewAllLink"
                    class="v-btn v-btn--variant-text v-btn--density-default v-btn--size-default v-theme--light text-primary"
                    :href="typeof viewAllLink === 'string' ? viewAllLink : route(viewAllLink.name, viewAllLink.params || {})">
                View All
                </Link>
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
    import { Link } from '@inertiajs/vue3';
    import ProductCard from '@/Components/ProductCard.vue';

    defineProps({
        title: String,
        products: Array,
        viewAllLink: [String, Object],
        background: {
            type: String,
            default: ''
        }
    });
</script>
