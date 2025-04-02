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
                        <!-- Product Name -->
                        <v-text-field v-model="form.name" label="Product Name*" :error-messages="errors?.name"
                            variant="outlined" density="comfortable" required></v-text-field>

                        <!-- Description -->
                        <RichTextEditor v-model="form.description" label="Product Description"
                            :error="errors?.description" required placeholder="Enter product description here..."
                            :min-height="300" variant="outlined" density="comfortable" />


                        <!-- Category & Brand -->
                        <v-row>
                            <v-col cols="12" md="6">
                                <v-select v-model="form.category_id" :items="categories" item-title="name"
                                    item-value="id" label="Category*" :error-messages="errors?.category_id"
                                    variant="outlined" density="comfortable" required></v-select>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-select v-model="form.brand_id" :items="brands" item-title="name" item-value="id"
                                    label="Brand*" :error-messages="errors?.brand_id" variant="outlined"
                                    density="comfortable" required></v-select>
                            </v-col>
                        </v-row>

                        <!-- SKU (read only for edit) -->
                        <v-text-field v-if="product" v-model="product.sku" label="SKU" disabled variant="outlined"
                            density="comfortable"
                            hint="SKU is generated automatically and cannot be changed"></v-text-field>
                    </v-card-text>
                </v-card>

                <!-- Pricing & Inventory Card -->
                <v-card class="mb-4">
                    <v-card-title>
                        <v-icon class="mr-2">mdi-cash-multiple</v-icon>
                        Pricing & Inventory
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-card-text>
                        <v-row>
                            <v-col cols="12" md="6">
                                <v-text-field v-model.number="form.price" label="Regular Price*" prefix="$"
                                    type="number" step="0.01" min="0" :error-messages="errors?.price" required
                                    variant="outlined" density="comfortable"></v-text-field>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-text-field v-model.number="form.sale_price" label="Sale Price" prefix="$"
                                    type="number" step="0.01" min="0" :error-messages="errors?.sale_price"
                                    hint="Leave empty for no sale price" variant="outlined"
                                    density="comfortable"></v-text-field>
                            </v-col>
                        </v-row>

                        <!-- Stock -->
                        <v-text-field v-model.number="form.stock" label="Stock Quantity*" type="number" min="0"
                            :error-messages="errors?.stock" variant="outlined" density="comfortable"
                            required></v-text-field>
                    </v-card-text>
                </v-card>

                <!-- Product Images Card -->
                <v-card>
                    <v-card-title>
                        <v-icon class="mr-2">mdi-image-multiple</v-icon>
                        Product Images
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-card-text>
                        <!-- Image Upload -->
                        <v-file-input v-model="form.images" label="Upload Images"
                            accept="image/png, image/jpeg, image/jpg" :error-messages="errors?.images" multiple
                            show-size prepend-icon="" prepend-inner-icon="mdi-camera" variant="outlined"
                            density="comfortable" @update:model-value="previewImages">
                            <template v-slot:selection="{ fileNames }">
                                <v-chip v-for="fileName in fileNames" :key="fileName" size="small" label color="primary"
                                    class="mr-1">
                                    {{ fileName }}
                                </v-chip>
                            </template>
                        </v-file-input>

                        <!-- New Images Preview -->
                        <div v-if="imagePreview.length > 0" class="mt-4">
                            <h3 class="text-subtitle-1 mb-2">New Images Preview:</h3>
                            <div class="d-flex flex-wrap gap-3">
                                <div v-for="(preview, index) in imagePreview" :key="index"
                                    class="image-container position-relative">
                                    <v-img :src="preview" width="100" height="100" cover class="rounded"></v-img>
                                </div>
                            </div>
                        </div>

                        <!-- Existing Images (Edit Mode) -->
                        <div v-if="product && filteredProductImages.length > 0" class="mt-4">
                            <h3 class="text-subtitle-1 mb-2">Existing Images</h3>
                            <div class="d-flex flex-wrap gap-3">
                                <div v-for="image in filteredProductImages" :key="image.id"
                                    class="image-container position-relative"
                                    :class="{ 'primary-image': image.id === form.primary_image }">
                                    <v-img :src="image.image_url" width="100" height="100" cover
                                        class="rounded"></v-img>

                                    <div class="image-actions">
                                        <!-- Make Primary Button -->
                                        <v-btn icon size="small" color="primary" variant="text"
                                            @click="setPrimaryImage(image.id)"
                                            :disabled="image.id === form.primary_image" class="mr-1"
                                            title="Set as primary image">
                                            <v-icon>mdi-star</v-icon>
                                        </v-btn>

                                        <!-- Delete Button -->
                                        <v-btn icon size="small" color="error" variant="text"
                                            @click="removeImage(image.id)"
                                            :disabled="filteredProductImages.length === 1" title="Delete image">
                                            <v-icon>mdi-delete</v-icon>
                                        </v-btn>
                                    </div>

                                    <v-chip v-if="image.id === form.primary_image" color="primary" size="x-small"
                                        class="image-badge">
                                        Primary
                                    </v-chip>
                                </div>
                            </div>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>

            <!-- Product Settings Card -->
            <v-col cols="12" md="4">
                <v-card class="sticky-card">
                    <v-card-title>
                        <v-icon class="mr-2">mdi-cog-outline</v-icon>
                        Product Settings
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-card-text>
                        <!-- Status -->
                        <div class="mb-4">
                            <v-switch v-model="form.status" color="success" label="Published"
                                hint="Toggle to publish or unpublish the product"
                                :error-messages="errors?.status"></v-switch>
                        </div>

                        <!-- Featured -->
                        <div class="mb-4">
                            <v-switch v-model="form.featured" color="primary" label="Featured"
                                hint="Featured products appear on the homepage"
                                :error-messages="errors?.featured"></v-switch>
                        </div>

                        <v-divider class="my-4"></v-divider>

                        <!-- Save Button -->
                        <v-btn color="primary" type="submit" block size="large" :loading="processing">
                            {{ product ? 'Update Product' : 'Create Product' }}
                        </v-btn>

                        <!-- Cancel Button -->
                        <Link :href="route('dashboard.products.index')"
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
    import { ref, computed, onMounted } from 'vue';
    import { Link } from '@inertiajs/vue3';

    const props = defineProps({
        product: {
            type: Object,
            default: null
        },
        categories: {
            type: Array,
            required: true
        },
        brands: {
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

    // Initialize form with default values and modelValue/product data
    const form = ref({
        name: '',
        description: '',
        category_id: null,
        brand_id: null,
        price: null,
        sale_price: null,
        stock: 0,
        status: true,
        featured: false,
        images: [],
        remove_images: [],
        primary_image: null
    });

    const imagePreview = ref([]);

    // Preview uploaded images
    const previewImages = (files) => {
        imagePreview.value = [];
        if (!files || files.length === 0) return;

        Array.from(files).forEach(file => {
            const reader = new FileReader();
            reader.onload = (e) => {
                imagePreview.value.push(e.target.result);
            };
            reader.readAsDataURL(file);
        });
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

        // Then initialize from product if available (for edit mode)
        if (props.product) {
            form.value.name = props.product.name;
            form.value.description = props.product.description || '';
            form.value.category_id = props.product.category_id;
            form.value.brand_id = props.product.brand_id;
            form.value.price = props.product.price;
            form.value.sale_price = props.product.sale_price || null;
            form.value.stock = props.product.stock;
            form.value.status = props.product.status;
            form.value.featured = props.product.featured;
            form.value.primary_image = props.product.product_images?.find(img => img.is_primary)?.id || null;

            // Keep the remove_images state if already set
            if (!form.value.remove_images?.length) {
                form.value.remove_images = [];
            }
        }
    });

    // Filter product images to remove those marked for removal
    const filteredProductImages = computed(() => {
        if (!props.product?.product_images) return [];
        return props.product.product_images.filter(
            img => !form.value.remove_images.includes(img.id)
        );
    });

    // Set primary image
    const setPrimaryImage = (imageId) => {
        form.value.primary_image = imageId;
        // Emit form changes
        emitFormChanges();
    };

    // Remove image
    const removeImage = (imageId) => {
        // Add to remove list
        form.value.remove_images.push(imageId);

        // If removing the primary image, select a new primary image
        if (form.value.primary_image === imageId) {
            // Find an image that's not being removed to set as primary
            const availableImages = filteredProductImages.value;

            if (availableImages.length > 0) {
                form.value.primary_image = availableImages[0].id;
            } else {
                form.value.primary_image = null;
            }
        }

        // Emit form changes
        emitFormChanges();
    };

    // Emit changes to the parent component - avoids recursive updates
    const emitFormChanges = () => {
        emit('update:modelValue', { ...form.value });
    };

    // Validate and submit form
    const submitForm = () => {
        // Basic validation
        if (!form.value.name) {
            alert('Product name is required');
            return;
        }

        if (!form.value.category_id) {
            alert('Category is required');
            return;
        }

        if (!form.value.brand_id) {
            alert('Brand is required');
            return;
        }

        if (!form.value.price || form.value.price <= 0) {
            alert('Price must be greater than 0');
            return;
        }

        // Emit the submit event with form data
        emit('submit', { ...form.value });
    };
</script>

<style scoped>
    .sticky-card {
        position: sticky;
        top: 20px;
    }

    .image-container {
        position: relative;
        border-radius: 4px;
        border: 1px solid #e0e0e0;
        overflow: hidden;
    }

    .primary-image {
        border: 2px solid #1976d2;
        box-shadow: 0 0 8px rgba(25, 118, 210, 0.5);
    }

    .image-badge {
        position: absolute;
        bottom: 4px;
        left: 4px;
    }

    .image-actions {
        position: absolute;
        top: 4px;
        right: 4px;
        display: flex;
        background: rgba(0, 0, 0, 0.3);
        border-radius: 4px;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .image-container:hover .image-actions {
        opacity: 1;
    }

    .gap-3 {
        gap: 12px;
    }
</style>
