<template>

    <Head title="Brands Management" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ $t('Brands Management') }}
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
                        <v-list-item :href="route('dashboard.brands.export', { format: 'xlsx' })"
                            prepend-icon="mdi-microsoft-excel" title="Export to Excel" />
                        <v-list-item :href="route('dashboard.brands.export', { format: 'csv' })"
                            prepend-icon="mdi-file-delimited" title="Export to CSV" />
                    </v-list>
                </v-menu>

                <Link :href="route('dashboard.brands.create')" class="text-decoration-none">
                <v-btn color="primary" prepend-icon="mdi-plus">
                    {{ $t('Add Brand') }}
                </v-btn>
                </Link>
            </div>
        </template>

        <v-container fluid class="py-8">
            <!-- Enhanced Filters -->
            <FilterBar :loading="loading" :total-items="brands.total" items-label="brands"
                :active-filters="activeFilters" @reset-filters="resetFilters" @clear-filter="clearFilter">
                <template #filters>
                    <v-col cols="12" md="6">
                        <SearchField v-model="search" :label="$t('Search Brands')" :loading="loading" @search="applyFilters"
                            @clear="applyFilters" />
                    </v-col>

                    <v-col cols="12" md="6">
                        <v-select v-model="selectedStatus" :items="statusOptions" :label="$t('Status')" hide-details clearable
                            @update:model-value="applyFilters" variant="outlined" density="comfortable">
                            <template v-slot:prepend-inner>
                                <v-icon color="primary" size="small">mdi-check-circle</v-icon>
                            </template>
                        </v-select>
                    </v-col>
                </template>
            </FilterBar>

            <!-- Brands List -->
            <v-row>
                <v-col cols="12">
                    <v-card>
                        <v-data-table :headers="headers" :items="brands.data" :items-per-page="brands.per_page"
                            :loading="loading" class="elevation-0" hide-default-footer>
                            <template v-slot:item.logo="{ item }">
                                <div class="d-flex align-center py-2">
                                    <v-img :src="item.logo_url" width="50" height="50" cover class="rounded"></v-img>
                                </div>
                            </template>

                            <template v-slot:item.status="{ item }">
                                <v-chip :color="item.status ? 'success' : 'error'" size="small">
                                    {{ item.status ? 'Active' : 'Inactive' }}
                                </v-chip>
                            </template>

                            <template v-slot:item.actions="{ item }">
                                <div class="d-flex flex-nowrap justify-center justify-sm-end">
                                    <Link :href="route('dashboard.brands.show', item.uuid)"
                                        class="text-decoration-none">
                                    <v-btn icon size="x-small" color="info" class="mr-1" title="View" rounded="lg">
                                        <v-icon>mdi-eye</v-icon>
                                    </v-btn>
                                    </Link>

                                    <Link :href="route('dashboard.brands.edit', item.uuid)"
                                        class="text-decoration-none">
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
                            <span class="mt-4">{{ $t('Rows per page') }}:</span>
                            <v-select v-model="perPage" :items="perPageOptions" class="ml-4"
                                style="max-width: 100px;" @update:model-value="changePerPage" hide-details />
                            <v-pagination v-if="brands.last_page" v-model="currentPage" :length="brands.last_page"
                                total-visible="7" @update:model-value="changePage" rounded></v-pagination>
                        </div>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>

        <!-- Delete Confirmation Dialog -->
        <v-dialog v-model="deleteDialog" max-width="500px">
            <v-card>
                <v-card-title class="text-h5">{{ $t('Delete Brand') }}</v-card-title>
                <v-card-text>
                    {{ $t('Are you sure you want to delete this brand? This action cannot be undone.') }}
                    <p class="mt-4 font-weight-bold" v-if="brandToDelete">{{ brandToDelete.name }}</p>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue-darken-1" variant="text" @click="closeDeleteDialog">
                        {{ $t('Cancel') }}
                    </v-btn>
                    <v-btn color="error" variant="flat" @click="deleteBrand" :loading="deleting">
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
    import debounce from 'lodash.debounce';
    import FilterBar from '@/Components/Dashboard/FilterBar.vue';
    import SearchField from '@/Components/Dashboard/SearchField.vue';

    const props = defineProps({
        brands: Object,
        filters: Object
    });

    // State variables
    const search = ref(props.filters.search || '');
    const selectedStatus = ref(props.filters.status || 'all');
    const currentPage = ref(props.brands.current_page || 1);
    const loading = ref(false);
    const deleteDialog = ref(false);
    const brandToDelete = ref(null);
    const deleting = ref(false);

    // Table headers
    const headers = [
        { title: 'Logo', key: 'logo', sortable: false },
        { title: 'Name', key: 'name' },
        { title: 'Status', key: 'status' },
        { title: 'Created Date', key: 'created_at' },
        { title: 'Actions', key: 'actions', sortable: false, align: 'center' },
    ];

    const statusOptions = [
        { title: 'All Statuses', value: 'all' },
        { title: 'Active', value: 'active' },
        { title: 'Inactive', value: 'inactive' },
    ];

    const perPageOptions = [10, 25, 50, 100];
    const perPage = ref(Number(props.filters.per_page) || 10);

    // Computed property for active filters
    const activeFilters = computed(() => ({
        search: {
            label: 'Search',
            value: search.value,
            displayValue: search.value,
            active: !!search.value
        },
        status: {
            label: 'Status',
            value: selectedStatus.value,
            displayValue: statusOptions.find(s => s.value === selectedStatus.value)?.title,
            active: selectedStatus.value !== 'all'
        }
    }));

    // Apply filters
    const applyFilters = () => {
        loading.value = true;
        router.get(route('dashboard.brands.index'), {
            search: search.value,
            status: selectedStatus.value,
            page: 1, // Reset to first page on filter change
            per_page: perPage.value, // Include per_page parameter
        }, {
            preserveState: true,
            replace: true,
            onFinish: () => {
                loading.value = false;
            }
        });
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
        }
        applyFilters();
    };

    // Reset all filters
    const resetFilters = () => {
        search.value = '';
        selectedStatus.value = 'all';
        loading.value = true;
        router.get(route('dashboard.brands.index'), {}, {
            onFinish: () => {
                loading.value = false;
            }
        });
    };

    // Pagination
    const changePage = (newPage) => {
        loading.value = true;
        router.get(route('dashboard.brands.index'), {
            search: search.value,
            status: selectedStatus.value,
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
        currentPage.value = 1; // Reset to first page
        applyFilters();
    };

    // Delete brand methods
    const confirmDelete = (item) => {
        brandToDelete.value = item;
        deleteDialog.value = true;
    };

    const closeDeleteDialog = () => {
        deleteDialog.value = false;
        brandToDelete.value = null;
    };

    const deleteBrand = () => {
        if (!brandToDelete.value) return;

        deleting.value = true;
        router.delete(route('dashboard.brands.destroy', brandToDelete.value.uuid), {
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
