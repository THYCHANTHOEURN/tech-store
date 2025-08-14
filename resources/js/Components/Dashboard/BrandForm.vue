<template>
    <form @submit.prevent="submitForm">
        <v-row>
            <!-- Main Content Column -->
            <v-col cols="12" md="8">
                <v-card>
                    <v-card-title>
                        <v-icon class="mr-2">mdi-tag-outline</v-icon>
                        {{ $t('Brand Information') }}
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-card-text>
                        <!-- Brand Name -->
                        <div class="mb-4">
                            <label class="text-subtitle-1 d-block mb-1">{{ $t('Brand Name') }}*</label>
                            <v-text-field v-model="form.name" :error-messages="errors?.name" hide-details="auto"
                                placeholder="Enter brand name" variant="outlined" density="comfortable"></v-text-field>
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label class="text-subtitle-1 d-block mb-1">{{ $t('Description') }}</label>
                            <v-textarea v-model="form.description" :error-messages="errors?.description"
                                hide-details="auto" placeholder="Enter brand description" variant="outlined"
                                density="comfortable" rows="6"></v-textarea>
                        </div>

                        <!-- Logo -->
                        <div class="mb-4">
                            <label class="text-subtitle-1 d-block mb-1">{{ $t('Logo Image') }}{{ brand ? '' : '*' }}</label>
                            <v-file-input v-model="form.logo" :error-messages="errors?.logo" hide-details="auto"
                                placeholder="Select logo image" variant="outlined" density="comfortable"
                                accept="image/*" prepend-icon="" prepend-inner-icon="mdi-image"
                                @update:model-value="previewImage"></v-file-input>
                            <p class="text-caption mt-1">Supported formats: JPG, PNG, JPEG. Max size: 2MB.</p>
                        </div>

                        <!-- Logo Preview -->
                        <div v-if="imagePreview || form.logo_url" class="mb-4">
                            <p class="text-subtitle-1 mb-2">{{ $t('Preview') }}:</p>
                            <v-img :src="imagePreview || form.logo_url" max-width="300" max-height="200" contain
                                class="bg-grey-lighten-2 rounded"></v-img>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>

            <!-- Brand Settings Card -->
            <v-col cols="12" md="4">
                <v-card class="sticky-card">
                    <v-card-title>
                        <v-icon class="mr-2">mdi-cog-outline</v-icon>
                        {{ $t('Brand Settings') }}
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-card-text>
                        <!-- Status -->
                        <div class="mb-4">
                            <v-switch v-model="form.status" color="success" :label="$t('Active')"
                                :hint="$t('Toggle to publish or unpublish the brand')"
                                :error-messages="errors?.status"></v-switch>
                        </div>

                        <v-divider class="my-4"></v-divider>

                        <!-- Save Button -->
                        <v-btn color="primary" type="submit" block size="large" :loading="processing">
                            {{ brand ? $t('Update Brand') : $t('Create Brand') }}
                        </v-btn>

                        <!-- Cancel Button -->
                        <Link :href="route('dashboard.brands.index')"
                            class="v-btn v-btn--block v-btn--text v-btn--secondary mt-3">
                            {{ $t('Cancel') }}
                        </Link>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </form>
</template>

<script setup>
    import { ref, onMounted } from 'vue';
    import { Link } from '@inertiajs/vue3';

    const props = defineProps({
        brand: {
            type: Object,
            default: null
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
        description: '',
        status: true,
        logo: null,
        logo_url: null,
    });

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

        // Then initialize from brand if available (for edit mode)
        if (props.brand) {
            form.value.name = props.brand.name;
            form.value.description = props.brand.description || '';
            form.value.status = props.brand.status;
            form.value.logo_url = props.brand.logo_url;
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
            alert('Brand name is required');
            return;
        }

        if (!form.value.logo && !props.brand) {
            alert('Logo is required for new brands');
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
