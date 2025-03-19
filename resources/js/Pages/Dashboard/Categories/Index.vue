<template>

    <Head title="Categories Management" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Categories Management
                </h2>
                <v-spacer></v-spacer>
                <Link :href="route('dashboard.categories.create')" class="text-decoration-none">
                <v-btn color="primary" prepend-icon="mdi-plus">
                    Add Category
                </v-btn>
                </Link>
            </div>
        </template>

        <v-container fluid class="py-8">
            <!-- Enhanced Filters -->
            <FilterBar :loading="loading" :total-items="categories.total" items-label="categories"
                :active-filters="activeFilters" @reset-filters="resetFilters" @clear-filter="clearFilter">
                <template #filters>
                    <v-col cols="12" md="6" lg="4">
                        <SearchField v-model="search" label="Search Categories" :loading="loading"
                            @search="applyFilters" @clear="applyFilters" />
                    </v-col>

                    <v-col cols="12" sm="6" md="3">
                        <v-select v-model="selectedParent" label="Parent Category" :items="parentCategories"
                            item-title="name" item-value="id" hide-details clearable @update:model-value="applyFilters"
                            variant="outlined" density="comfortable">
                            <template v-slot:prepend-inner>
                                <v-icon color="primary" size="small">mdi-folder-outline</v-icon>
                            </template>
                            <template v-slot:prepend-item>
                                <v-list-item title="Root Categories" value="root"
                                    @click="selectedParent = 'root'"></v-list-item>
                                <v-divider></v-divider>
                            </template>
                        </v-select>
                    </v-col>

                    <v-col cols="12" sm="6" md="3">
                        <v-select v-model="selectedStatus" label="Status" :items="statusOptions" item-title="title"
                            item-value="value" hide-details clearable @update:model-value="applyFilters"
                            variant="outlined" density="comfortable">
                            <template v-slot:prepend-inner>
                                <v-icon color="primary" size="small">mdi-eye</v-icon>
                            </template>
                        </v-select>
                    </v-col>
                </template>
            </FilterBar>

            <!-- Categories Table -->
            <v-card elevation="2">
                <v-data-table :headers="headers" :items="categories.data" :loading="loading" class="elevation-0"
                    hide-default-footer>
                    <template v-slot:item.image_url="{ item }">
                        <div class="d-flex align-center py-2">
                            <v-img :src="item.image_url" :alt="item.name" width="50" height="50" class="rounded"
                                cover></v-img>
                        </div>
                    </template>

                    <template v-slot:item.parent="{ item }">
                        <span v-if="item.parent">{{ item.parent.name }}</span>
                        <span v-else class="text-grey">Root Category</span>
                    </template>

                    <template v-slot:item.status="{ item }">
                        <v-chip :color="item.status ? 'success' : 'error'" size="small"
                            :text="item.status ? 'Active' : 'Inactive'"></v-chip>
                    </template>

                    <template v-slot:item.actions="{ item }">
                        <div class="d-flex flex-nowrap justify-center justify-sm-end">
                            <Link :href="route('dashboard.categories.show', item.uuid)" class="text-decoration-none">
                            <v-btn icon size="x-small" color="info" class="mr-1" title="View" rounded="lg">
                                <v-icon>mdi-eye</v-icon>
                            </v-btn>
                            </Link>

                            <Link :href="route('dashboard.categories.edit', item.uuid)" class="text-decoration-none">
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
                    <v-pagination v-if="categories.last_page" v-model="page" :length="categories.last_page"
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
                    Are you sure you want to delete the category "{{ categoryToDelete?.name }}"? This action cannot be
                    undone.
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue-darken-1" variant="text" @click="closeDeleteDialog">
                        Cancel
                    </v-btn>
                    <v-btn color="error" variant="flat" @click="deleteCategory" :loading="deleting">
                        Delete
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </DashboardLayout>
</template>

<script setup>
    import DashboardLayout from '@/Layouts/DashboardLayout.vue';
    import { Head, router } from '@inertiajs/vue3';
    import { ref, computed } from 'vue';
    import { Link } from '@inertiajs/vue3';
    import FilterBar from '@/Components/Dashboard/FilterBar.vue';
    import SearchField from '@/Components/Dashboard/SearchField.vue';

    const props = defineProps({
        categories: Object,
        filters: Object,
        parentCategories: Array
    });

    // Table headers
    const headers = [
        { title: 'Image', key: 'image_url', sortable: false },
        { title: 'Name', key: 'name', sortable: true },
        { title: 'Parent Category', key: 'parent', sortable: false },
        { title: 'Status', key: 'status', sortable: true },
        { title: 'Created At', key: 'created_at', sortable: true },
        { title: 'Actions', key: 'actions', sortable: false, align: 'center' },
    ];

    // Filter states
    const search = ref(props.filters?.search || '');
    const selectedParent = ref(props.filters?.parent || null);
    const selectedStatus = ref(props.filters?.status || 'all');
    const page = ref(props.categories.current_page || 1);

    // Status options
    const statusOptions = [
        { title: 'All', value: 'all' },
        { title: 'Active', value: 'active' },
        { title: 'Inactive', value: 'inactive' }
    ];

    // UI state
    const loading = ref(false);
    const deleteDialog = ref(false);
    const categoryToDelete = ref(null);
    const deleting = ref(false);

    // Computed property for active filters
    const activeFilters = computed(() => ({
        search: {
            label: 'Search',
            value: search.value,
            displayValue: search.value,
            active: !!search.value
        },
        parent: {
            label: 'Parent',
            value: selectedParent.value,
            displayValue: selectedParent.value === 'root'
                ? 'Root Categories'
                : props.parentCategories.find(c => c.id === selectedParent.value)?.name,
            active: !!selectedParent.value
        },
        status: {
            label: 'Status',
            value: selectedStatus.value,
            displayValue: statusOptions.find(s => s.value === selectedStatus.value)?.title,
            active: selectedStatus.value !== 'all'
        }
    }));

    // Filter methods
    const applyFilters = () => {
        loading.value = true;
        router.get(route('dashboard.categories.index'), {
            search: search.value || undefined,
            parent: selectedParent.value || undefined,
            status: selectedStatus.value,
            page: 1, // Reset to first page when filtering
        }, {
            preserveState: true,
            replace: true,
            onFinish: () => {
                loading.value = false;
            }
        });
    };

    const clearFilter = (filterKey) => {
        switch (filterKey) {
            case 'search':
                search.value = '';
                break;
            case 'parent':
                selectedParent.value = null;
                break;
            case 'status':
                selectedStatus.value = 'all';
                break;
        }
        applyFilters();
    };

    const resetFilters = () => {
        search.value = '';
        selectedParent.value = null;
        selectedStatus.value = 'all';
        loading.value = true;
        router.get(route('dashboard.categories.index'), {}, {
            onFinish: () => {
                loading.value = false;
            }
        });
    };

    const changePage = (newPage) => {
        loading.value = true;
        router.get(route('dashboard.categories.index'), {
            search: search.value,
            parent: selectedParent.value,
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

    // Delete category
    const confirmDelete = (item) => {
        categoryToDelete.value = item;
        deleteDialog.value = true;
    };

    const closeDeleteDialog = () => {
        deleteDialog.value = false;
        categoryToDelete.value = null;
    };

    /**
     * Delete the category
     *
     * @return void
     */
    const deleteCategory = () => {
        if (!categoryToDelete.value) return;

        deleting.value = true;
        router.delete(route('dashboard.categories.destroy', categoryToDelete.value.uuid), {
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
