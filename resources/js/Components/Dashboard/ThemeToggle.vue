<template>
    <div>
        <v-tooltip :text="currentTheme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode'">
            <template v-slot:activator="{ props }">
                <v-btn v-bind="props" icon variant="text" @click="toggleTheme" :size="size">
                    <v-icon>{{ currentTheme === 'dark' ? 'mdi-weather-sunny' : 'mdi-weather-night' }}</v-icon>
                </v-btn>
            </template>
        </v-tooltip>

        <v-dialog v-model="showThemeOptions" width="500px" v-if="showFullOptions">
            <v-card>
                <v-card-title class="d-flex align-center">
                    <v-icon class="mr-2">mdi-palette</v-icon>
                    Theme Options
                    <v-spacer></v-spacer>
                    <v-btn icon @click="showThemeOptions = false">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>

                <v-card-text>
                    <h3 class="text-subtitle-1 font-weight-bold mb-2">Theme Mode</h3>
                    <v-btn-toggle v-model="themeMode" mandatory color="primary" class="mb-4">
                        <v-btn value="light">
                            <v-icon start>mdi-weather-sunny</v-icon>
                            Light
                        </v-btn>
                        <v-btn value="dark">
                            <v-icon start>mdi-weather-night</v-icon>
                            Dark
                        </v-btn>
                        <v-btn value="system">
                            <v-icon start>mdi-monitor</v-icon>
                            System
                        </v-btn>
                    </v-btn-toggle>

                    <h3 class="text-subtitle-1 font-weight-bold mb-2">Primary Color</h3>
                    <div class="d-flex flex-wrap mb-4">
                        <v-btn v-for="(color, index) in predefinedColors" :key="index"
                            :style="{ backgroundColor: color }" icon class="ma-1 color-option" size="large"
                            @click="selectColor(color)" :disabled="primaryColor === color">
                            <v-icon v-if="primaryColor === color" color="white">mdi-check</v-icon>
                        </v-btn>
                    </div>

                    <h3 class="text-subtitle-1 font-weight-bold mb-2">Interface Density</h3>
                    <v-select v-model="selectedDensity" :items="densityOptions" item-title="title" item-value="value"
                        label="Density" variant="outlined" density="comfortable"></v-select>
                </v-card-text>

                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="primary" @click="applySettings">
                        Apply Settings
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script setup>
    import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
    import { useDisplay } from 'vuetify';

    const props = defineProps({
        size: {
            type: String,
            default: 'default'
        },
        showFullOptions: {
            type: Boolean,
            default: false
        }
    });

    const currentTheme = ref(localStorage.getItem('theme') || 'light');
    const themeMode = ref(localStorage.getItem('theme_mode') || 'system');
    const primaryColor = ref(localStorage.getItem('primary_color') || '#1976D2');
    const selectedDensity = ref(localStorage.getItem('density') || 'comfortable');
    const showThemeOptions = ref(false);

    const densityOptions = [
        { title: 'Comfortable', value: 'comfortable' },
        { title: 'Compact', value: 'compact' },
        { title: 'Default', value: 'default' }
    ];

    const predefinedColors = [
        '#1976D2', // Blue
        '#2196F3', // Light Blue
        '#673AB7', // Deep Purple
        '#9C27B0', // Purple
        '#E91E63', // Pink
        '#F44336', // Red
        '#FF9800', // Orange
        '#4CAF50', // Green
        '#009688', // Teal
        '#607D8B', // Blue Grey
    ];

    // Apply theme based on system preference if set to 'system'
    const applySystemPreference = () => {
        if (themeMode.value === 'system') {
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                currentTheme.value = 'dark';
            } else {
                currentTheme.value = 'light';
            }
        } else {
            currentTheme.value = themeMode.value;
        }

        localStorage.setItem('theme', currentTheme.value);
        document.documentElement.setAttribute('data-theme', currentTheme.value);
    };

    const toggleTheme = () => {
        if (props.showFullOptions) {
            showThemeOptions.value = true;
        } else {
            // Simple toggle between light and dark
            currentTheme.value = currentTheme.value === 'dark' ? 'light' : 'dark';
            themeMode.value = currentTheme.value;
            localStorage.setItem('theme', currentTheme.value);
            localStorage.setItem('theme_mode', themeMode.value);
            document.documentElement.setAttribute('data-theme', currentTheme.value);

            // Tell parent to update theme
            emitThemeChange();
        }
    };

    const selectColor = (color) => {
        primaryColor.value = color;
    };

    const emitThemeChange = () => {
        const event = new CustomEvent('theme-changed', {
            detail: {
                theme: currentTheme.value,
                primaryColor: primaryColor.value,
                density: selectedDensity.value
            }
        });
        window.dispatchEvent(event);
    };

    const applySettings = () => {
        // Save all settings to localStorage
        localStorage.setItem('theme_mode', themeMode.value);
        localStorage.setItem('primary_color', primaryColor.value);
        localStorage.setItem('density', selectedDensity.value);

        // Apply system preference based on theme mode
        applySystemPreference();

        showThemeOptions.value = false;

        // Emit theme change event
        emitThemeChange();
    };

    // Watch for changes to theme mode
    watch(themeMode, () => {
        applySystemPreference();
    });

    // Initialize on mount
    onMounted(() => {
        applySystemPreference();

        // Listen for system theme changes
        const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        const handleChange = () => {
            if (themeMode.value === 'system') {
                applySystemPreference();
            }
        };

        mediaQuery.addEventListener('change', handleChange);

        // Clean up
        onUnmounted(() => {
            mediaQuery.removeEventListener('change', handleChange);
        });
    });
</script>

<style scoped>
    .color-option {
        border: 1px solid rgba(0, 0, 0, 0.12);
    }

    .color-option:hover {
        transform: scale(1.1);
        transition: transform 0.2s;
    }
</style>
