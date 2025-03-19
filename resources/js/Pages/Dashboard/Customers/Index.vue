<template>

    <Head title="Customers" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Customers
                </h2>
                <v-spacer></v-spacer>
                <Link :href="route('dashboard.customers.create')">
                <v-btn color="primary" prepend-icon="mdi-plus">
                    Add Customer
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
                        <v-col cols="12" md="4">
                            <v-text-field v-model="search" label="Search Customers" prepend-inner-icon="mdi-magnify"
                                single-line hide-details clearable @update:model-value="debouncedSearch"
                                @click:clear="resetSearch"></v-text-field>
                        </v-col>
                        <v-col cols="12" md="5">
                            <v-select v-model="selectedStatus" :items="statusOptions" label="Email Status" hide-details
                                clearable @update:model-value="filterCustomers"></v-select>
                        </v-col>

                        <v-col cols="12" md="3">
                            <v-btn color="error" variant="outlined" block @click="resetFilters">
                                Reset
                            </v-btn>
                        </v-col>
                    </v-row>

                    <!-- Total count indicator -->
                    <div class="d-flex justify-end mt-2">
                        <p class="text-caption mb-0">Total {{ customers.total }} customers</p>
                    </div>
                </v-card-text>
            </v-card>

            <!-- Customers Table -->
            <v-card>
                <v-data-table :headers="headers" :items="customers.data" :loading="loading" item-value="id"
                    :sort-by="[{ key: sortBy, order: sortOrder }]" @update:sort-by="updateSort" hide-default-footer>
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
                    <v-pagination v-if="customers.last_page" v-model="page" :length="customers.last_page"
                        total-visible="7" @update:model-value="changePage" rounded></v-pagination>
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
                    Are you sure you want to delete the customer "{{ customerToDelete?.name }}"? This action cannot be
                    undone.
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue-darken-1" variant="text" @click="closeDeleteDialog">
                        Cancel
                    </v-btn>
                    <v-btn color="error" variant="flat" @click="deleteCustomer" :loading="deleting">
                        Delete
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </DashboardLayout>
</template>

<script setup>
    import DashboardLayout from '@/Layouts/DashboardLayout.vue';
    import { Head, router, Link } from '@inertiajs/vue3';
    import { ref, watch } from 'vue';
    import { debounce } from 'lodash';

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
    const sortBy = ref('created_at');
    const sortOrder = ref('desc');

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

    // Debounced search function
    const debouncedSearch = debounce(() => {
        filterCustomers();
    }, 300);

    // Apply filters
    const filterCustomers = () => {
        loading.value = true;
        page.value = 1; // Reset to first page when filters change
        router.get(route('dashboard.customers.index'), {
            search: search.value,
            status: selectedStatus.value,
            sort_field: sortBy.value,
            sort_order: sortOrder.value,
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

    // Reset search
    const resetSearch = () => {
        search.value = '';
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
    const changePage = (newPage) => {
        loading.value = true;
        router.get(route('dashboard.customers.index'), {
            search: search.value,
            status: selectedStatus.value,
            sort_field: sortBy.value,
            sort_order: sortOrder.value,
            page: newPage,
        }, {
            preserveState: true,
            replace: true,
            onFinish: () => {
                loading.value = false;
            }
        });
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
