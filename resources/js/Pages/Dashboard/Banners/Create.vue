<template>
    <Head title="Create Banner" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Create New Banner
                </h2>
                <v-spacer></v-spacer>
                <Link :href="route('dashboard.banners.index')">
                <v-btn color="secondary" prepend-icon="mdi-arrow-left" variant="outlined">
                    Back to Banners
                </v-btn>
                </Link>
            </div>
        </template>

        <v-container fluid class="py-8">
            <v-row justify="center">
                <v-col cols="12" md="10" lg="8">
                    <v-card>
                        <v-card-text>
                            <BannerForm :modelValue="formData" @update:modelValue="formData = $event"
                                :positions="positions" :processing="processing" :errors="errors" @submit="createBanner" />
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </DashboardLayout>
</template>

<script setup>
    import DashboardLayout from '@/Layouts/DashboardLayout.vue';
    import BannerForm from '@/Components/Dashboard/BannerForm.vue';
    import { Head, router, Link } from '@inertiajs/vue3';
    import { ref } from 'vue';

    const props = defineProps({
        positions: {
            type: Array,
            required: true
        },
        errors: Object,
        flash: Object
    });

    const processing = ref(false);

    // Form data with default values
    const formData = ref({
        title: '',
        link: '',
        position: 'slider', // Default position
        status: true,
        image: null,
        image_url: null,
    });

    // Create new banner
    const createBanner = (data) => {
        processing.value = true;

        // Create FormData for file uploads
        const form = new FormData();

        // Append all form data
        Object.keys(data).forEach(key => {
            // Handle file upload differently
            if (key === 'image' && data[key]) {
                form.append(key, data[key]);
            }
            // Handle boolean status field properly
            else if (key === 'status') {
                form.append(key, data[key] ? '1' : '0');  // Convert boolean to '1'/'0'
            }
            // Skip image_url as it's just for preview display
            else if (key !== 'image_url' && data[key] !== null && data[key] !== undefined) {
                form.append(key, data[key]);
            }
        });

        router.post(route('dashboard.banners.store'), form, {
            onFinish: () => {
                processing.value = false;
            },
            forceFormData: true
        });
    };
</script>