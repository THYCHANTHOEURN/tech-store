<template>

    <Head title="Products Management" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Products Management
                </h2>
                <v-spacer></v-spacer>
                <Link :href="route('dashboard.products.create')" class="text-decoration-none">
                <v-btn color="primary" prepend-icon="mdi-plus">
                    Add Product
                </v-btn>
                </Link>
            </div>
        </template>

        <v-container fluid class="py-8">
            <!-- Filters -->
            <v-card class="mb-6">
                <v-card-title>
                    <v-icon class="mr-2">mdi-filter-variant</v-icon>
                    Filters
                </v-card-title>
                <v-card-text>
                    <v-row>
                        <v-col cols="12" md="3">
                            <v-text-field v-model="search" label="Search" prepend-inner-icon="mdi-magnify" single-line
                                hide-details clearable @keyup.enter="applyFilters"
                                @click:clear="clearSearch"></v-text-field>
                        </v-col>

                        <v-col cols="12" md="2">
                            <v-select v-model="selectedCategory" label="Category" :items="categories" item-title="name"
                                item-value="id" hide-details clearable @update:model-value="applyFilters"></v-select>
                        </v-col>

                        <v-col cols="12" md="2">
                            <v-select v-model="selectedBrand" label="Brand" :items="brands" item-title="name"
                                item-value="id" hide-details clearable @update:model-value="applyFilters"></v-select>
                        </v-col>

                        <v-col cols="12" md="2">
                            <v-select v-model="selectedStatus" label="Status" :items="statusOptions" hide-details
                                @update:model-value="applyFilters"></v-select>
                        </v-col>

                        <v-col cols="12" md="2">
                            <v-select v-model="selectedFeatured" label="Featured" :items="featuredOptions" hide-details
                                @update:model-value="applyFilters"></v-select>
                        </v-col>

                        <v-col cols="12" md="1">
                            <v-btn color="error" variant="outlined" block @click="resetFilters">
                                Reset
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>

            <!-- Products Table -->
            <v-card>
                <v-data-table :headers="headers" :items="products.data" :loading="loading" class="elevation-1">
                    <template v-slot:item.primary_image_url="{ item }">
                        <div class="d-flex align-center py-2">
                            <v-img :src="item.primary_image_url" :alt="item.name" width="50" height="50" class="rounded"
                                cover></v-img>
                        </div>
                    </template>

                    <template v-slot:item.name="{ item }">
                        <div>
                            <div>{{ item.name }}</div>
                            <div class="text-caption text-grey">SKU: {{ item.sku }}</div>
                        </div>
                    </template>

                    <template v-slot:item.price="{ item }">
                        <div>
                            <div>${{ item.price }}</div>
                            <div v-if="item.sale_price" class="text-caption text-error">
                                Sale: ${{ item.sale_price }}
                            </div>
                        </div>
                    </template>

                    <template v-slot:item.status="{ item }">
                        <v-chip :color="item.status ? 'success' : 'error'" size="small" class="text-white">
                            {{ item.status ? 'Active' : 'Inactive' }}
                        </v-chip>
                    </template>

                    <template v-slot:item.featured="{ item }">
                        <v-chip v-if="item.featured" color="primary" size="small" class="text-white">
                            Featured
                        </v-chip>
                        <span v-else>-</span>
                    </template>

                    <template v-slot:item.actions="{ item }">
                        <Link :href="route('dashboard.products.show', item.uuid)" class="text-decoration-none">
                            <v-btn icon size="small" color="info" class="mr-1" title="View">
                                <v-icon>mdi-eye</v-icon>
                            </v-btn>
                        </Link>

                        <Link :href="route('dashboard.products.edit', item.uuid)" class="text-decoration-none">
                            <v-btn icon size="small" color="warning" class="mr-1" title="Edit">
                                <v-icon>mdi-pencil</v-icon>
                            </v-btn>
                        </Link>

                        <v-btn icon size="small" color="error" @click="confirmDelete(item)" title="Delete">
                            <v-icon>mdi-delete</v-icon>
                        </v-btn>
                    </template>
                </v-data-table>

                <!-- Pagination -->
                <div class="d-flex justify-center py-4">
                    <v-pagination v-if="products.last_page > 1" v-model="page" :length="products.last_page"
                        total-visible="7" @update:model-value="changePage"></v-pagination>
                </div>
            </v-card>
        </v-container>

        <!-- Delete Confirmation Dialog -->
        <v-dialog v-model="deleteDialog" max-width="500">
            <v-card>
                <v-card-title class="text-h5">
                    Confirm Delete
                </v-card-title>
                <v-card-text>
                    Are you sure you want to delete the product "{{ productToDelete?.name }}"? This action cannot be
                    undone.
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue-darken-1" variant="text" @click="closeDeleteDialog">
                        Cancel
                    </v-btn>
                    <v-btn color="error" variant="flat" @click="deleteProduct" :loading="deleting">
                        Delete
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Flash Messages -->
        <v-snackbar v-model="showSuccessMessage" color="success" timeout="3000" location="top">
            {{ flashMessage }}
        </v-snackbar>

        <v-snackbar v-model="showErrorMessage" color="error" timeout="3000" location="top">
            {{ errorMessage }}
        </v-snackbar>
    </DashboardLayout>
