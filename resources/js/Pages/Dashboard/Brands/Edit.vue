<template>

    <Head :title="`Edit ${brand.name}`" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ $t('Edit Brand') }}: {{ brand.name }}
                </h2>
                <v-spacer></v-spacer>
                <Link :href="route('dashboard.brands.index')">
                <v-btn color="secondary" prepend-icon="mdi-arrow-left" variant="outlined">
                    {{ $t('Back to Brands') }}
                </v-btn>
                </Link>
            </div>
        </template>

        <v-container fluid class="py-8">
            <v-row justify="center">
                <v-col cols="12" md="10" lg="8">
                    <v-card>
                        <v-card-text>
                            <BrandForm :modelValue="formData" @update:modelValue="formData = $event" :brand="brand"
                                :processing="processing" :errors="errors" @submit="updateBrand" />
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </DashboardLayout>
</template>

<script setup>
    import DashboardLayout from '@/Layouts/DashboardLayout.vue';
    import BrandForm from '@/Components/Dashboard/BrandForm.vue';
    import { Head, Link, router } from '@inertiajs/vue3';
    import { ref } from 'vue';

    const props = defineProps({
        brand: Object,
        errors: Object,
        flash: Object
    });

    const processing = ref(false);

    // Form data initialized from brand
    const formData = ref({
        name: props.brand.name,
        description: props.brand.description || '',
        status: props.brand.status,
        logo: null,
        logo_url: props.brand.logo_url,
    });

    const updateBrand = (data) => {
        processing.value = true;

        // Create FormData for file uploads
        const form = new FormData();

        // Add method for Laravel to recognize as PUT request
        form.append('_method', 'PUT');

        // Append all form data
        Object.keys(data).forEach(key => {
            // Handle file upload differently
            if (key === 'logo' && data[key]) {
                form.append(key, data[key]);
            }
            // Handle boolean status field properly
            else if (key === 'status') {
                form.append(key, data[key] ? '1' : '0');  // Convert boolean to '1'/'0'
            }
            // Skip logo_url as it's just for preview display
            else if (key !== 'logo_url' && data[key] !== null && data[key] !== undefined) {
                form.append(key, data[key]);
            }
        });

        router.post(route('dashboard.brands.update', props.brand.uuid), form, {
            onFinish: () => {
                processing.value = false;
            },
            preserveScroll: true
        });
    };
</script>
