<template>

    <Head title="Orders" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Orders</h2>
                <v-spacer></v-spacer>

                <!-- Export Menu -->
                <v-menu location="bottom">
                    <template v-slot:activator="{ props }">
                        <v-btn color="secondary" class="mr-2" v-bind="props" prepend-icon="mdi-database-export-outline">
                            Export
                        </v-btn>
                    </template>
                    <v-list>
                        <v-list-item :href="route('dashboard.orders.export', { format: 'xlsx' })"
                            prepend-icon="mdi-microsoft-excel" title="Export to Excel" />
                        <v-list-item :href="route('dashboard.orders.export', { format: 'csv' })"
                            prepend-icon="mdi-file-delimited" title="Export to CSV" />
                    </v-list>
                </v-menu>

                <Link :href="route('dashboard.orders.create')">
                <v-btn color="primary" prepend-icon="mdi-plus" class="ml-2">
                    Create New Order
                </v-btn>
                </Link>
            </div>
        </template>

        <v-container fluid class="py-8">
            <!-- Enhanced Filters -->
            <FilterBar :loading="loading" :total-items="orders.total" items-label="orders"
                :active-filters="activeFilters" @reset-filters="resetFilters" @clear-filter="clearFilter">
                <template #filters>
                    <v-col cols="12" md="4">
                        <SearchField v-model="search" label="Search Orders" :loading="loading" @search="applyFilters"
                            @clear="applyFilters" />
                    </v-col>

                    <v-col cols="12" md="4">
                        <v-select v-model="selectedOrderStatus" :items="orderStatusOptions" label="Order Status"
                            hide-details clearable variant="outlined" density="comfortable"
                            @update:model-value="applyFilters">
                            <template v-slot:prepend-inner>
                                <v-icon color="primary" size="small">mdi-cart-outline</v-icon>
                            </template>
                        </v-select>
                    </v-col>

                    <v-col cols="12" md="4">
                        <v-select v-model="selectedPaymentStatus" :items="paymentStatusOptions" label="Payment Status"
                            hide-details clearable variant="outlined" density="comfortable"
                            @update:model-value="applyFilters">
                            <template v-slot:prepend-inner>
                                <v-icon color="primary" size="small">mdi-credit-card-outline</v-icon>
                            </template>
                        </v-select>
                    </v-col>
                </template>
            </FilterBar>

            <!-- Orders Table -->
            <v-card elevation="2">
                <v-data-table :headers="headers" :items="orders.data" :items-per-page="orders.per_page"
                    :loading="loading" class="elevation-0" hide-default-footer>
                    <template v-slot:item.order_id="{ item }">
                        <span class="font-weight-medium text-primary">#{{ item.uuid.slice(-8).toUpperCase()
                            }}</span>
                    </template>

                    <template v-slot:item.customer="{ item }">
                        {{ item.user?.name }}
                    </template>

                    <template v-slot:item.phone="{ item }">
                        <span v-if="item.phone">{{ item.phone }}</span>
                        <span v-else class="text-grey-lighten-1">Not provided</span>
                    </template>

                    <template v-slot:item.total_amount="{ item }">
                        <span class="font-weight-bold">${{ formatCurrency(item.total_amount) }}</span>
                    </template>

                    <template v-slot:item.status="{ item }">
                        <v-chip :color="getOrderStatusColor(item.status)" size="small">
                            {{ formatOrderStatus(item.status) }}
                        </v-chip>
                    </template>

                    <template v-slot:item.payment_status="{ item }">
                        <v-chip :color="getPaymentStatusColor(item.payment_status)" size="small">
                            {{ formatPaymentStatus(item.payment_status) }}
                        </v-chip>
                    </template>

                    <template v-slot:item.created_at="{ item }">
                        {{ formatDate(item.created_at) }}
                    </template>

                    <template v-slot:item.actions="{ item }">
                        <div class="d-flex flex-nowrap justify-center justify-sm-end">
                            <Link :href="route('dashboard.orders.show', item.uuid)" class="text-decoration-none">
                            <v-btn icon size="x-small" color="info" class="mr-1" title="View" rounded="lg">
                                <v-icon>mdi-eye</v-icon>
                            </v-btn>
                            </Link>

                            <Link :href="route('dashboard.orders.edit', item.uuid)" class="text-decoration-none">
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
                    <v-select v-model="perPage" :items="perPageOptions" class="ml-4"
                        style="max-width: 100px;" @update:model-value="changePerPage" hide-details />
                    <v-pagination v-if="orders.last_page" v-model="currentPage" :length="orders.last_page"
                        total-visible="7" @update:model-value="changePage" rounded></v-pagination>
                </div>
            </v-card>
        </v-container>

        <!-- Delete Confirmation Dialog -->
        <v-dialog v-model="deleteDialog" max-width="500px">
            <v-card>
                <v-card-title class="text-h5">Delete Order</v-card-title>
                <v-card-text>
                    Are you sure you want to delete this order? This action cannot be undone.
                    <p class="mt-4 font-weight-bold" v-if="orderToDelete">
                        Order #{{ orderToDelete.uuid.slice(-8).toUpperCase() }} - ${{
                            formatCurrency(orderToDelete.total_amount) }}
                    </p>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue-darken-1" variant="text" @click="closeDeleteDialog">Cancel</v-btn>
                    <v-btn color="error" :loading="deleting" @click="deleteOrder">Delete</v-btn>
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </DashboardLayout>
</template>

