<template>

    <Head title="Brands" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Brands</h2>
                <v-spacer></v-spacer>
                <Link :href="route('dashboard.brands.create')">
                <v-btn color="primary" prepend-icon="mdi-plus" class="ml-2">
                    Add New Brand
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
                            <v-text-field v-model="search" placeholder="Search brands..." variant="underlined"
                                density="compact" hide-details prepend-inner-icon="mdi-magnify" class="mr-4"
                                style="max-width: 300px;" @update:model-value="debouncedSearch"></v-text-field>

                            <v-select v-model="selectedStatus" :items="statusOptions" label="Status"
                                variant="underlined" density="compact" hide-details class="mr-4"
                                style="max-width: 150px;" @update:model-value="applyFilters"></v-select>

                            <v-btn color="error" variant="text" :disabled="!hasFilters" @click="resetFilters"
                                class="ml-auto" density="comfortable">
                                Clear Filters
                            </v-btn>
                        </div>
                    </v-card>
                </v-col>
                <v-col cols="12" md="3" class="d-flex align-center justify-end">
                    <div class="text-right">
                        <p class="text-caption mb-0">Total {{ brands.total }} brands</p>
                    </div>
                </v-col>
            </v-row>

            <!-- Brands List -->
            <v-row>
                <v-col cols="12">
                    <v-data-table :headers="headers" :items="brands.data" class="elevation-1" :loading="loading">
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
                                <Link :href="route('dashboard.brands.show', item.uuid)" class="text-decoration-none">
                                <v-btn icon size="x-small" color="info" class="mr-1" title="View" rounded="lg">
                                    <v-icon>mdi-eye</v-icon>
                                </v-btn>
                                </Link>

                                <Link :href="route('dashboard.brands.edit', item.uuid)" class="text-decoration-none">
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
                        <v-pagination v-if="brands.last_page > 1" v-model="currentPage" :length="brands.last_page"
                            @update:model-value="changePage" rounded></v-pagination>
                    </div>
                </v-col>
            </v-row>

            <!-- Delete Confirmation Dialog -->
            <v-dialog v-model="deleteDialog" max-width="500px">
                <v-card>
                    <v-card-title class="text-h5">Delete Brand</v-card-title>
                    <v-card-text>
                        Are you sure you want to delete this brand? This action cannot be undone.
                        <p class="mt-4 font-weight-bold" v-if="brandToDelete">{{ brandToDelete.name }}</p>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue-darken-1" variant="text" @click="closeDeleteDialog">Cancel</v-btn>
                        <v-btn color="error" :loading="deleting" @click="deleteBrand">Delete</v-btn>
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
        brands: Object,
        filters: Object
    });

    // State
    const search = ref(props.filters?.search || '');
    const selectedStatus = ref(props.filters?.status || 'all');
    const loading = ref(false);
    const currentPage = ref(props.brands?.current_page || 1);
    const deleteDialog = ref(false);
    const brandToDelete = ref(null);
    const deleting = ref(false);

    // Computed properties
    const hasFilters = computed(() => {
        return search.value || selectedStatus.value !== 'all';
    });

    const headers = [
        { title: 'Logo', key: 'logo', sortable: false },
        { title: 'Name', key: 'name' },
        { title: 'Status', key: 'status' },
        { title: 'Created Date', key: 'created_at' },
        { title: 'Actions', key: 'actions', sortable: false, align: 'end' },
    ];

    const statusOptions = [
        { title: 'All Statuses', value: 'all' },
        { title: 'Active', value: 'active' },
        { title: 'Inactive', value: 'inactive' },
    ];

    // Methods
    const debouncedSearch = debounce(() => {
        applyFilters();
    }, 300);

    const applyFilters = () => {
        loading.value = true;
        router.get(route('dashboard.brands.index'), {
            search: search.value,
            status: selectedStatus.value,
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
        selectedStatus.value = 'all';
        loading.value = true;
        router.get(route('dashboard.brands.index'), {}, {
            onFinish: () => {
                loading.value = false;
            }
        });
    };

    const changePage = (newPage) => {
        loading.value = true;
        router.get(route('dashboard.brands.index'), {
            search: search.value,
            status: selectedStatus.value,
            page: newPage,
        }, {
            preserveState: true,
            replace: true,
            onFinish: () => {
                loading.value = false;
            }
        });
    };

    // Delete brand
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
