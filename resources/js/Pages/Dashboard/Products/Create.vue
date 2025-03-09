<template>
    <Head title="Create Product" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Create New Product
                </h2>
                <v-spacer></v-spacer>
                <Link :href="route('dashboard.products.index')">
                    <v-btn
                        color="secondary"
                        prepend-icon="mdi-arrow-left"
                        variant="outlined"
                    >
                        Back to Products
                    </v-btn>
                </Link>
            </div>
        </template>

        <v-container fluid class="py-8">
            <v-row justify="center">
                <v-col cols="12" md="10" lg="8">
                    <v-card>
                        <v-card-text>
                            <ProductForm
                                :categories="categories"
                                :brands="brands"
                                @submit="createProduct"
                                :processing="processing"
                                :errors="errors"
                            />
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>

        <!-- Flash Messages -->
        <v-snackbar
            v-model="showErrorMessage"
            color="error"
            timeout="3000"
            location="top"
        >
            {{ errorMessage }}
        </v-snackbar>
    </DashboardLayout>
</template>

<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import ProductForm from '@/Components/Dashboard/ProductForm.vue';
import { Head, router, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    categories: Array,
    brands: Array,
    errors: Object
});

const processing = ref(false);
const showErrorMessage = ref(false);
const errorMessage = ref('');

const createProduct = (formData) => {
    processing.value = true;

    console.log('Submitting product data:', formData);

    // Create FormData for file uploads
    const data = new FormData();
    data.append('name', formData.name);
    data.append('category_id', formData.category_id);
    data.append('brand_id', formData.brand_id);
    data.append('price', formData.price);

    if (formData.sale_price) {
        data.append('sale_price', formData.sale_price);
    }

    data.append('stock', formData.stock);

    if (formData.description) {
        data.append('description', formData.description);
    }

    data.append('status', formData.status ? '1' : '0');
    data.append('featured', formData.featured ? '1' : '0');

    // Append image files
    if (formData.images && formData.images.length > 0) {
        formData.images.forEach((file, index) => {
            data.append(`images[${index}]`, file);
        });
    }

    router.post(route('dashboard.products.store'), data, {
        onSuccess: () => {
            processing.value = false;
        },
        onError: (errors) => {
            processing.value = false;
            console.error('Form submission errors:', errors);

            if (errors.message) {
                errorMessage.value = errors.message;
                showErrorMessage.value = true;
            }
        },
        forceFormData: true
    });
};
</script>
