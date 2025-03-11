<template>
    <!-- Success Message -->
    <v-snackbar app v-model="showSuccess" location="bottom right" position="sticky"
                :multi-line="false" color="success" dark>
        {{ successMessage }}

        <template v-slot:actions>
            <v-btn color="white" variant="text" @click="showSuccess = false">
                {{ __("Close") }}
            </v-btn>
        </template>
    </v-snackbar>

    <!-- Error Message -->
    <v-snackbar app v-model="showError" location="bottom right" position="sticky"
                :multi-line="false" color="red" style="color: white" dark>
        {{ errorMessage }}

        <template v-slot:actions>
            <v-btn color="white" variant="text" @click="showError = false">
                {{ __("Close") }}
            </v-btn>
        </template>
    </v-snackbar>

    <!-- Validation Errors -->
    <v-alert border="start" type="error" v-if="showFormErrors && Object.keys(errors).length > 0"
             dismissible v-model="showFormErrors" prominent class="ma-3" density="compact">
        <div class="py-2 text-white text-sm font-medium">
            <span>
                {{ __('There are :count form errors', {
                    count: Object.keys(errors).length,
                }) }}
            </span>

            <ul>
                <li v-for="(error, index) in errors" :key="index">
                    <small>{{ error }}</small>
                </li>
            </ul>
        </div>
    </v-alert>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

// Success message handling
const showSuccess = ref(false);
const successMessage = computed(() => usePage().props.flash?.success || '');

// Error message handling
const showError = ref(false);
const errorMessage = computed(() => usePage().props.flash?.error || '');

// Form errors handling
const errors = computed(() => usePage().props.errors || {});
const showFormErrors = ref(false);

// Watch for changes in flash messages and errors
watch(() => usePage().props.flash?.success, (newValue) => {
    if (newValue) {
        showSuccess.value = true;

        // Auto-hide after 5 seconds
        setTimeout(() => {
            showSuccess.value = false;
        }, 5000);
    }
}, { immediate: true });

watch(() => usePage().props.flash?.error, (newValue) => {
    if (newValue) {
        showError.value = true;

        // Auto-hide after 8 seconds (longer for errors)
        setTimeout(() => {
            showError.value = false;
        }, 8000);
    }
}, { immediate: true });

watch(() => Object.keys(errors.value).length, (count) => {
    if (count > 0) {
        showFormErrors.value = true;
    }
}, { immediate: true });

// Translation function
const __ = (key, params = {}) => {
    // Simple translation function - in a real app, you'd use a proper i18n system
    if (key === 'There are :count form errors') {
        return `There are ${params.count} form errors`;
    }
    return key;
};

// Expose methods to manually show/hide messages
defineExpose({
    showSuccess: (message) => {
        successMessage.value = message;
        showSuccess.value = true;
    },
    showError: (message) => {
        errorMessage.value = message;
        showError.value = true;
    },
    closeAll: () => {
        showSuccess.value = false;
        showError.value = false;
        showFormErrors.value = false;
    }
});
</script>
