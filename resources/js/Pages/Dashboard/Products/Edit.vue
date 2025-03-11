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
                            <ProductForm
                                :modelValue="formData"
                                @update:modelValue="formData = $event"
                                :product="product"
                                :categories="categories"
                                :brands="brands"
                                :processing="processing"
                                :errors="errors"
                                @submit="updateProduct"
                            />
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
    import { Head, Link, router } from '@inertiajs/vue3';
    import { ref, onMounted } from 'vue';

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

    // Form data initialized from product
    const formData = ref({
        name: props.product.name,
        description: props.product.description || '',
        category_id: props.product.category_id,
        brand_id: props.product.brand_id,
        price: props.product.price,
        sale_price: props.product.sale_price || null,
        stock: props.product.stock,
        status: props.product.status,
        featured: props.product.featured,
        images: [],
        remove_images: [],
        primary_image: props.product.product_images?.find(img => img.is_primary)?.id || null
    });

    // Check for flash messages from the backend
    onMounted(() => {
        if (props.flash?.success) {
            flashMessage.value = props.flash.success;
            showSuccessMessage.value = true;
        }

        if (props.flash?.error) {
            errorMessage.value = props.flash.error;
            showErrorMessage.value = true;
        }
    });

    const updateProduct = (data) => {
        processing.value = true;

        // Create FormData for file uploads
        const form = new FormData();

        // Add method for Laravel to recognize as PUT request
        form.append('_method', 'PUT');

        // Add all text/number fields
        Object.keys(data).forEach(key => {
            // Skip image files and handle special cases
            if (key !== 'images' && key !== 'remove_images' && data[key] !== undefined) {
                if (key === 'primary_image' && data[key] !== null) {
                    form.append(key, data[key]);
                } else if (typeof data[key] === 'boolean') {
                    form.append(key, data[key] ? '1' : '0');
                } else if (data[key] !== null) {
                    form.append(key, data[key]);
                }
            }
        });

        // Add image files
        if (data.images && data.images.length > 0) {
            data.images.forEach((file, index) => {
                form.append(`images[${index}]`, file);
            });
        }

        // Add remove_images array
        if (data.remove_images && data.remove_images.length > 0) {
            data.remove_images.forEach((id, index) => {
                form.append(`remove_images[${index}]`, id);
            });
        }

        router.post(route('dashboard.products.update', props.product.uuid), form, {
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
