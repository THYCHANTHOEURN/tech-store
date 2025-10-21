<template>

    <Head title="Customers" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ $t('Customers') }}
                </h2>
                <v-spacer></v-spacer>

                <!-- Export Menu -->
                <v-menu location="bottom">
                    <template v-slot:activator="{ props }">
                        <v-btn color="secondary" class="mr-2" v-bind="props" prepend-icon="mdi-database-export-outline">
                            {{ $t('Export') }}
                        </v-btn>
                    </template>
                    <v-list>
                        <v-list-item :href="route('dashboard.customers.export', { format: 'xlsx' })"
                            prepend-icon="mdi-microsoft-excel" title="Export to Excel" />
                        <v-list-item :href="route('dashboard.customers.export', { format: 'csv' })"
                            prepend-icon="mdi-file-delimited" title="Export to CSV" />
                    </v-list>
                </v-menu>

                <Link :href="route('dashboard.customers.create')">
                <v-btn color="primary" prepend-icon="mdi-plus">
                    {{ $t('Add Customer') }}
                </v-btn>
                </Link>
            </div>
        </template>

        <v-container fluid class="py-8">
            <!-- Enhanced Filters -->
            <FilterBar :loading="loading" :total-items="customers.total" items-label="customers"
                :active-filters="activeFilters" @reset-filters="resetFilters" @clear-filter="clearFilter">
                <template #filters>
                    <v-col cols="12" md="6">
                        <SearchField v-model="search" :label="$t('Search Customers')" :loading="loading"
                            @search="filterCustomers" @clear="filterCustomers" />
                    </v-col>

                    <v-col cols="12" sm="6" md="6">
                        <v-select v-model="selectedStatus" :items="statusOptions" :label="$t('Email Status')" hide-details
                            clearable @update:model-value="filterCustomers" variant="outlined" density="comfortable">
                            <template v-slot:prepend-inner>
                                <v-icon color="primary" size="small">mdi-email-check</v-icon>
                            </template>
                        </v-select>
                    </v-col>
                </template>
            </FilterBar>

            <!-- Customers Table -->
            <v-card elevation="2">
                <v-data-table :headers="headers" :items="customers.data" :items-per-page="customers.per_page"
                    :loading="loading" item-value="id" :sort-by="[{ key: sortBy, order: sortOrder }]" @update:sort-by="updateSort"
                    class="elevation-0" hide-default-footer>
                    <template v-slot:item.email_verified_at="{ item }">
                        <v-chip :color="item.email_verified_at ? 'success' : 'error'" size="small"
                            class="text-uppercase">
                            {{ item.email_verified_at ? 'Verified' : 'Unverified' }}
                        </v-chip>
                    </template>

                    <template v-slot:item.created_at="{ item }">
                        {{ new Date(item.created_at).toLocaleDateString() }}
                    </template>

                    <template v-slot:item.actions="{ item }">
                        <div class="d-flex flex-nowrap justify-center justify-sm-end">
                            <Link :href="route('dashboard.customers.show', item.uuid)" class="text-decoration-none">
                            <v-btn icon size="x-small" color="info" class="mr-1" title="View" rounded="lg">
                                <v-icon>mdi-eye</v-icon>
                            </v-btn>
                            </Link>
                            <Link :href="route('dashboard.customers.edit', item.uuid)" class="text-decoration-none">
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
                    <span class="mt-4">Rows per page:</span>
                    <v-select v-model="perPage" :items="perPageOptions" class="ml-4" style="max-width: 100px;"
                        @update:model-value="changePerPage" hide-details />
                    <v-pagination v-if="customers.last_page" v-model="page" :length="customers.last_page"
                        total-visible="7" @update:model-value="changePage" rounded></v-pagination>
                </div>
            </v-card>
        </v-container>

        <!-- Delete Confirmation Dialog -->
        <v-dialog v-model="deleteDialog" max-width="500">
            <v-card>
                <v-card-title class="text-h5">
                    {{ $t('Confirm Delete') }}
                </v-card-title>
                <v-card-text>
                    {{ $t('Are you sure you want to delete the customer') }} "{{ customerToDelete?.name }}"? {{ $t('This action cannot be undone.') }}
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue-darken-1" variant="text" @click="closeDeleteDialog">
                        {{ $t('Cancel') }}
                    </v-btn>
                    <v-btn color="error" variant="flat" @click="deleteCustomer" :loading="deleting">
                        {{ $t('Delete') }}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </DashboardLayout>
</template>

