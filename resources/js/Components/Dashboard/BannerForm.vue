<template>
    <form @submit.prevent="submitForm">
        <v-row>
            <!-- Main Content Column -->
            <v-col cols="12" md="8">
                <v-card>
                    <v-card-title>
                        <v-icon class="mr-2">mdi-image-multiple</v-icon>
                        {{ $t('Banner Information') }}
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-card-text>
                        <!-- Banner Title -->
                        <div class="mb-4">
                            <label class="text-subtitle-1 d-block mb-1">{{ $t('Banner Title') }}*</label>
                            <v-text-field v-model="form.title" :error-messages="errors?.title" hide-details="auto"
                                placeholder="Enter banner title" variant="outlined" density="comfortable"></v-text-field>
                        </div>

                        <!-- Link -->
                        <div class="mb-4">
                            <label class="text-subtitle-1 d-block mb-1">{{ $t('Link URL') }}*</label>
                            <v-text-field v-model="form.link" :error-messages="errors?.link" hide-details="auto"
                                placeholder="Enter link URL (e.g., /products/gaming-laptop)" variant="outlined" density="comfortable"></v-text-field>
                            <p class="text-caption mt-1">The URL that users will be redirected to when they click the banner.</p>
                        </div>

                        <!-- Banner Image -->
                        <div class="mb-4">
                            <label class="text-subtitle-1 d-block mb-1">{{ $t('Banner Image') }} {{ banner ? '' : '*' }}</label>
                            <v-file-input v-model="form.image" :error-messages="errors?.image" hide-details="auto"
                                placeholder="Select banner image" variant="outlined" density="comfortable"
                                accept="image/*" prepend-icon="" prepend-inner-icon="mdi-image"
                                @update:model-value="previewImage"></v-file-input>
                            <p class="text-caption mt-1">
                                Supported formats: JPG, PNG, JPEG. Max size: 2MB.<br>
                                <span v-if="form.position === 'slider'">Recommended size for slider: 1200x400 pixels</span>
                                <span v-else-if="form.position === 'side'">Recommended size for side banners: 600x400 pixels</span>
                                <span v-else-if="form.position === 'promo'">Recommended size for promo banners: 800x200 pixels</span>
                            </p>
                        </div>

                        <!-- Image Preview -->
                        <div v-if="imagePreview || form.image_url" class="mb-4">
                            <p class="text-subtitle-1 mb-2">{{ $t('Preview') }}:</p>
                            <v-img :src="imagePreview || form.image_url" max-width="400" max-height="200" contain
                                class="bg-grey-lighten-2 rounded"></v-img>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>

            <!-- Banner Settings Card -->
            <v-col cols="12" md="4">
                <v-card class="sticky-card">
                    <v-card-title>
                        <v-icon class="mr-2">mdi-cog-outline</v-icon>
                        {{ $t('Banner Settings') }}
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-card-text>
                        <!-- Position -->
                        <div class="mb-4">
                            <label class="text-subtitle-1 d-block mb-1">{{ $t('Position') }}*</label>
                            <v-select v-model="form.position" :items="positions" item-title="label" item-value="value"
                                :error-messages="errors?.position" hide-details="auto" variant="outlined" density="comfortable">
                            </v-select>
                            <p class="text-caption mt-1">
                                Where the banner will be displayed on the website.
                            </p>
                        </div>

                        <!-- Status -->
                        <div class="mb-4">
                            <v-switch v-model="form.status" color="success" :label="$t('Active')"
                                hint="Toggle to publish or unpublish the banner"
                                :error-messages="errors?.status"></v-switch>
                        </div>

                        <v-divider class="my-4"></v-divider>

                        <!-- Save Button -->
                        <v-btn color="primary" type="submit" block size="large" :loading="processing">
                            {{ banner ? $t('Update Banner') : $t('Create Banner') }}
                        </v-btn>

                        <!-- Cancel Button -->
                        <Link :href="route('dashboard.banners.index')"
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
        banner: {
            type: Object,
            default: null
        },
        positions: {
            type: Array,
            required: true
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
        title: '',
        link: '',
        position: 'slider',
        status: true,
        image: null,
        image_url: null,
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

        // Then initialize from banner if available (for edit mode)
        if (props.banner) {
            form.value.title = props.banner.title;
            form.value.link = props.banner.link;
            form.value.position = props.banner.position;
            form.value.status = props.banner.status;
            form.value.image_url = props.banner.image_url;
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
        if (!form.value.title) {
            alert('Banner title is required');
            return;
        }

        if (!form.value.link) {
            alert('Link URL is required');
            return;
        }

        if (!form.value.position) {
            alert('Banner position is required');
            return;
        }

        if (!form.value.image && !props.banner) {
            alert('Banner image is required for new banners');
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
