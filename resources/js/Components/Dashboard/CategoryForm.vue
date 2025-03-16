<template>
    <form @submit.prevent="submitForm">
        <v-row>
            <!-- Basic Information Card -->
            <v-col cols="12" md="8">
                <v-card class="mb-4">
                    <v-card-title>
                        <v-icon class="mr-2">mdi-information-outline</v-icon>
                        Basic Information
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-card-text>
                        <!-- Category Name -->
                        <v-text-field v-model="form.name" label="Category Name*" :error-messages="errors?.name" required
                            variant="outlined" density="comfortable"></v-text-field>

                        <!-- Description -->
                        <v-textarea v-model="form.description" label="Category Description"
                            :error="errors?.description" required placeholder="Enter category description here..."
                            :min-height="300" variant="outlined" density="comfortable"></v-textarea>

                        <!-- Parent Category -->
                        <v-select v-model="form.parent_id" :items="parentCategories || []" item-title="name"
                            item-value="id" label="Parent Category" :error-messages="errors?.parent_id" clearable
                            variant="outlined" density="comfortable" hint="Leave empty for top-level category"
                            persistent-hint></v-select>
                    </v-card-text>
                </v-card>

                <!-- Image Upload Card -->
                <v-card>
                    <v-card-title>
                        <v-icon class="mr-2">mdi-image</v-icon>
                        Category Image
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-card-text>
                        <!-- Current Image Preview -->
                        <div v-if="category && category.image_url" class="mb-4">
                            <p class="text-subtitle-1 mb-2">Current Image:</p>
                            <v-img :src="category.image_url" max-width="300" max-height="200" contain
                                class="bg-grey-lighten-2 rounded"></v-img>
                        </div>

                        <!-- Image Upload Field -->
                        <v-file-input v-model="form.image" accept="image/*" label="Upload Category Image*"
                            :error-messages="errors?.image" prepend-icon="" prepend-inner-icon="mdi-camera" show-size
                            :required="!category" @change="previewImage" variant="outlined"
                            density="comfortable"></v-file-input>

                        <!-- Image Preview -->
                        <div v-if="imagePreview" class="mt-4">
                            <p class="text-subtitle-1 mb-2">Preview:</p>
                            <v-img :src="imagePreview" max-width="300" max-height="200" contain
                                class="bg-grey-lighten-2 rounded"></v-img>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>

            <!-- Category Settings Card -->
            <v-col cols="12" md="4">
                <v-card class="sticky-card">
                    <v-card-title>
                        <v-icon class="mr-2">mdi-cog-outline</v-icon>
                        Category Settings
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-card-text>
                        <!-- Status -->
                        <div class="mb-4">
                            <v-switch v-model="form.status" color="success" label="Active"
                                hint="Toggle to publish or unpublish the category"
                                :error-messages="errors?.status"></v-switch>
                        </div>

                        <v-divider class="my-4"></v-divider>

                        <!-- Save Button -->
                        <v-btn color="primary" type="submit" block size="large" :loading="processing">
                            {{ category ? 'Update Category' : 'Create Category' }}
                        </v-btn>

                        <!-- Cancel Button -->
                        <Link :href="route('dashboard.categories.index')"
                            class="v-btn v-btn--block v-btn--text v-btn--secondary mt-3">
                        Cancel
                        </Link>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </form>
</template>

<script setup>
    import { ref, onMounted, computed } from 'vue';
    import { Link } from '@inertiajs/vue3';

    const props = defineProps({
        category: {
            type: Object,
            default: null
        },
        parentCategories: {
            type: Array,
            default: () => []
        },
        errors: {
            type: Object,
            default: () => ({})
        },
        processing: {
            type: Boolean,
            default: false
        },
        modelValue: {
            type: Object,
            default: () => ({})
        }
    });

    const emit = defineEmits(['submit', 'update:modelValue']);

    // Initialize form with default values
    const form = ref({
        name: '',
        slug: '',
        description: '',
        parent_id: null,
        status: true,
        image: null,
        image_url: null,
    });

    console.log('modelValue', props.modelValue);

    const imagePreview = ref(null);

    // Preview uploaded image
    const previewImage = (file) => {
        if (!file) {
            imagePreview.value = null;
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    };

    // Set initial form values on component mount
    onMounted(() => {
        // Initialize from props.modelValue first
        if (props.modelValue && Object.keys(props.modelValue).length) {
            Object.keys(form.value).forEach(key => {
                if (props.modelValue[key] !== undefined) {
                    form.value[key] = props.modelValue[key];
                }
            });
        }

        // Then initialize from category if available (for edit mode)
        if (props.category) {
            form.value.name = props.category.name;
            form.value.description = props.category.description || '';
            form.value.parent_id = props.category.parent_id;
            form.value.status = props.category.status;
        }

        // Emit initial form data
        emitFormChanges();
    });

    // Emit changes to the parent component
    const emitFormChanges = () => {
        emit('update:modelValue', { ...form.value });
    };

    // Validate and submit form
    const submitForm = () => {
        // Basic validation
        if (!form.value.name) {
            alert('Category name is required');
            return;
        }

        if (!form.value.image && !props.category) {
            alert('Image is required for new categories');
            return;
        }

        // Emit the submit event with form data
        emit('submit', { ...form.value });
    };
</script>

<style scoped>
    .sticky-card {
        position: sticky;
        top: 24px;
    }
</style>
