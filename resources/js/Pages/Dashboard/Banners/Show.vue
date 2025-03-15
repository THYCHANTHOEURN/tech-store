<template>
    <Head :title="banner.title" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Banner Details: {{ banner.title }}
                </h2>
                <v-spacer></v-spacer>
                <Link :href="route('dashboard.banners.edit', banner.uuid)" class="text-decoration-none">
                <v-btn color="warning" prepend-icon="mdi-pencil" class="mx-2">
                    Edit
                </v-btn>
                </Link>
                <Link :href="route('dashboard.banners.index')">
                <v-btn color="secondary" prepend-icon="mdi-arrow-left" variant="outlined">
                    Back to Banners
                </v-btn>
                </Link>
            </div>
        </template>

        <v-container fluid class="py-8">
            <v-row>
                <v-col cols="12" md="6">
                    <v-card class="mb-4">
                        <v-card-title>Banner Image</v-card-title>
                        <v-card-text>
                            <v-img :src="banner.image_url" max-height="400" contain class="bg-grey-lighten-2 rounded"></v-img>
                        </v-card-text>
                    </v-card>
                </v-col>

                <v-col cols="12" md="6">
                    <v-card class="mb-4">
                        <v-card-title>Banner Information</v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <v-list>
                                <v-list-item>
                                    <v-list-item-title>Title</v-list-item-title>
                                    <v-list-item-subtitle>{{ banner.title }}</v-list-item-subtitle>
                                </v-list-item>

                                <v-list-item>
                                    <v-list-item-title>Link URL</v-list-item-title>
                                    <v-list-item-subtitle>
                                        <a :href="banner.link" target="_blank" class="text-decoration-none">
                                            {{ banner.link }}
                                            <v-icon size="small" class="ml-1">mdi-open-in-new</v-icon>
                                        </a>
                                    </v-list-item-subtitle>
                                </v-list-item>

                                <v-list-item>
                                    <v-list-item-title>Position</v-list-item-title>
                                    <v-list-item-subtitle>
                                        <v-chip :color="positionColor(banner.position)" size="small">
                                            {{ formatPosition(banner.position) }}
                                        </v-chip>
                                    </v-list-item-subtitle>
                                </v-list-item>

                                <v-list-item>
                                    <v-list-item-title>Status</v-list-item-title>
                                    <v-list-item-subtitle>
                                        <v-chip :color="banner.status ? 'success' : 'error'" size="small">
                                            {{ banner.status ? 'Active' : 'Inactive' }}
                                        </v-chip>
                                    </v-list-item-subtitle>
                                </v-list-item>

                                <v-list-item>
                                    <v-list-item-title>Created Date</v-list-item-title>
                                    <v-list-item-subtitle>{{ banner.created_at }}</v-list-item-subtitle>
                                </v-list-item>

                                <v-list-item>
                                    <v-list-item-title>Last Updated</v-list-item-title>
                                    <v-list-item-subtitle>{{ banner.updated_at }}</v-list-item-subtitle>
                                </v-list-item>
                            </v-list>

                            <v-divider class="my-4"></v-divider>

                            <!-- Banner Preview Card -->
                            <div>
                                <h3 class="text-subtitle-1 mb-2">Preview:</h3>
                                <v-card variant="outlined">
                                    <v-card-title class="text-subtitle-1">How it will appear on the website:</v-card-title>
                                    <v-card-text>
                                        <div v-if="banner.position === 'slider'" class="preview-container slider-preview">
                                            <v-img :src="banner.image_url" height="200" class="rounded-lg">
                                                <div class="slider-content pa-4">
                                                    <h3 class="text-h6 white--text font-weight-bold">{{ banner.title }}</h3>
                                                    <v-btn color="white" size="small" class="mt-2">Shop Now</v-btn>
                                                </div>
                                            </v-img>
                                        </div>
                                        <div v-else-if="banner.position === 'side'" class="preview-container">
                                            <v-img :src="banner.image_url" height="400" width="300" cover class="rounded-lg"></v-img>
                                        </div>
                                        <div v-else-if="banner.position === 'promo'" class="preview-container">
                                            <v-img :src="banner.image_url" height="120" class="rounded-lg">
                                                <div class="banner-content">
                                                    <h3 class="text-subtitle-2 white--text font-weight-bold">{{ banner.title }}</h3>
                                                    <v-btn color="white" size="x-small" class="mt-1">Shop Now</v-btn>
                                                </div>
                                            </v-img>
                                        </div>
                                    </v-card-text>
                                </v-card>
                            </div>
                        </v-card-text>
                    </v-card>

                    <!-- Delete Section -->
                    <v-card color="error" variant="outlined">
                        <v-card-title class="text-white">Danger Zone</v-card-title>
                        <v-card-text>
                            <p class="text-white">Once you delete this banner, there is no going back.</p>
                            <v-btn color="white" variant="outlined" class="mt-2" @click="confirmDelete">Delete Banner</v-btn>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Delete Confirmation Dialog -->
            <v-dialog v-model="deleteDialog" max-width="500px">
                <v-card>
                    <v-card-title class="text-h5">Delete Banner</v-card-title>
                    <v-card-text>
                        Are you sure you want to delete this banner? This action cannot be undone.
                        <p class="mt-4 font-weight-bold">{{ banner.title }}</p>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue-darken-1" variant="text" @click="closeDeleteDialog">Cancel</v-btn>
                        <v-btn color="error" :loading="deleting" @click="deleteBanner">Delete</v-btn>
                        <v-spacer></v-spacer>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-container>
    </DashboardLayout>
</template>

<script setup>
    import DashboardLayout from '@/Layouts/DashboardLayout.vue';
    import { Head, Link, router } from '@inertiajs/vue3';
    import { ref } from 'vue';

    const props = defineProps({
        banner: Object,
        positions: Array
    });

    const deleteDialog = ref(false);
    const deleting = ref(false);

    function formatPosition(position) {
        switch (position) {
            case 'slider': return 'Main Slider';
            case 'side': return 'Side Banner';
            case 'promo': return 'Promo Banner';
            default: return position;
        }
    }

    function positionColor(position) {
        switch (position) {
            case 'slider': return 'primary';
            case 'side': return 'indigo';
            case 'promo': return 'deep-purple';
            default: return 'grey';
        }
    }

    const confirmDelete = () => {
        deleteDialog.value = true;
    };

    const closeDeleteDialog = () => {
        deleteDialog.value = false;
    };

    const deleteBanner = () => {
        deleting.value = true;
        router.delete(route('dashboard.banners.destroy', props.banner.uuid), {
            onSuccess: () => {
                // Will redirect to banners index page
            },
            onError: () => {
                closeDeleteDialog();
            },
            onFinish: () => {
                deleting.value = false;
            }
        });
    };
</script>

<style scoped>
    .preview-container {
        max-width: 100%;
        overflow: hidden;
    }
    
    .slider-content, .banner-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        background: rgba(0, 0, 0, 0.4);
        padding: 10px;
        border-radius: 4px;
        width: 80%;
    }
</style>