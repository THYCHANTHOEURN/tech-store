<template>
    <Head :title="`Edit ${product.name}`" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ $t('Edit Product') }}: {{ product.name }}
                </h2>
                <v-spacer></v-spacer>
                <Link :href="route('dashboard.products.index')">
                <v-btn color="secondary" prepend-icon="mdi-arrow-left" variant="outlined">
                    {{ $t('Back to Products') }}
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
    </DashboardLayout>
</template>

<script setup>
    import DashboardLayout from '@/Layouts/DashboardLayout.vue';
    import ProductForm from '@/Components/Dashboard/ProductForm.vue';
    import { Head, Link, router } from '@inertiajs/vue3';
    import { ref } from 'vue';

    const props = defineProps({
        product: Object,
        categories: Array,
        brands: Array,
        errors: Object,
        flash: Object
    });

    const processing = ref(false);

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
            onFinish: () => {
                processing.value = false;
            },
            preserveScroll: true
        });
    };
</script>
