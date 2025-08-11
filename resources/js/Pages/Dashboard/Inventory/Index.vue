<template>

    <Head title="Inventory Management" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Inventory Management
                </h2>
                <v-spacer></v-spacer>
                <v-btn color="primary" @click="showSettingsDialog = true" prepend-icon="mdi-cog">
                    Settings
                </v-btn>
            </div>
        </template>

        <v-container fluid class="py-8">
            <!-- Inventory Statistics -->
            <v-row class="mb-6">
                <v-col cols="12" sm="6" md="2">
                    <v-card color="primary" dark>
                        <v-card-text class="text-center">
                            <v-icon size="large" class="mb-2">mdi-package-variant</v-icon>
                            <div class="text-h4">{{ stats.total_products }}</div>
                            <div class="text-caption">Total Products</div>
                        </v-card-text>
                    </v-card>
                </v-col>
                <v-col cols="12" sm="6" md="2">
                    <v-card color="error" dark>
                        <v-card-text class="text-center">
                            <v-icon size="large" class="mb-2">mdi-alert-circle</v-icon>
                            <div class="text-h4">{{ stats.out_of_stock }}</div>
                            <div class="text-caption">Out of Stock</div>
                        </v-card-text>
                    </v-card>
                </v-col>
                <v-col cols="12" sm="6" md="2">
                    <v-card color="warning" dark>
                        <v-card-text class="text-center">
                            <v-icon size="large" class="mb-2">mdi-alert</v-icon>
                            <div class="text-h4">{{ stats.critical_stock }}</div>
                            <div class="text-caption">Critical Stock</div>
                        </v-card-text>
                    </v-card>
                </v-col>
                <v-col cols="12" sm="6" md="2">
                    <v-card color="orange" dark>
                        <v-card-text class="text-center">
                            <v-icon size="large" class="mb-2">mdi-trending-down</v-icon>
                            <div class="text-h4">{{ stats.low_stock }}</div>
                            <div class="text-caption">Low Stock</div>
                        </v-card-text>
                    </v-card>
                </v-col>
                <v-col cols="12" sm="6" md="2">
                    <v-card color="info" dark>
                        <v-card-text class="text-center">
                            <v-icon size="large" class="mb-2">mdi-trending-up</v-icon>
                            <div class="text-h4">{{ stats.overstock }}</div>
                            <div class="text-caption">Overstock</div>
                        </v-card-text>
                    </v-card>
                </v-col>
                <v-col cols="12" sm="6" md="2">
                    <v-card color="success" dark>
                        <v-card-text class="text-center">
                            <v-icon size="large" class="mb-2">mdi-currency-usd</v-icon>
                            <div class="text-h4">${{ formatCurrency(stats.total_stock_value) }}</div>
                            <div class="text-caption">Stock Value</div>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Filters and Search -->
            <v-card class="mb-6">
                <v-card-text>
                    <v-row align="center">
                        <v-col cols="12" md="4">
                            <v-text-field v-model="search" label="Search products..." prepend-inner-icon="mdi-magnify"
                                variant="outlined" density="comfortable" hide-details @keyup.enter="applyFilters"
                                clearable />
                        </v-col>
                        <v-col cols="12" md="3">
                            <v-select v-model="selectedFilter" :items="filterOptions" label="Filter by status"
                                variant="outlined" density="comfortable" hide-details
                                @update:model-value="applyFilters" clearable/>
                        </v-col>
                        <v-col cols="12" md="3">
                            <v-btn color="primary" @click="applyFilters" prepend-icon="mdi-filter">
                                Apply Filters
                            </v-btn>
                            <v-btn variant="text" @click="resetFilters" class="ml-2">
                                Reset
                            </v-btn>
                        </v-col>
                        <v-col cols="12" md="2" class="text-right">
                            <v-btn color="success" @click="showBulkUpdateDialog = true" prepend-icon="mdi-update">
                                Bulk Update
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>

            <!-- Products Table -->
            <v-card>
                <v-data-table
                    :headers="headers"
                    :items="products.data"
                    :items-per-page="products.per_page"
                    :loading="loading"
                    class="elevation-0"
                    hide-default-footer
                >
                    <template v-slot:item.primary_image_url="{ item }">
                        <v-img :src="item.primary_image_url" width="50" height="50" class="rounded" cover />
                    </template>

                    <template v-slot:item.name="{ item }">
                        <div>
                            <div class="font-weight-medium">{{ item.name }}</div>
                            <div class="text-caption text-grey">SKU: {{ item.sku }}</div>
                        </div>
                    </template>

                    <template v-slot:item.stock="{ item }">
                        <div class="d-flex align-center">
                            <v-chip :color="getStockColor(item)" size="small" class="mr-2">
                                {{ item.stock }}
                            </v-chip>
                            <v-icon v-if="item.stock_status !== 'in_stock'" :color="getStockColor(item)" size="small">
                                {{ getStockIcon(item.stock_status) }}
                            </v-icon>
                        </div>
                    </template>

                    <template v-slot:item.stock_status="{ item }">
                        <v-chip :color="getStockColor(item)" size="small" variant="tonal">
                            {{ getStockStatusText(item.stock_status) }}
                        </v-chip>
                    </template>

                    <template v-slot:item.price="{ item }">
                        <div>
                            <div>${{ item.price }}</div>
                            <div v-if="item.sale_price" class="text-caption text-error">
                                Sale: ${{ item.sale_price }}
                            </div>
                        </div>
                    </template>

                    <template v-slot:item.stock_value="{ item }">
                        ${{ formatCurrency(item.stock * (item.sale_price || item.price)) }}
                    </template>

                    <template v-slot:item.actions="{ item }">
                        <div class="d-flex gap-1">
                            <v-btn icon="mdi-pencil" size="x-small" color="primary" variant="text"
                                @click="editStock(item)" />
                            <Link :href="route('dashboard.products.show', item.uuid)">
                            <v-btn icon="mdi-eye" size="x-small" color="info" variant="text" />
                            </Link>
                        </div>
                    </template>
                </v-data-table>

                <!-- Pagination -->
                <div class="d-flex justify-center py-4">
                    <span class="mt-4">Rows per page:</span>
                    <v-select
                        v-model="perPage"
                        :items="perPageOptions"
                        class="ml-4"
                        style="max-width: 100px;"
                        @update:model-value="changePerPage"
                        hide-details
                    />
                    <v-pagination
                        v-if="products.last_page > 1"
                        v-model="page"
                        :length="products.last_page"
                        total-visible="7"
                        @update:model-value="changePage"
                        rounded
                    />
                </div>
            </v-card>

            <!-- Settings Dialog -->
            <v-dialog v-model="showSettingsDialog" max-width="600px">
                <v-card>
                    <v-card-title>Inventory Settings</v-card-title>
                    <v-card-text>
                        <v-form @submit.prevent="updateSettings">
                            <v-row>
                                <v-col cols="12" md="6">
                                    <v-text-field v-model.number="settingsForm.low_stock_threshold"
                                        label="Low Stock Threshold" type="number" min="1" variant="outlined"
                                        prepend-icon="mdi-trending-down" />
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-text-field v-model.number="settingsForm.critical_stock_threshold"
                                        label="Critical Stock Threshold" type="number" min="1" variant="outlined"
                                        prepend-icon="mdi-alert" />
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-text-field v-model.number="settingsForm.overstock_threshold"
                                        label="Overstock Threshold" type="number" min="100" variant="outlined"
                                        prepend-icon="mdi-trending-up" />
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-switch v-model="settingsForm.enable_stock_alerts" label="Enable Stock Alerts"
                                        color="primary" />
                                </v-col>
                            </v-row>
                        </v-form>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer />
                        <v-btn variant="text" @click="showSettingsDialog = false">Cancel</v-btn>
                        <v-btn color="primary" @click="updateSettings" :loading="savingSettings">Save</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <!-- Quick Stock Edit Dialog -->
            <v-dialog v-model="showStockEditDialog" max-width="400px">
                <v-card v-if="editingProduct">
                    <v-card-title>Update Stock: {{ editingProduct.name }}</v-card-title>
                    <v-card-text>
                        <v-text-field v-model.number="stockEditForm.stock" label="Stock Quantity" type="number" min="0"
                            variant="outlined" prepend-icon="mdi-package-variant" />
                        <div class="text-caption mt-2">
                            Current stock: {{ editingProduct.stock }}
                        </div>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer />
                        <v-btn variant="text" @click="showStockEditDialog = false">Cancel</v-btn>
                        <v-btn color="primary" @click="updateSingleStock" :loading="updatingStock">Update</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <!-- Bulk Update Dialog -->
            <v-dialog v-model="showBulkUpdateDialog" max-width="800px">
                <v-card>
                    <v-card-title>Bulk Stock Update</v-card-title>
                    <v-card-text>
                        <p class="mb-4">Update stock for multiple products at once. Only products with changes will be
                            updated.
                        </p>
                        <div class="bulk-update-list" style="max-height: 400px; overflow-y: auto;">
                            <v-row v-for="product in bulkUpdateProducts" :key="product.id" class="mb-2">
                                <v-col cols="6">
                                    <div class="d-flex align-center">
                                        <v-img :src="product.primary_image_url" width="40" height="40"
                                            class="rounded mr-3" />
                                        <div>
                                            <div class="font-weight-medium">{{ product.name }}</div>
                                            <div class="text-caption">Current: {{ product.stock }}</div>
                                        </div>
                                    </div>
                                </v-col>
                                <v-col cols="6">
                                    <v-text-field v-model.number="product.new_stock" type="number" min="0"
                                        variant="outlined" density="compact" hide-details />
                                </v-col>
                            </v-row>
                        </div>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer />
                        <v-btn variant="text" @click="showBulkUpdateDialog = false">Cancel</v-btn>
                        <v-btn color="primary" @click="bulkUpdateStock" :loading="bulkUpdating">Update All</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-container>
    </DashboardLayout>
