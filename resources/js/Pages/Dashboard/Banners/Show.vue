<template>

    <Head :title="banner.title" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ $t('Banner Details') }}: {{ banner.title }}
                </h2>
                <v-spacer></v-spacer>
                <Link :href="route('dashboard.banners.edit', banner.uuid)" class="text-decoration-none">
                <v-btn color="warning" prepend-icon="mdi-pencil" class="mx-2">
                    {{ $t('Edit') }}
                </v-btn>
                </Link>
                <Link :href="route('dashboard.banners.index')">
                <v-btn color="secondary" prepend-icon="mdi-arrow-left" variant="outlined">
                    {{ $t('Back to Banners') }}
                </v-btn>
                </Link>
            </div>
        </template>

        <v-container fluid class="py-8">
            <v-row>
                <v-col cols="12" md="6">
                    <v-card class="mb-4">
                        <v-card-title>{{ $t('Banner Image') }}</v-card-title>
                        <v-card-text>
                            <v-img :src="banner.image_url" max-height="400" contain
                                class="bg-grey-lighten-2 rounded"></v-img>
                        </v-card-text>
                    </v-card>
                </v-col>

                <v-col cols="12" md="6">
                    <v-card class="mb-4">
                        <v-card-title>{{ $t('Banner Information') }}</v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <v-list>
                                <v-list-item>
                                    <v-list-item-title>{{ $t('Title') }}</v-list-item-title>
                                    <v-list-item-subtitle>{{ banner.title }}</v-list-item-subtitle>
                                </v-list-item>

                                <v-list-item>
                                    <v-list-item-title>{{ $t('Link URL') }}</v-list-item-title>
                                    <v-list-item-subtitle>
                                        <a :href="banner.link" target="_blank" class="text-decoration-none">
                                            {{ banner.link }}
                                            <v-icon size="small" class="ml-1">mdi-open-in-new</v-icon>
                                        </a>
                                    </v-list-item-subtitle>
                                </v-list-item>

                                <v-list-item>
                                    <v-list-item-title>{{ $t('Position') }}</v-list-item-title>
                                    <v-list-item-subtitle>
                                        <v-chip :color="positionColor(banner.position)" size="small">
                                            {{ formatPosition(banner.position) }}
                                        </v-chip>
                                    </v-list-item-subtitle>
                                </v-list-item>

                                <v-list-item>
                                    <v-list-item-title>{{ $t('Status') }}</v-list-item-title>
                                    <v-list-item-subtitle>
                                        <v-chip :color="banner.status ? 'success' : 'error'" size="small">
                                            {{ banner.status ? 'Active' : 'Inactive' }}
                                        </v-chip>
                                    </v-list-item-subtitle>
                                </v-list-item>

                                <v-list-item>
                                    <v-list-item-title>{{ $t('Created Date') }}</v-list-item-title>
                                    <v-list-item-subtitle>{{ banner.created_at }}</v-list-item-subtitle>
                                </v-list-item>

                                <v-list-item>
                                    <v-list-item-title>{{ $t('Last Updated') }}</v-list-item-title>
                                    <v-list-item-subtitle>{{ banner.updated_at }}</v-list-item-subtitle>
                                </v-list-item>
                            </v-list>

                            <v-divider class="my-4"></v-divider>

                            <!-- Banner Preview Card -->
                            <div>
                                <h3 class="text-subtitle-1 mb-2">{{ $t('Preview') }}:</h3>
                                <v-card variant="outlined">
                                    <v-card-title class="text-subtitle-1">How it will appear on the
                                        website:</v-card-title>
                                    <v-card-text>
                                        <div v-if="banner.position === 'slider'"
                                            class="preview-container slider-preview">
                                            <v-img :src="banner.image_url" height="200" class="rounded-lg">
                                                <div class="slider-content pa-4">
                                                    <h3 class="text-h6 white--text font-weight-bold">{{ banner.title }}
                                                    </h3>
                                                    <v-btn color="white" size="small" class="mt-2">Shop Now</v-btn>
                                                </div>
                                            </v-img>
                                        </div>
                                        <div v-else-if="banner.position === 'side'" class="preview-container">
                                            <v-img :src="banner.image_url" height="400" width="300" cover
                                                class="rounded-lg"></v-img>
                                        </div>
                                        <div v-else-if="banner.position === 'promo'"
                                            class="preview-container promo-preview">
                                            <v-hover v-slot="{ isHovering, props }">
                                                <v-card v-bind="props" :elevation="isHovering ? 4 : 1"
                                                    class="banner-card">
                                                    <v-img :src="banner.image_url" height="180" cover>
                                                        <div class="banner-content">
                                                            <h3 class="text-h6 white--text font-weight-bold">{{
                                                                banner.title }}
                                                            </h3>
                                                            <v-btn color="white" class="mt-4" :href="banner.link">
                                                                Shop Now
                                                            </v-btn>
                                                        </div>
                                                    </v-img>
                                                </v-card>
                                            </v-hover>
                                        </div>
                                    </v-card-text>
                                </v-card>
                            </div>
                        </v-card-text>
                    </v-card>

                    <!-- Delete Section -->
                    <v-card color="error" variant="outlined">
                        <v-card-title class="text-warning">{{ $t('Danger Zone') }}</v-card-title>
                        <v-card-text>
                            <p class="text-warning">{{ $t('Once you delete this banner, there is no going back.') }}</p>
                            <v-btn color="white" variant="outlined" class="mt-2" @click="confirmDelete">{{ $t('Delete Banner') }}</v-btn>
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
        border-radius: 4px;
        color: white;
    }

    .slider-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        /* background: rgba(0, 0, 0, 0.4); */
        padding: 10px;
        border-radius: 4px;
        width: 80%;
        color: white;
    }

    .banner-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        background: rgba(0, 0, 0, 0.4);
        padding: 10px;
        border-radius: 4px;
        width: 80%;
        color: white;
    }

    /* New promo banner styles */
    .promo-preview {
        margin: 10px auto;
        max-width: 800px;
    }

    .promo-wrapper {
        position: relative;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .banner-card {
        transition: transform 0.2s;
    }

    .promo-content {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
    }

    .promo-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, rgba(0, 0, 0, 0.7) 0%, rgba(0, 0, 0, 0.3) 50%, rgba(0, 0, 0, 0) 100%);
    }

    .promo-details {
        position: relative;
        z-index: 2;
        padding: 20px;
        max-width: 60%;
    }

    .promo-badge {
        display: inline-block;
        background-color: #FF5252;
        color: white;
        font-size: 12px;
        font-weight: bold;
        padding: 4px 8px;
        border-radius: 4px;
        margin-bottom: 10px;
    }
</style>
