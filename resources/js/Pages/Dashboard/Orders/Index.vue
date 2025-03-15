<template>
    <Head title="Orders" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Orders</h2>
                <v-spacer></v-spacer>
                <Link :href="route('dashboard.orders.create')">
                <v-btn color="primary" prepend-icon="mdi-plus" class="ml-2">
                    Create New Order
                </v-btn>
                </Link>
            </div>
        </template>

        <v-container>
            <!-- Filters and Search -->
            <v-row>
                <v-col cols="12" md="9">
                    <v-card variant="flat" class="pa-2">
                        <div class="d-flex flex-wrap align-center">
                            <v-text-field v-model="search" placeholder="Search orders..." variant="underlined"
                                density="compact" hide-details prepend-inner-icon="mdi-magnify" class="mr-4"
                                style="max-width: 300px;" @update:model-value="debouncedSearch"></v-text-field>

                            <v-select v-model="selectedOrderStatus" :items="orderStatusOptions" label="Order Status"
                                variant="underlined" density="compact" hide-details class="mr-4"
                                style="max-width: 200px;" @update:model-value="applyFilters"></v-select>

                            <v-select v-model="selectedPaymentStatus" :items="paymentStatusOptions" label="Payment Status"
                                variant="underlined" density="compact" hide-details class="mr-4"
                                style="max-width: 200px;" @update:model-value="applyFilters"></v-select>

                            <v-btn color="error" variant="text" :disabled="!hasFilters" @click="resetFilters"
                                class="ml-auto" density="comfortable">
                                Clear Filters
                            </v-btn>
                        </div>
                    </v-card>
                </v-col>
                <v-col cols="12" md="3" class="d-flex align-center justify-end">
                    <div class="text-right">
                        <p class="text-caption mb-0">Total {{ orders.total }} orders</p>
                    </div>
                </v-col>
            </v-row>

            <!-- Orders List -->
            <v-row>
                <v-col cols="12">
                    <v-data-table :headers="headers" :items="orders.data" class="elevation-1" :loading="loading">
                        <template v-slot:item.order_id="{ item }">
                            <span class="font-weight-medium text-primary">#{{ item.uuid.slice(-8).toUpperCase() }}</span>
                        </template>

                        <template v-slot:item.customer="{ item }">
                            {{ item.user?.name }}
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
                        <v-pagination v-if="orders.last_page > 1" v-model="currentPage" :length="orders.last_page"
                            @update:model-value="changePage" rounded></v-pagination>
                    </div>
                </v-col>
            </v-row>

            <!-- Delete Confirmation Dialog -->
            <v-dialog v-model="deleteDialog" max-width="500px">
                <v-card>
                    <v-card-title class="text-h5">Delete Order</v-card-title>
                    <v-card-text>
                        Are you sure you want to delete this order? This action cannot be undone.
                        <p class="mt-4 font-weight-bold" v-if="orderToDelete">
                            Order #{{ orderToDelete.uuid.slice(-8).toUpperCase() }} - ${{ formatCurrency(orderToDelete.total_amount) }}
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
        </v-container>
    </DashboardLayout>
</template>

<script setup>
    import { ref, computed } from 'vue';
    import DashboardLayout from '@/Layouts/DashboardLayout.vue';
    import { Head, Link, router } from '@inertiajs/vue3';
    import debounce from 'lodash/debounce';

    const props = defineProps({
        orders: Object,
        filters: Object,
        orderStatuses: Array,
        paymentStatuses: Array
    });

    // State
    const search = ref(props.filters?.search || '');
    const selectedOrderStatus = ref(props.filters?.order_status || 'all');
    const selectedPaymentStatus = ref(props.filters?.payment_status || 'all');
    const loading = ref(false);
    const currentPage = ref(props.orders?.current_page || 1);
    const deleteDialog = ref(false);
    const orderToDelete = ref(null);
    const deleting = ref(false);

    // Computed properties
    const hasFilters = computed(() => {
        return search.value || selectedOrderStatus.value !== 'all' || selectedPaymentStatus.value !== 'all';
    });

    const headers = [
        { title: 'Order ID', key: 'order_id' },
        { title: 'Customer', key: 'customer' },
        { title: 'Total', key: 'total_amount' },
        { title: 'Status', key: 'status' },
        { title: 'Payment Status', key: 'payment_status' },
        { title: 'Created Date', key: 'created_at' },
        { title: 'Actions', key: 'actions', sortable: false, align: 'end' },
    ];

    const orderStatusOptions = [
        { title: 'All Statuses', value: 'all' },
        ...props.orderStatuses.map(status => ({ title: status.label, value: status.value }))
    ];

    const paymentStatusOptions = [
        { title: 'All Statuses', value: 'all' },
        ...props.paymentStatuses.map(status => ({ title: status.label, value: status.value }))
    ];

    // Helper methods
    function formatOrderStatus(status) {
        return status.charAt(0).toUpperCase() + status.slice(1);
    }

    function formatPaymentStatus(status) {
        return status.charAt(0).toUpperCase() + status.slice(1);
    }

    function getOrderStatusColor(status) {
        switch (status) {
            case 'pending': return 'warning';
            case 'processing': return 'info';
            case 'completed': return 'success';
            case 'cancelled': return 'error';
            default: return 'grey';
        }
    }

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

    // Methods
    const debouncedSearch = debounce(() => {
        applyFilters();
    }, 300);

    const applyFilters = () => {
        loading.value = true;
        router.get(route('dashboard.orders.index'), {
            search: search.value,
            order_status: selectedOrderStatus.value,
            payment_status: selectedPaymentStatus.value,
            page: 1, // Reset to first page on filter change
        }, {
            preserveState: true,
            replace: true,
            onFinish: () => {
                loading.value = false;
            }
        });
    };

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

    const changePage = (newPage) => {
        loading.value = true;
        router.get(route('dashboard.orders.index'), {
            search: search.value,
            order_status: selectedOrderStatus.value,
            payment_status: selectedPaymentStatus.value,
            page: newPage,
        }, {
            preserveState: true,
            replace: true,
            onFinish: () => {
                loading.value = false;
            }
        });
    };

    // Delete order
    const confirmDelete = (item) => {
        orderToDelete.value = item;
        deleteDialog.value = true;
    };

    const closeDeleteDialog = () => {
        deleteDialog.value = false;
        orderToDelete.value = null;
    };

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