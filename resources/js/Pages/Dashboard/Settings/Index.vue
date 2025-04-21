<template>

    <Head title="Settings" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Settings
                </h2>
                <v-spacer></v-spacer>
            </div>
        </template>

        <v-container fluid class="py-8">
            <!-- Settings Tab Navigation -->
            <v-card>
                <v-tabs v-model="activeTab" bg-color="primary">
                    <v-tab v-for="group in formattedGroups" :key="group.value" :value="group.value">
                        <v-icon start>{{ getGroupIcon(group.value) }}</v-icon>
                        {{ group.label }}
                    </v-tab>
                </v-tabs>
            </v-card>

            <v-card class="mt-4">
                <v-card-text>
                    <v-form @submit.prevent="updateSettings" ref="settingsForm">
                        <template v-if="settings.length === 0">
                            <div class="text-center py-8">
                                <v-icon size="large" color="grey" class="mb-4">mdi-alert-circle-outline</v-icon>
                                <p class="text-body-1 text-grey">No settings found for this group.</p>
                            </div>
                        </template>

                        <div v-else>
                            <!-- Image Settings -->
                            <div v-if="hasImageSettings" class="mb-6">
                                <h3 class="text-h6 mb-4">Images</h3>
                                <v-row>
                                    <v-col v-for="setting in imageSettings" :key="setting.id" cols="12" md="4">
                                        <v-card outlined class="mb-4">
                                            <v-card-title class="text-subtitle-1">{{ setting.label }}</v-card-title>
                                            <v-card-text>
                                                <!-- Show either the preview or the existing image -->
                                                <div v-if="getImageUrl(setting.key)" class="mb-3">
                                                    <v-img :src="getImageUrl(setting.key)" max-height="200" contain
                                                        class="bg-grey-lighten-3 rounded"></v-img>
                                                </div>
                                                <v-file-input :label="`Change ${setting.label}`"
                                                    v-model="files[setting.key]" accept="image/*" show-size
                                                    prepend-icon="" prepend-inner-icon="mdi-camera" variant="outlined"
                                                    @update:model-value="(file) => previewImage(file, setting.key)"></v-file-input>
                                                <p class="text-caption text-grey">{{ setting.description }}</p>
                                            </v-card-text>
                                        </v-card>
                                    </v-col>
                                </v-row>
                            </div>

                            <!-- Text Settings -->
                            <div v-if="hasTextSettings" class="mb-6">
                                <h3 class="text-h6 mb-4">Text Settings</h3>
                                <v-row>
                                    <v-col v-for="setting in textSettings" :key="setting.id" cols="12" md="6">
                                        <v-card outlined class="mb-4">
                                            <v-card-title class="text-subtitle-1">{{ setting.label }}</v-card-title>
                                            <v-card-text>
                                                <v-text-field v-model="setting.value" :label="setting.label"
                                                    variant="outlined" density="comfortable"></v-text-field>
                                                <p class="text-caption text-grey">{{ setting.description }}</p>
                                            </v-card-text>
                                        </v-card>
                                    </v-col>
                                </v-row>
                            </div>
                        </div>

                        <v-divider class="my-4"></v-divider>

                        <div class="d-flex justify-end">
                            <v-btn color="primary" type="submit" :loading="processing">
                                Save Settings
                            </v-btn>
                        </div>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-container>
    </DashboardLayout>
</template>

<script setup>
    import { ref, computed, watch } from 'vue';
    import { Head, router } from '@inertiajs/vue3';
    import DashboardLayout from '@/Layouts/DashboardLayout.vue';

    const props = defineProps({
        settings: Array,
        groups: Array,
        currentGroup: String,
        errors: Object,
        flash: Object
    });

    const settingsForm = ref(null);
    const activeTab = ref(props.currentGroup);
    const formData = ref([...props.settings]);
    const files = ref({});
    const imagePreviews = ref({});
    const processing = ref(false);
    // Format group names for display
    const formattedGroups = computed(() => {
        return props.groups.map(group => ({
            value: group,
            label: formatGroupName(group)
        }));
    });

    const imageSettings = computed(() => {
        return formData.value.filter(setting => setting.type === 'image');
    });

    const hasImageSettings = computed(() => {
        return imageSettings.value.length > 0;
    });

    // Text settings computed properties
    const textSettings = computed(() => {
        return formData.value.filter(setting => setting.type === 'text');
    });

    const hasTextSettings = computed(() => {
        return textSettings.value.length > 0;
    });
    // Preview uploaded image
    const previewImage = (file, key) => {
        if (!file) {
            imagePreviews.value[key] = null;
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreviews.value[key] = e.target.result;
        };
        reader.readAsDataURL(file);
    };

    // Helper method to safely get a setting
    function getSetting(key) {
        const setting = formData.value?.find(s => s.key === key);
        return setting || { value: '', key: key };
    }

    // Get image URL for settings
    function getImageUrl(key) {
        const setting = formData.value?.find(s => s.key === key);
        if (setting) {
            // For images, return the preview if available, otherwise the stored value
            if (imagePreviews.value[key]) {
                return imagePreviews.value[key];
            }

            // Check if we have a value
            if (setting.value) {
                // Handle both formats: with and without storage/ prefix
                if (setting.value.startsWith('settings/') ||
                    setting.value.startsWith('banners/') ||
                    setting.value.startsWith('brands/')) {
                    return `/storage/${setting.value}`;
                } else if (setting.value.startsWith('/storage/')) {
                    return setting.value;
                } else {
                    return `/storage/${setting.value}`; // Assume it's a storage path
                }
            }
        }

        // Return a placeholder image if nothing is available
        return '/images/placeholder.png';
    }

    // Add a method to handle form submission
    function submitForm() {
        updateSettings();
    }

    // Handle tab change
    watch(activeTab, (newValue) => {
        if (newValue !== props.currentGroup) {
            router.get(route('dashboard.settings.index', { group: newValue }), {}, {
                preserveScroll: true,
                replace: true
            });
        }
    });

    // Format group name for display
    function formatGroupName(name) {
        return name.split('_')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ');
    }

    // Get icon for group
    function getGroupIcon(group) {
        const icons = {
            'general': 'mdi-cog',
            'about_page': 'mdi-information',
            'contact': 'mdi-phone',
            'social': 'mdi-share-variant',
            'seo': 'mdi-google',
            'legal': 'mdi-gavel'
        };

        return icons[group] || 'mdi-cog';
    }

    // Submit the form
    function updateSettings() {
        processing.value = true;

        // Create FormData for file uploads
        const form = new FormData();

        // Add settings data - make sure to use the current state of formData
        form.append('settings', JSON.stringify(formData.value));

        // Add files with proper naming
        Object.entries(files.value).forEach(([key, file]) => {
            if (file) {
                // Make sure we have the right property name for files
                form.append(`files[${key}]`, file);
            }
        });

        // Output form data for debugging (fixed)
        console.log('Submitting settings with files:', files.value);

        router.post(route('dashboard.settings.update'), form, {
            onFinish: () => {
                processing.value = false;
                // Clear file inputs after successful submission
                files.value = {};
                // Clear image previews
                imagePreviews.value = {};
            },
            preserveScroll: true,
            forceFormData: true
        });
    }
</script>
