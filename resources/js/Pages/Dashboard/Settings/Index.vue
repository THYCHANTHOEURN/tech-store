<template>

    <Head :title="t('Settings')" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ t('Settings') }}
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
                                <p class="text-body-1 text-grey">{{ t('No settings found for this group.') }}</p>
                            </div>
                        </template>

                        <div v-else>
                            <!-- Image Settings -->
                            <div v-if="hasImageSettings" class="mb-6">
                                <h3 class="text-h6 mb-4">{{ t('Images') }}</h3>
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
                                                <v-file-input :label="`${t('Change')} ${setting.label}`"
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
                                <h3 class="text-h6 mb-4">{{ t('Text Settings') }}</h3>
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

                            <!-- Theme Settings -->
                            <div v-if="activeTab === 'appearance'" class="mb-6">
                                <h3 class="text-h6 mb-4">{{ t('Theme Options') }}</h3>
                                <v-row>
                                    <!-- Theme Mode Selection -->
                                    <v-col cols="12" md="6">
                                        <v-card outlined class="mb-4">
                                            <v-card-title class="text-subtitle-1">{{ t('Theme Mode') }}</v-card-title>
                                            <v-card-text>
                                                <v-select :items="['light', 'dark', 'system']"
                                                    v-model="getSettingByKey('default_theme_mode').value"
                                                    :label="t('Default Theme Mode')" variant="outlined" density="comfortable">
                                                    <template v-slot:prepend-inner>
                                                        <v-icon>{{ getSettingByKey('default_theme_mode').value ===
                                                            'dark' ? 'mdi-weather-night' : 'mdi-weather-sunny'
                                                            }}</v-icon>
                                                    </template>
                                                </v-select>
                                                <p class="text-caption text-grey mt-2">
                                                    {{ t('System will use your device theme preference if selected') || 'System will use your device theme preference if selected' }}
                                                </p>

                                                <v-switch :label="t('Allow users to override theme')" color="primary"
                                                    class="mt-3" v-model="allowUserThemeOverride"></v-switch>
                                            </v-card-text>
                                        </v-card>
                                    </v-col>

                                    <!-- UI Density -->
                                    <v-col cols="12" md="6">
                                        <v-card outlined class="mb-4">
                                            <v-card-title class="text-subtitle-1">{{ t('Interface Density') }}</v-card-title>
                                            <v-card-text>
                                                <v-select :items="densityItems" v-model="getSettingByKey('density').value"
                                                    :label="t('UI Density')" item-title="title" item-value="value"
                                                    variant="outlined" density="comfortable">
                                                    <template v-slot:prepend-inner>
                                                        <v-icon>mdi-format-line-spacing</v-icon>
                                                    </template>
                                                </v-select>
                                                <p class="text-caption text-grey mt-2">
                                                    {{ t('Controls the spacing of UI elements throughout the dashboard') }}
                                                </p>
                                            </v-card-text>
                                        </v-card>
                                    </v-col>

                                    <!-- Color Picker for Primary Color -->
                                    <v-col cols="12" md="4">
                                        <v-card outlined class="mb-4">
                                            <v-card-title class="text-subtitle-1">{{ t('Primary Color') }}</v-card-title>
                                            <v-card-text>
                                                <div class="d-flex align-center">
                                                    <v-color-picker v-model="getSettingByKey('primary_color').value"
                                                        hide-inputs hide-canvas show-swatches :swatches="swatchesColors"
                                                        class="ma-2"></v-color-picker>
                                                </div>
                                                <p class="text-caption text-grey mt-2">
                                                    {{ t('The main color used throughout the dashboard') }}
                                                </p>
                                            </v-card-text>
                                        </v-card>
                                    </v-col>

                                    <!-- Color Picker for Secondary Color -->
                                    <v-col cols="12" md="4">
                                        <v-card outlined class="mb-4">
                                            <v-card-title class="text-subtitle-1">{{ t('Secondary Color') }}</v-card-title>
                                            <v-card-text>
                                                <div class="d-flex align-center">
                                                    <v-color-picker v-model="getSettingByKey('secondary_color').value"
                                                        hide-inputs hide-canvas show-swatches :swatches="swatchesColors"
                                                        class="ma-2"></v-color-picker>
                                                </div>
                                                <p class="text-caption text-grey mt-2">
                                                    {{ t('The secondary color used throughout the dashboard') }}
                                                </p>
                                            </v-card-text>
                                        </v-card>
                                    </v-col>

                                    <!-- Color Picker for Accent Color -->
                                    <v-col cols="12" md="4">
                                        <v-card outlined class="mb-4">
                                            <v-card-title class="text-subtitle-1">{{ t('Accent Color') }}</v-card-title>
                                            <v-card-text>
                                                <div class="d-flex align-center">
                                                    <v-color-picker v-model="getSettingByKey('accent_color').value"
                                                        hide-inputs hide-canvas show-swatches :swatches="swatchesColors"
                                                        class="ma-2"></v-color-picker>
                                                </div>
                                                <p class="text-caption text-grey mt-2">
                                                    {{ t('The accent color used for highlights and emphasis') }}
                                                </p>
                                            </v-card-text>
                                        </v-card>
                                    </v-col>

                                    <!-- Theme Preview -->
                                    <v-col cols="12">
                                        <v-card outlined class="mb-4">
                                            <v-card-title class="text-subtitle-1">{{ t('Preview') }}</v-card-title>
                                            <v-card-text>
                                                <v-sheet
                                                     :color="getSettingByKey('default_theme_mode').value === 'dark' ? '#121212' : '#FFFFFF'"
                                                     :dark="getSettingByKey('default_theme_mode').value === 'dark'"
                                                     class="pa-6 rounded" border>
                                                     <div class="d-flex align-center justify-space-between mb-6">
                                                        <h3 class="text-h6">{{ t('Theme Preview') }}</h3>
                                                        <div>
                                                            <v-btn :color="getSettingByKey('primary_color').value" class="mr-2" size="small">
                                                                {{ t('Primary Color') }}
                                                            </v-btn>
                                                            <v-btn :color="getSettingByKey('secondary_color').value" size="small" variant="outlined">
                                                                {{ t('Secondary Color') }}
                                                            </v-btn>
                                                        </div>
                                                     </div>

                                                    <v-row>
                                                        <v-col cols="12" md="6">
                                                            <v-text-field :label="t('Sample Text Field')" variant="outlined"
                                                                :density="getSettingByKey('density').value"></v-text-field>
                                                            <v-switch :label="t('Sample Switch')"
                                                                :color="getSettingByKey('primary_color').value"></v-switch>
                                                        </v-col>
                                                        <v-col cols="12" md="6">
                                                            <v-card>
                                                                <v-card-text>
                                                                    <p>{{ t('This is how your cards will look.') }}</p>
                                                                    <v-chip :color="getSettingByKey('accent_color').value" class="mt-2">
                                                                        {{ t('Accent Color') }}
                                                                    </v-chip>
                                                                </v-card-text>
                                                            </v-card>
                                                        </v-col>
                                                    </v-row>
                                                </v-sheet>
                                            </v-card-text>
                                        </v-card>
                                    </v-col>
                                </v-row>
                            </div>
                        </div>

                        <v-divider class="my-4"></v-divider>

                        <div class="d-flex justify-end">
                            <v-btn color="primary" type="submit" :loading="processing">
                                {{ t('Save Settings') }}
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
    import { useI18n } from 'vue-i18n';
    import { Head, router } from '@inertiajs/vue3';
    import DashboardLayout from '@/Layouts/DashboardLayout.vue';

    const { t } = useI18n();

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
    const allowUserThemeOverride = ref(true);
    const swatchesColors = [
        ['#1976D2', '#2196F3', '#03A9F4', '#00BCD4', '#009688', '#4CAF50'],
        ['#8BC34A', '#CDDC39', '#FFEB3B', '#FFC107', '#FF9800', '#FF5722'],
        ['#F44336', '#E91E63', '#9C27B0', '#673AB7', '#3F51B5', '#607D8B'],
        ['#000000', '#424242', '#616161', '#757575', '#9E9E9E', '#FFFFFF'],
    ];

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

    // Helper method to get a setting by key
    const getSettingByKey = (key) => {
        const setting = formData.value.find(s => s.key === key);
        if (setting) return setting;
        return { value: '', type: 'text' };
    };

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
            'legal': 'mdi-gavel',
            'appearance': 'mdi-palette', // Add icon for appearance group
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

    // localized density option titles
    const densityItems = [
        { title: t('Comfortable'), value: 'comfortable' },
        { title: t('Compact'), value: 'compact' },
        { title: t('Default'), value: 'default' },
    ];
</script>
