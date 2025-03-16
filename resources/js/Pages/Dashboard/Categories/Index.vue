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
            <!-- Filters -->
            <v-card class="mb-6">
                <v-card-title>
                    <v-icon class="mr-2">mdi-filter-variant</v-icon>
                    Filters
                </v-card-title>
                <v-card-text>
                    <v-row>
                        <v-col cols="12" md="4">
                            <v-text-field v-model="search" label="Search Categories" prepend-inner-icon="mdi-magnify"
                                single-line hide-details clearable @update:model-value="applyFilters"
                                @click:clear="clearSearch"></v-text-field>
                        </v-col>

                        <v-col cols="12" md="3">
                            <v-select v-model="selectedParent" label="Parent Category" :items="parentCategories"
                                item-title="name" item-value="id" hide-details clearable
                                @update:model-value="applyFilters">
                                <template v-slot:prepend-item>
                                    <v-list-item title="Root Categories" value="root"
                                        @click="selectedParent = 'root'"></v-list-item>
                                    <v-divider></v-divider>
                                </template>
                            </v-select>
                        </v-col>

                        <v-col cols="12" md="3">
                            <v-select v-model="selectedStatus" label="Status" :items="statusOptions" hide-details
                                clearable @update:model-value="applyFilters"></v-select>
                        </v-col>

                        <v-col cols="12" md="2">
                            <v-btn color="error" variant="outlined" block @click="resetFilters">
                                Reset
                            </v-btn>
                        </v-col>
                    </v-row>

                    <!-- Total count indicator -->
                    <div class="d-flex justify-end mt-2">
                        <p class="text-caption mb-0">Total {{ categories.total }} categories</p>
                    </div>
                </v-card-text>
            </v-card>

            <!-- Categories Table -->
            <v-card>
                <v-data-table :headers="headers" :items="categories.data" :loading="loading" class="elevation-1">
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
                    <v-pagination v-if="categories.last_page > 1" v-model="page" :length="categories.last_page"
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
    import { Head, router, Link } from '@inertiajs/vue3';
    import { ref } from 'vue';

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
        { title: 'Actions', key: 'actions', sortable: false, align: 'end' },
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

    const clearSearch = () => {
        search.value = '';
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