<script setup>
    import DashboardLayout from '@/Layouts/DashboardLayout.vue';
    import { Head, router, Link } from '@inertiajs/vue3';
    import { ref, computed } from 'vue';
    import { debounce } from 'lodash';
    import FilterBar from '@/Components/Dashboard/FilterBar.vue';
    import SearchField from '@/Components/Dashboard/SearchField.vue';

    const props = defineProps({
        orders: Object,
        filters: Object,
        orderStatuses: Array,
        paymentStatuses: Array
    });

    // State variables
    const search = ref(props.filters?.search || '');
    const selectedOrderStatus = ref(props.filters?.order_status || 'all');
    const selectedPaymentStatus = ref(props.filters?.payment_status || 'all');
    const loading = ref(false);
    const currentPage = ref(props.orders?.current_page || 1);
    const deleteDialog = ref(false);
    const orderToDelete = ref(null);
    const deleting = ref(false);

    // Table headers
    const headers = [
        { title: 'Order ID', key: 'order_id' },
        { title: 'Customer', key: 'customer' },
        { title: 'Phone', key: 'phone' },
        { title: 'Total', key: 'total_amount' },
        { title: 'Status', key: 'status' },
        { title: 'Payment Status', key: 'payment_status' },
        { title: 'Created Date', key: 'created_at' },
        { title: 'Actions', key: 'actions', sortable: false, align: 'end' },
    ];

    // Status options
    const orderStatusOptions = [
        { title: 'All Statuses', value: 'all' },
        ...props.orderStatuses.map(status => ({
            title: status.label,
            value: status.value
        }))
    ];

    // Payment status options
    const paymentStatusOptions = [
        { title: 'All Payment Statuses', value: 'all' },
        ...props.paymentStatuses.map(status => ({
            title: status.label,
            value: status.value
        }))
    ];

    // Computed property for active filters
    const activeFilters = computed(() => ({
        search: {
            label: 'Search',
            value: search.value,
            displayValue: search.value,
            active: !!search.value
        },
        order_status: {
            label: 'Order Status',
            value: selectedOrderStatus.value,
            displayValue: orderStatusOptions.find(s => s.value === selectedOrderStatus.value)?.title,
            active: selectedOrderStatus.value !== 'all'
        },
        payment_status: {
            label: 'Payment Status',
            value: selectedPaymentStatus.value,
            displayValue: paymentStatusOptions.find(s => s.value === selectedPaymentStatus.value)?.title,
            active: selectedPaymentStatus.value !== 'all'
        }
    }));

    const perPageOptions = [10, 25, 50, 100];
    const perPage = ref(Number(props.filters.per_page) || 10);

    // Apply filters
    const applyFilters = () => {
        loading.value = true;
        router.get(route('dashboard.orders.index'), {
            search: search.value,
            order_status: selectedOrderStatus.value,
            payment_status: selectedPaymentStatus.value,
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
            case 'order_status':
                selectedOrderStatus.value = 'all';
                break;
            case 'payment_status':
                selectedPaymentStatus.value = 'all';
                break;
        }
        applyFilters();
    };

    // Reset filters
    const resetFilters = () => {
        search.value = '';
        selectedOrderStatus.value = 'all';
        selectedPaymentStatus.value = 'all';
        loading.value = true;
        router.get(route('dashboard.orders.index'), {}, {
            onFinish: () => {
                loading.value = false;
            }
        });
    };

    // Page change
    const changePage = (newPage) => {
        loading.value = true;
        router.get(route('dashboard.orders.index'), {
            search: search.value,
            order_status: selectedOrderStatus.value,
            payment_status: selectedPaymentStatus.value,
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

    // Helper functions for formatting and order status/payment status display
    function formatOrderStatus(status) {
        return status.charAt(0).toUpperCase() + status.slice(1);
    }

    // Format payment status
    function formatPaymentStatus(status) {
        return status.charAt(0).toUpperCase() + status.slice(1);
    }

    // Get color for order status
    function getOrderStatusColor(status) {
        switch (status) {
            case 'pending': return 'warning';
            case 'processing': return 'info';
            case 'completed': return 'success';
            case 'cancelled': return 'error';
            default: return 'grey';
        }
    }

    // Get color for payment status
    function getPaymentStatusColor(status) {
        switch (status) {
            case 'paid': return 'success';
            case 'pending': return 'warning';
            case 'unpaid': return 'grey';
            case 'failed': return 'error';
            default: return 'grey';
        }
    }

    /// Format currency
    function formatCurrency(amount) {
        return amount ? Number(amount).toFixed(2) : '0.00';
    }

    function formatDate(dateString) {
        const options = { year: 'numeric', month: 'short', day: 'numeric' };
        return new Date(dateString).toLocaleDateString(undefined, options);
    }

    // Delete order methods
    const confirmDelete = (item) => {
        orderToDelete.value = item;
        deleteDialog.value = true;
    };

    const closeDeleteDialog = () => {
        deleteDialog.value = false;
        orderToDelete.value = null;
    };


    /**
     * Delete an order
     *
     * @returns {void}
     */
    const deleteOrder = () => {
        if (!orderToDelete.value) return;

        deleting.value = true;
        router.delete(route('dashboard.orders.destroy', orderToDelete.value.uuid), {
            preserveScroll: true,
            onSuccess: () => {
                closeDeleteDialog();
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
