<template>
    <Head title="Categories" />
    <WebLayout>
        <v-container>
            <Breadcrumbs :items="breadcrumbs" />

            <div v-for="category in mainCategories" :key="category.id" class="mb-8">
                <ProductList
                    :title="category.name"
                    :products="category.products"
                    :view-all-link="`/categories/${category.slug}`"
                    :background="category.id % 2 === 0 ? 'grey-lighten-4' : ''"
                />
            </div>
        </v-container>
    </WebLayout>
</template>

<script setup>
    import WebLayout from '@/Layouts/WebLayout.vue'
    import ProductList from '@/Components/ProductList.vue'
    import Breadcrumbs from '@/Components/Breadcrumbs.vue'
    import { Head } from '@inertiajs/vue3'
    import { defineProps } from 'vue'

    defineProps({
        mainCategories: {
            type: Array,
            required: true
        },
        breadcrumbs: {
            type: Array,
            required: true
        }
    })
</script>

<style scoped>

</style>