</template>

<script setup>
    import { ref, computed, onMounted } from 'vue'
    import { Head, Link, router } from '@inertiajs/vue3'
    import DashboardLayout from '@/Layouts/DashboardLayout.vue'

    const props = defineProps({
        stats: Object,
        products: Object,
        settings: Object,
        filters: Object,
    })

    const loading = ref(false)
    const showSettingsDialog = ref(false)
    const showStockEditDialog = ref(false)
    const showBulkUpdateDialog = ref(false)
    const savingSettings = ref(false)
    const updatingStock = ref(false)
    const bulkUpdating = ref(false)

    const search = ref(props.filters.search || '')
    const selectedFilter = ref('all')
    const page = ref(props.products.current_page || 1)

    const editingProduct = ref(null)
    const stockEditForm = ref({ stock: 0 })
    const bulkUpdateProducts = ref([])

    const settingsForm = ref({
        low_stock_threshold: props.settings.low_stock_threshold,
        critical_stock_threshold: props.settings.critical_stock_threshold,
        overstock_threshold: props.settings.overstock_threshold,
        enable_stock_alerts: props.settings.enable_stock_alerts,
    })

    const headers = [
        { title: 'Image', key: 'primary_image_url', sortable: false },
        { title: 'Product', key: 'name' },
        { title: 'Category', key: 'category.name' },
        { title: 'Stock', key: 'stock' },
        { title: 'Status', key: 'stock_status' },
        { title: 'Price', key: 'price' },
        { title: 'Stock Value', key: 'stock_value', sortable: false },
        { title: 'Actions', key: 'actions', sortable: false },
    ]

    const filterOptions = [
        { title: 'All Products', value: 'all' },
        { title: 'Out of Stock', value: 'out_of_stock' },
        { title: 'Critical Stock', value: 'critical_stock' },
        { title: 'Low Stock', value: 'low_stock' },
        { title: 'Overstock', value: 'overstock' },
    ]

    const perPageOptions = [10, 25, 50, 100];
    const perPage = ref(Number(props.filters.per_page) || 10);

    const formatCurrency = (amount) => {
        return new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }).format(amount)
    }

    const getStockColor = (item) => {
        switch (item.stock_status) {
            case 'out_of_stock': return 'error'
            case 'critical': return 'warning'
            case 'low': return 'orange'
            case 'overstock': return 'info'
            default: return 'success'
        }
    }

    const getStockIcon = (status) => {
        switch (status) {
            case 'out_of_stock': return 'mdi-alert-circle'
            case 'critical': return 'mdi-alert'
            case 'low': return 'mdi-trending-down'
            case 'overstock': return 'mdi-trending-up'
            default: return 'mdi-check-circle'
        }
    }

    const getStockStatusText = (status) => {
        switch (status) {
            case 'out_of_stock': return 'Out of Stock'
            case 'critical': return 'Critical'
            case 'low': return 'Low Stock'
            case 'overstock': return 'Overstock'
            default: return 'In Stock'
        }
    }

    const applyFilters = () => {
        loading.value = true
        router.get(route('dashboard.inventory.index'), {
            search: search.value || undefined,
            filter: selectedFilter.value,
            page: 1,
            per_page: perPage.value, // Include per_page parameter
        }, {
            preserveState: true,
            replace: true,
            onFinish: () => loading.value = false,
        })
    }

    const resetFilters = () => {
        search.value = ''
        selectedFilter.value = 'all'
        page.value = 1 // Reset page
        loading.value = true
        router.get(route('dashboard.inventory.index'), {
            per_page: perPage.value, // Include per_page parameter
            page: page.value,
        }, {
            onFinish: () => loading.value = false,
        })
    }

    const changePage = (newPage) => {
        loading.value = true
        router.get(route('dashboard.inventory.index'), {
            search: search.value,
            filter: selectedFilter.value,
            page: newPage,
            per_page: perPage.value, // Include per_page parameter
        }, {
            preserveState: true,
            replace: true,
            onFinish: () => loading.value = false
        })
    }

    const changePerPage = (newPerPage) => {
        perPage.value = newPerPage;
        page.value = 1; // Reset to first page
        applyFilters();
    };

    const updateSettings = () => {
        savingSettings.value = true
        router.post(route('dashboard.inventory.settings'), settingsForm.value, {
            onSuccess: () => {
                showSettingsDialog.value = false
            },
            onFinish: () => savingSettings.value = false
        })
    }

    const editStock = (product) => {
        editingProduct.value = product
        stockEditForm.value.stock = product.stock
        showStockEditDialog.value = true
    }

    const updateSingleStock = () => {
        updatingStock.value = true
        router.post(route('dashboard.inventory.bulk-update'), {
            updates: [{
                product_id: editingProduct.value.id,
                stock: stockEditForm.value.stock
            }]
        }, {
            onSuccess: () => {
                showStockEditDialog.value = false
            },
            onFinish: () => updatingStock.value = false
        })
    }

    const prepareBulkUpdate = () => {
        bulkUpdateProducts.value = props.products.data.map(product => ({
            ...product,
            new_stock: product.stock
        }))
        showBulkUpdateDialog.value = true
    }

    const bulkUpdateStock = () => {
        const updates = bulkUpdateProducts.value
            .filter(product => product.new_stock !== product.stock)
            .map(product => ({
                product_id: product.id,
                stock: product.new_stock
            }))

        if (updates.length === 0) {
            showBulkUpdateDialog.value = false
            return
        }

        bulkUpdating.value = true
        router.post(route('dashboard.inventory.bulk-update'), { updates }, {
            onSuccess: () => {
                showBulkUpdateDialog.value = false
            },
            onFinish: () => bulkUpdating.value = false
        })
    }

    onMounted(() => {
        prepareBulkUpdate()
    })
</script>
