<template>

    <Head title="Create Product" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ $t('Create New Product') }}
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
                            <ProductForm :modelValue="formData" @update:modelValue="formData = $event"
                                :categories="categories" :brands="brands" :processing="processing" :errors="errors"
                                @submit="createProduct" />
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
    import { Head, router, Link } from '@inertiajs/vue3';
    import { ref } from 'vue';

    const props = defineProps({
        categories: Array,
        brands: Array,
        errors: Object,
        flash: Object
    });

    const processing = ref(false);

    // Form data with default values
    const formData = ref({
        name: '',
        description: '',
        category_id: null,
        brand_id: null,
        price: null,
        sale_price: null,
        stock: 0,
        status: true,
        featured: false,
        images: [],
        remove_images: [],
        primary_image: null
    });

    const createProduct = (data) => {
        processing.value = true;

        // Create FormData for file uploads
        const form = new FormData();

        // Add all text/number fields
        Object.keys(data).forEach(key => {
            // Skip image files and arrays that need special handling
            if (key !== 'images' && key !== 'remove_images' && data[key] !== null && data[key] !== undefined) {
                // Handle boolean values
                if (typeof data[key] === 'boolean') {
                    form.append(key, data[key] ? '1' : '0');
                } else {
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

        router.post(route('dashboard.products.store'), form, {
            onFinish: () => {
                processing.value = false;
            },
            forceFormData: true
        });
    };
</script>
