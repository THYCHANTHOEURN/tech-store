<template>

    <Head title="Roles" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Roles</h2>
                <v-spacer></v-spacer>
                <Link :href="route('dashboard.roles.create')">
                <v-btn color="primary" prepend-icon="mdi-plus" class="ml-2">
                    Add New Role
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
                        <v-col cols="12" md="8">
                            <v-text-field v-model="search" label="Search Roles" prepend-inner-icon="mdi-magnify"
                                single-line hide-details clearable @update:model-value="debouncedSearch"
                                @click:clear="resetSearch"></v-text-field>
                        </v-col>

                        <v-col cols="12" md="4">
                            <v-btn color="error" variant="outlined" block @click="resetFilters">
                                Reset
                            </v-btn>
                        </v-col>
                    </v-row>

                    <!-- Total count indicator -->
                    <div class="d-flex justify-end mt-2">
                        <p class="text-caption mb-0">Total {{ roles.total }} roles</p>
                    </div>
                </v-card-text>
            </v-card>

            <!-- Roles List -->
            <v-row>
                <v-col cols="12">
                    <v-data-table :headers="headers" :items="roles.data" class="elevation-1" :loading="loading">
                        <template v-slot:item.name="{ item }">
                            <span class="font-weight-medium">{{ item.name }}</span>
                        </template>

                        <template v-slot:item.permissions="{ item }">
                            <div class="d-flex flex-wrap gap-1">
                                <v-chip v-for="permission in item.permissions.slice(0, 3)" :key="permission.id"
                                    size="small" color="info" class="mr-1">
                                    {{ formatPermissionName(permission.name) }}
                                </v-chip>
                                <v-chip v-if="item.permissions.length > 3" size="small" color="grey">
                                    +{{ item.permissions.length - 3 }} more
                                </v-chip>
                            </div>
                        </template>

                        <template v-slot:item.created_at="{ item }">
                            {{ new Date(item.created_at).toLocaleString() }}
                        </template>

                        <template v-slot:item.actions="{ item }">
                            <div class="d-flex flex-nowrap justify-center justify-sm-end">
                                <Link :href="route('dashboard.roles.show', item.id)" class="text-decoration-none">
                                <v-btn icon size="x-small" color="info" class="mr-1" title="View" rounded="lg">
                                    <v-icon>mdi-eye</v-icon>
                                </v-btn>
                                </Link>

                                <Link :href="route('dashboard.roles.edit', item.id)" class="text-decoration-none">
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
                        <v-pagination v-if="roles.last_page" v-model="currentPage" :length="roles.last_page" total-visible="7"
                            @update:model-value="changePage" rounded></v-pagination>
                    </div>
                </v-col>
            </v-row>

            <!-- Delete Confirmation Dialog -->
            <v-dialog v-model="deleteDialog" max-width="500px">
                <v-card>
                    <v-card-title class="text-h5">Delete Role</v-card-title>
                    <v-card-text>
                        Are you sure you want to delete this role? This action cannot be undone.
                        <p class="mt-4 font-weight-bold" v-if="roleToDelete">{{ roleToDelete.name }}</p>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue-darken-1" variant="text" @click="closeDeleteDialog">Cancel</v-btn>
                        <v-btn color="error" variant="flat" :loading="deleting" @click="deleteRole">Delete</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-container>
    </DashboardLayout>
</template>

<script setup>
    import { ref, watch } from 'vue';
    import { Head, Link, router } from '@inertiajs/vue3';
    import DashboardLayout from '@/Layouts/DashboardLayout.vue';
    import { debounce } from 'lodash';

    const props = defineProps({
        roles: Object,
        filters: Object
    });

    // State variables
    const search = ref(props.filters.search || '');
    const loading = ref(false);
    const currentPage = ref(props.roles.current_page);
    const roleToDelete = ref(null);
    const deleteDialog = ref(false);
    const deleting = ref(false);

    const headers = [
        { title: 'Name', key: 'name' },
        { title: 'Permissions', key: 'permissions' },
        { title: 'Created Date', key: 'created_at' },
        { title: 'Actions', key: 'actions', sortable: false, align: 'center' },
    ];

    // Methods
    const formatPermissionName = (name) => {
        return name
            .replace(/([A-Z])/g, ' $1')
            .replace(/_/g, ' ')
            .replace(/\w\S*/g, (txt) => txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase());
    };

    const debouncedSearch = debounce(() => {
        applyFilters();
    }, 300);

    const applyFilters = () => {
        loading.value = true;
        router.get(route('dashboard.roles.index'), {
            search: search.value,
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
        loading.value = true;
        router.get(route('dashboard.roles.index'), {}, {
            onFinish: () => {
                loading.value = false;
            }
        });
    };

    const resetSearch = () => {
        search.value = '';
        applyFilters();
    };

    const changePage = (newPage) => {
        loading.value = true;
        router.get(route('dashboard.roles.index'), {
            search: search.value,
            page: newPage,
        }, {
            preserveState: true,
            replace: true,
            onFinish: () => {
                loading.value = false;
            }
        });
    };

    // Delete role
    const confirmDelete = (item) => {
        roleToDelete.value = item;
        deleteDialog.value = true;
    };

    const closeDeleteDialog = () => {
        deleteDialog.value = false;
        roleToDelete.value = null;
    };

    const deleteRole = () => {
        if (!roleToDelete.value) return;

        deleting.value = true;
        router.delete(route('dashboard.roles.destroy', roleToDelete.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                closeDeleteDialog();
            },
            onError: () => {
                // Handle error
            },
            onFinish: () => {
                deleting.value = false;
            }
        });
    };

    // Watch for changes in current page
    watch(() => props.roles.current_page, (newVal) => {
        currentPage.value = newVal;
    });
</script>
