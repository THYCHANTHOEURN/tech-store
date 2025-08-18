<template>

    <Head :title="t('Product Management')" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ t('Product Management') }}
                </h2>
                <v-spacer></v-spacer>

                <!-- Import/Export Menu -->
                <v-menu location="bottom">
                    <template v-slot:activator="{ props }">
                        <v-btn color="secondary" class="mr-2" v-bind="props" prepend-icon="mdi-database-import-outline">
                            {{ t('Import/Export') }}
                        </v-btn>
                    </template>
                    <v-list>
                        <v-list-subheader>{{ t('Export Products') }}</v-list-subheader>
                        <v-list-item :href="route('dashboard.products.export', { format: 'xlsx' })"
                            prepend-icon="mdi-microsoft-excel" title="Export to Excel" />
                        <v-list-item :href="route('dashboard.products.export', { format: 'csv' })"
                            prepend-icon="mdi-file-delimited" title="Export to CSV" />

                        <v-divider class="my-2"></v-divider>

                        <v-list-subheader>Download Templates</v-list-subheader>
                        <v-list-item :href="route('dashboard.products.template', { format: 'xlsx' })"
                            prepend-icon="mdi-file-excel" title="Excel Template" />
                        <v-list-item :href="route('dashboard.products.template', { format: 'csv' })"
                            prepend-icon="mdi-file-delimited" title="CSV Template" />

                        <v-divider class="my-2"></v-divider>

                        <v-list-item @click="showImportDialog = true" prepend-icon="mdi-database-import"
                            title="Import Products" />
                    </v-list>
                </v-menu>

                <Link :href="route('dashboard.products.create')" class="text-decoration-none">
                <v-btn color="primary" prepend-icon="mdi-plus">
                    {{ t('Add Product') }}
                </v-btn>
                </Link>
            </div>
        </template>

        <v-container fluid class="py-8">
            <!-- Flash Messages -->
            <v-alert v-if="flash.success" type="success" variant="tonal" class="mb-4" closable>
                {{ flash.success }}
            </v-alert>

            <v-alert v-if="flash.error" type="error" variant="tonal" class="mb-4" closable>
                {{ flash.error }}
            </v-alert>

            <!-- Enhanced Filters -->
            <FilterBar :loading="loading" :total-items="products.total" items-label="products"
                :active-filters="activeFilters" @reset-filters="resetFilters" @clear-filter="clearFilter">
                <template #filters>
                    <v-col cols="12" md="6" lg="4">
                        <SearchField v-model="search" :label="t('Search Product')" :loading="loading" @search="applyFilters"
                            @clear="applyFilters" />
                    </v-col>

                    <v-col cols="12" sm="6" md="3" lg="2">
                        <v-select v-model="selectedCategory" :label="t('Category')" :items="categories" item-title="name"
                            item-value="id" hide-details clearable @update:model-value="applyFilters" variant="outlined"
                            density="comfortable">
                            <template v-slot:prepend-inner>
                                <v-icon color="primary" size="small">mdi-shape</v-icon>
                            </template>
                        </v-select>
                    </v-col>

                    <v-col cols="12" sm="6" md="3" lg="2">
                        <v-select v-model="selectedBrand" :label="t('Brand')" :items="brands" item-title="name"
                            item-value="id" hide-details clearable @update:model-value="applyFilters" variant="outlined"
                            density="comfortable">
                            <template v-slot:prepend-inner>
                                <v-icon color="primary" size="small">mdi-tag</v-icon>
                            </template>
                        </v-select>
                    </v-col>

                    <v-col cols="12" sm="6" md="3" lg="2">
                        <v-select v-model="selectedStatus" :label="t('Status')" :items="statusOptions" hide-details clearable
                            @update:model-value="applyFilters" variant="outlined" density="comfortable">
                            <template v-slot:prepend-inner>
                                <v-icon color="primary" size="small">mdi-eye</v-icon>
                            </template>
                        </v-select>
                    </v-col>

                    <v-col cols="12" sm="6" md="3" lg="2">
                        <v-select v-model="selectedFeatured" :label="t('Featured')" :items="featuredOptions" hide-details
                            clearable @update:model-value="applyFilters" variant="outlined" density="comfortable">
                            <template v-slot:prepend-inner>
                                <v-icon color="primary" size="small">mdi-star</v-icon>
                            </template>
                        </v-select>
                    </v-col>
                </template>
            </FilterBar>

            <!-- Products Table -->
            <v-card elevation="2">
                <v-data-table :headers="headers" :items="products.data" :items-per-page="products.per_page"
                    :loading="loading" class="elevation-0" hide-default-footer>
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
                        <v-btn :color="item.status ? 'success' : 'error'" size="small" variant="flat" density="compact"
                            class="text-white">
                            {{ item.status ? 'Published' : 'UnPublished' }}
                        </v-btn>
                    </template>

                    <template v-slot:item.featured="{ item }">
                        <v-btn :color="item.featured ? 'primary' : 'secondary'" size="small" variant="flat"
                            density="compact" class="text-white">
                            {{ item.featured ? 'Featured' : 'Not Featured' }}
                        </v-btn>
                    </template>

                    <template v-slot:item.actions="{ item }">
                        <div class="d-flex flex-nowrap justify-center justify-sm-end">
                            <Link :href="route('dashboard.products.show', item.uuid)" class="text-decoration-none">
                            <v-btn icon size="x-small" color="info" class="mr-1" title="View" rounded="lg">
                                <v-icon>mdi-eye</v-icon>
                            </v-btn>
                            </Link>

                            <Link :href="route('dashboard.products.edit', item.uuid)" class="text-decoration-none">
                            <v-btn icon size="x-small" color="warning" class="mr-1" title="Edit" rounded="lg">
                                <v-icon>mdi-pencil</v-icon>
                            </v-btn>
                            </Link>

                            <v-btn icon size="x-small" color="error" class="mr-1" @click="confirmDelete(item)"
                                title="Delete" rounded="lg">
                                <v-icon>mdi-delete</v-icon>
                            </v-btn>
                        </div>
                    </template>
                </v-data-table>

                <!-- Pagination -->
                <div class="d-flex justify-center py-4">
                    <span class="mt-4">{{ t('Rows per page:') }}</span>
                    <v-select v-model="perPage" :items="perPageOptions" class="ml-4" style="max-width: 100px;"
                        @update:model-value="changePerPage" hide-details></v-select>
                    <v-pagination v-if="products.last_page" v-model="page" :length="products.last_page"
                        total-visible="7" @update:model-value="changePage" rounded></v-pagination>
                </div>
            </v-card>

            <!-- Import Dialog -->
            <v-dialog v-model="showImportDialog" max-width="600px">
                <v-card>
                    <v-card-title class="text-h5">
                        <v-icon class="mr-2">mdi-database-import</v-icon>
                        Import Products
                    </v-card-title>

                    <v-card-text>
                        <p class="mb-4">Upload a CSV or Excel file to import products. Please follow the required
                            format.</p>

                        <v-alert type="info" variant="tonal" class="mb-4">
                            <p>Not sure about the format? Download our template:</p>
                            <div class="d-flex mt-2">
                                <v-btn size="small" variant="text" color="primary"
                                    :href="route('dashboard.products.template', { format: 'xlsx' })" class="mr-2">
                                    <v-icon start>mdi-microsoft-excel</v-icon>
                                    Excel Template
                                </v-btn>
                                <v-btn size="small" variant="text" color="primary"
                                    :href="route('dashboard.products.template', { format: 'csv' })">
                                    <v-icon start>mdi-file-delimited</v-icon>
                                    CSV Template
                                </v-btn>
                            </div>
                        </v-alert>

                        <form @submit.prevent="submitImport" enctype="multipart/form-data">
                            <v-file-input v-model="importFile" accept=".csv, .xlsx, .xls" label="Select file to import"
                                prepend-icon="mdi-file-upload" show-size :error-messages="importError"
                                required></v-file-input>
                        </form>
                    </v-card-text>

                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue-darken-1" variant="text" @click="closeImportDialog">
                            Cancel
                        </v-btn>
                        <v-btn color="primary" @click="submitImport" :loading="importing" :disabled="!importFile">
                            <v-icon start>mdi-database-import</v-icon>
                            Import
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <!-- Delete Confirmation Dialog -->
            <v-dialog v-model="deleteDialog" max-width="500">
                <v-card>
                    <v-card-title class="text-h5">
                        {{ $t('Confirm Delete') }}
                    </v-card-title>
                    <v-card-text>
                        {{ $t('Are you sure you want to delete the product') }} "{{ productToDelete?.name }}"? {{ $t('This action cannot be undone.') }}
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue-darken-1" variant="text" @click="closeDeleteDialog">
                            {{ $t('Cancel') }}
                        </v-btn>
                        <v-btn color="error" variant="flat" @click="deleteProduct" :loading="deleting">
                            {{ $t('Delete') }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-container>
    </DashboardLayout>
</template>

<script setup>
    import DashboardLayout from '@/Layouts/DashboardLayout.vue';
    import { Head, router, Link, usePage } from '@inertiajs/vue3';
    import { ref, computed } from 'vue';
    import FilterBar from '@/Components/Dashboard/FilterBar.vue';
    import SearchField from '@/Components/Dashboard/SearchField.vue';
    import { useI18n } from 'vue-i18n';

    const { t, locale } = useI18n();

    const props = defineProps({
        products: Object,
        filters: Object,
        categories: Array,
        brands: Array
    });

    // Get flash messages
    const flash = computed(() => usePage().props.flash);

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
        { title: 'Actions', key: 'actions', sortable: false, align: 'center' },
    ];

    // Filter states
    const search = ref(props.filters.search || '');
    const selectedCategory = ref(props.filters.category || null);
    const selectedBrand = ref(props.filters.brand || null);
    const selectedStatus = ref(props.filters.status || 'all');
    const selectedFeatured = ref(props.filters.featured || 'all');
    const page = ref(props.products.current_page || 1);

    // Add perPage state and options
    const perPageOptions = [10, 25, 50, 100];
    const perPage = ref(Number(props.filters.per_page) || 10);

    // Options for dropdowns
    const statusOptions = [
        { title: 'All', value: 'all' },
        { title: 'Published', value: 'published' },
        { title: 'UnPublished', value: 'unpublished' }
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

    // Import/Export Functionality
    const showImportDialog = ref(false);
    const importFile = ref(null);
    const importError = ref('');
    const importing = ref(false);

    const closeImportDialog = () => {
        showImportDialog.value = false;
        importFile.value = null;
        importError.value = '';
    };

    const submitImport = () => {
        if (!importFile.value) {
            importError.value = 'Please select a file to import';
            return;
        }

        importError.value = '';
        importing.value = true;

        const form = new FormData();
        form.append('file', importFile.value);

        router.post(route('dashboard.products.import'), form, {
            onFinish: () => {
                importing.value = false;
                closeImportDialog();
            },
            onError: (errors) => {
                importError.value = errors.file || 'Error uploading file';
                importing.value = false;
            }
        });
    };

    // Computed property for active filters
    const activeFilters = computed(() => ({
        search: {
            label: 'Search',
            value: search.value,
            displayValue: search.value,
            active: !!search.value
        },
        category: {
            label: 'Category',
            value: selectedCategory.value,
            displayValue: props.categories.find(c => c.id === selectedCategory.value)?.name,
            active: !!selectedCategory.value
        },
        brand: {
            label: 'Brand',
            value: selectedBrand.value,
            displayValue: props.brands.find(b => b.id === selectedBrand.value)?.name,
            active: !!selectedBrand.value
        },
        status: {
            label: 'Status',
            value: selectedStatus.value,
            displayValue: statusOptions.find(s => s.value === selectedStatus.value)?.title,
            active: selectedStatus.value !== 'all'
        },
        featured: {
            label: 'Featured',
            value: selectedFeatured.value,
            displayValue: featuredOptions.find(f => f.value === selectedFeatured.value)?.title,
            active: selectedFeatured.value !== 'all'
        }
    }));

    // Filter methods
    const applyFilters = () => {
        loading.value = true;
        page.value = 1; // Always reset to first page when filtering
        router.get(route('dashboard.products.index'), {
            search: search.value || undefined,
            category: selectedCategory.value || undefined,
            brand: selectedBrand.value || undefined,
            status: selectedStatus.value,
            featured: selectedFeatured.value,
            page: page.value,
            per_page: perPage.value,
        }, {
            preserveState: true,
            replace: true,
            onFinish: () => {
                // Sync page value with backend after navigation
                page.value = usePage().props.products.current_page;
                loading.value = false;
            }
        });
    };

    const clearFilter = (filterKey) => {
        switch (filterKey) {
            case 'search':
                search.value = '';
                break;
            case 'category':
                selectedCategory.value = null;
                break;
            case 'brand':
                selectedBrand.value = null;
                break;
            case 'status':
                selectedStatus.value = 'all';
                break;
            case 'featured':
                selectedFeatured.value = 'all';
                break;
        }
        applyFilters();
    };

    const resetFilters = () => {
        search.value = '';
        selectedCategory.value = null;
        selectedBrand.value = null;
        selectedStatus.value = 'all';
        selectedFeatured.value = 'all';
        page.value = 1; // Reset page
        loading.value = true;
        router.get(route('dashboard.products.index'), {
            per_page: perPage.value,
            page: page.value,
        }, {
            onFinish: () => {
                page.value = usePage().props.products.current_page;
                loading.value = false;
            }
        });
    };

    // Pagination handler
    const changePage = (newPage) => {
        loading.value = true;
        router.get(route('dashboard.products.index'), {
            search: search.value,
            category: selectedCategory.value,
            brand: selectedBrand.value,
            status: selectedStatus.value,
            featured: selectedFeatured.value,
            page: newPage,
            per_page: perPage.value,
        }, {
            preserveState: true,
            replace: true,
            onFinish: () => {
                page.value = usePage().props.products.current_page;
                loading.value = false;
            }
        });
    };

    // Change per page handler
    const changePerPage = (newPerPage) => {
        perPage.value = newPerPage;
        page.value = 1; // Reset to first page
        applyFilters();
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
            preserveScroll: true,
            onSuccess: () => {
                closeDeleteDialog();
                // Flash messages will be handled by the backend and FlashMessage component
            },
            onError: () => {
                // Error handling will be done by the FlashMessage component
            },
            onFinish: () => {
                deleting.value = false;
            }
        });
    };
</script>
