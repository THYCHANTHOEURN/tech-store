<template>

    <Head :title="`Edit ${product.name}`" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Edit Product: {{ product.name }}
                </h2>
                <v-spacer></v-spacer>
                <Link :href="route('dashboard.products.index')">
                <v-btn color="secondary" prepend-icon="mdi-arrow-left" variant="outlined">
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
                            <ProductForm :categories="categories" :brands="brands" :product="product"
                                @submit="updateProduct" :processing="processing" :errors="errors" />
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>

        <!-- Flash Messages -->
        <v-snackbar v-model="showSuccessMessage" color="success" timeout="3000" location="top">
            {{ flashMessage }}
        </v-snackbar>

        <v-snackbar v-model="showErrorMessage" color="error" timeout="3000" location="top">
            {{ errorMessage }}
        </v-snackbar>
    </DashboardLayout>
</template>

<script setup>
    import DashboardLayout from '@/Layouts/DashboardLayout.vue';
    import ProductForm from '@/Components/Dashboard/ProductForm.vue';
    import { Head, Link, useForm } from '@inertiajs/vue3';
    import { ref } from 'vue';

    const props = defineProps({
        product: Object,
        categories: Array,
        brands: Array,
        errors: Object,
        flash: Object
    });

    const processing = ref(false);
    const showSuccessMessage = ref(false);
    const showErrorMessage = ref(false);
    const flashMessage = ref('');
    const errorMessage = ref('');

    // Check for flash messages from the backend
    if (props.flash?.success) {
        flashMessage.value = props.flash.success;
        showSuccessMessage.value = true;
    }

    if (props.flash?.error) {
        errorMessage.value = props.flash.error;
        showErrorMessage.value = true;
    }

    const form = useForm({
        name: props.product.name,
        category_id: props.product.category_id,
        brand_id: props.product.brand_id,
        price: props.product.price,
        sale_price: props.product.sale_price || '',
        stock: props.product.stock,
        description: props.product.description || '',
        status: props.product.status,
        featured: props.product.featured,
        images: [],
        remove_images: [],
        primary_image: null
    });

    const updateProduct = (formData) => {
        processing.value = true;

        form.name = formData.name;
        form.category_id = formData.category_id;
        form.brand_id = formData.brand_id;
        form.price = formData.price;
        form.sale_price = formData.sale_price;
        form.stock = formData.stock;
        form.description = formData.description;
        form.status = formData.status;
        form.featured = formData.featured;
        form.images = formData.images;
        form.remove_images = formData.remove_images || [];
        form.primary_image = formData.primary_image;

        form.put(route('dashboard.products.update', props.product.uuid), {
            onSuccess: () => {
                processing.value = false;
                flashMessage.value = 'Product updated successfully';
                showSuccessMessage.value = true;
            },
            onError: (err) => {
                processing.value = false;

                if (err.message) {
                    errorMessage.value = err.message;
                    showErrorMessage.value = true;
                }
            },
            preserveScroll: true
        });
    };
</script>