<script setup>
    import DashboardLayout from '@/Layouts/DashboardLayout.vue';
    import { Head, router, Link } from '@inertiajs/vue3';
    import { ref, computed } from 'vue';
    import FilterBar from '@/Components/Dashboard/FilterBar.vue';
    import SearchField from '@/Components/Dashboard/SearchField.vue';
    import debounce from 'lodash.debounce';

    const props = defineProps({
        customers: Object,
        filters: Object
    });

    // Table headers
    const headers = [
        { title: 'Name', key: 'name', sortable: true },
        { title: 'Email', key: 'email', sortable: true },
        { title: 'Phone', key: 'phone', sortable: false },
        { title: 'Status', key: 'email_verified_at', sortable: true },
        { title: 'Joined', key: 'created_at', sortable: true },
        { title: 'Actions', key: 'actions', sortable: false, align: 'center' },
    ];

    // Filter and sort states
    const loading = ref(false);
    const search = ref(props.filters.search || '');
    const selectedStatus = ref(props.filters.status || 'all');
    const page = ref(props.customers?.current_page || 1);
    const sortBy = ref(props.filters.sort_field || 'created_at');
    const sortOrder = ref(props.filters.sort_order || 'desc');

    // Delete dialog
    const deleteDialog = ref(false);
    const customerToDelete = ref(null);
    const deleting = ref(false);

    // Status filter options
    const statusOptions = [
        { title: 'All Customers', value: 'all' },
        { title: 'Verified Email', value: 'verified' },
        { title: 'Unverified Email', value: 'unverified' },
    ];

    // Computed property for active filters
    const activeFilters = computed(() => ({
        search: {
            label: 'Search',
            value: search.value,
            displayValue: search.value,
            active: !!search.value
        },
        status: {
            label: 'Email Status',
            value: selectedStatus.value,
            displayValue: statusOptions.find(s => s.value === selectedStatus.value)?.title,
            active: selectedStatus.value !== 'all'
        },
        sort: {
            label: 'Sort By',
            value: `${sortBy.value} ${sortOrder.value}`,
            displayValue: `${sortBy.value === 'created_at' ? 'Date' : sortBy.value} (${sortOrder.value === 'asc' ? 'Ascending' : 'Descending'})`,
            active: sortBy.value !== 'created_at' || sortOrder.value !== 'desc'
        }
    }));

    // Apply filters
    const filterCustomers = () => {
        loading.value = true;
        page.value = 1; // Reset to first page when filters change
        router.get(route('dashboard.customers.index'), {
            search: search.value,
            status: selectedStatus.value,
            sort_field: sortBy.value,
            sort_order: sortOrder.value,
            per_page: perPage.value, // Include per_page parameter
        }, {
            preserveState: true,
            replace: true,
            onFinish: () => {
                loading.value = false;
            }
        });
    };

    // Change sort order
    const updateSort = (sortItems) => {
        if (sortItems.length > 0) {
            sortBy.value = sortItems[0].key;
            sortOrder.value = sortItems[0].order;
        } else {
            sortBy.value = 'created_at';
            sortOrder.value = 'desc';
        }
        filterCustomers();
    };

    // Clear specific filter
    const clearFilter = (filterKey) => {
        switch (filterKey) {
            case 'search':
                search.value = '';
                break;
            case 'status':
                selectedStatus.value = 'all';
                break;
            case 'sort':
                sortBy.value = 'created_at';
                sortOrder.value = 'desc';
                break;
        }
        filterCustomers();
    };

    // Reset all filters
    const resetFilters = () => {
        search.value = '';
        selectedStatus.value = 'all';
        sortBy.value = 'created_at';
        sortOrder.value = 'desc';
        page.value = 1;
        loading.value = true;
        router.get(route('dashboard.customers.index'), {}, {
            onFinish: () => {
                loading.value = false;
            }
        });
    };

    // Pagination
    const perPageOptions = [10, 25, 50, 100];
    const perPage = ref(Number(props.filters.per_page) || 10);

    const changePage = (newPage) => {
        loading.value = true;
        router.get(route('dashboard.customers.index'), {
            search: search.value,
            status: selectedStatus.value,
            sort_field: sortBy.value,
            sort_order: sortOrder.value,
            page: newPage,
            per_page: perPage.value, // Include per_page parameter
        }, {
            preserveState: true,
            replace: true,
            onFinish: () => {
                loading.value = false;
            }
        });
    };

    const changePerPage = (newPerPage) => {
        perPage.value = newPerPage;
        page.value = 1; // Reset to first page
        filterCustomers();
    };

    // Delete customer
    const confirmDelete = (item) => {
        customerToDelete.value = item;
        deleteDialog.value = true;
    };

    const closeDeleteDialog = () => {
        deleteDialog.value = false;
        customerToDelete.value = null;
    };

    const deleteCustomer = () => {
        if (!customerToDelete.value) return;

        deleting.value = true;
        router.delete(route('dashboard.customers.destroy', customerToDelete.value.uuid), {
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