</template>

<script setup>
    import DashboardLayout from '@/Layouts/DashboardLayout.vue';
    import { Head, router } from '@inertiajs/vue3';
    import { ref } from 'vue';

    const props = defineProps({
        products: Object,
        filters: Object,
        categories: Array,
        brands: Array
    });

    // Table headers
    const headers = [
        { title: 'Image', key: 'primary_image_url', sortable: false },
        { title: 'Name', key: 'name' },
        { title: 'Category', key: 'category.name' },
        { title: 'Brand', key: 'brand.name' },
        { title: 'Price', key: 'price' },
        { title: 'Stock', key: 'stock' },
        { title: 'Status', key: 'status', sortable: false },
        { title: 'Featured', key: 'featured', sortable: false },
        { title: 'Actions', key: 'actions', sortable: false, align: 'end' }
    ];

    // Filter states
    const search = ref(props.filters.search || '');
    const selectedCategory = ref(props.filters.category || null);
    const selectedBrand = ref(props.filters.brand || null);
    const selectedStatus = ref(props.filters.status || 'all');
    const selectedFeatured = ref(props.filters.featured || 'all');
    const page = ref(props.products.current_page || 1);

    // Options for dropdowns
    const statusOptions = [
        { title: 'All', value: 'all' },
        { title: 'Active', value: 'active' },
        { title: 'Inactive', value: 'inactive' }
    ];

    const featuredOptions = [
        { title: 'All', value: 'all' },
        { title: 'Featured', value: 'featured' },
        { title: 'Not Featured', value: 'not-featured' }
    ];

    // UI state
    const loading = ref(false);
    const deleteDialog = ref(false);
    const productToDelete = ref(null);
    const deleting = ref(false);
    const showSuccessMessage = ref(false);
    const showErrorMessage = ref(false);
    const flashMessage = ref('');
    const errorMessage = ref('');

    // Check for flash messages from the backend
    if (props.flash?.success) {
        flashMessage.value = props.flash.success;
        showSuccessMessage.value = true;
    }

    if (props.flash?.error) {
        errorMessage.value = props.flash.error;
        showErrorMessage.value = true;
    }

    // Filter methods
    const applyFilters = () => {
        loading.value = true;
        router.get(route('dashboard.products.index'), {
            search: search.value,
            category: selectedCategory.value,
            brand: selectedBrand.value,
            status: selectedStatus.value,
            featured: selectedFeatured.value,
            page: 1, // Reset to first page when filtering
        }, {
            preserveState: true,
            replace: true,
            onFinish: () => {
                loading.value = false;
            }
        });
    };

    const clearSearch = () => {
        search.value = '';
        applyFilters();
    };

    const resetFilters = () => {
        search.value = '';
        selectedCategory.value = null;
        selectedBrand.value = null;
        selectedStatus.value = 'all';
        selectedFeatured.value = 'all';
        applyFilters();
    };

    const changePage = (newPage) => {
        loading.value = true;
        router.get(route('dashboard.products.index'), {
            search: search.value,
            category: selectedCategory.value,
            brand: selectedBrand.value,
            status: selectedStatus.value,
            featured: selectedFeatured.value,
            page: newPage,
        }, {
            preserveState: true,
            replace: true,
            onFinish: () => {
                loading.value = false;
            }
        });
    };

    // Delete product
    const confirmDelete = (item) => {
        productToDelete.value = item;
        deleteDialog.value = true;
    };

    const closeDeleteDialog = () => {
        deleteDialog.value = false;
        productToDelete.value = null;
    };

    /**
     * Delete the product
     *
     * @return void
     */
    const deleteProduct = () => {
        if (!productToDelete.value) return;

        deleting.value = true;
        router.delete(route('dashboard.products.destroy', productToDelete.value.uuid), {
            onSuccess: () => {
                closeDeleteDialog();
                flashMessage.value = 'Product deleted successfully';
                showSuccessMessage.value = true;
            },
            onError: (error) => {
                errorMessage.value = error.message || 'Failed to delete product';
                showErrorMessage.value = true;
            },
            onFinish: () => {
                deleting.value = false;
            }
        });
    };
</script>
