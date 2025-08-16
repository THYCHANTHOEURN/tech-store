<template>

    <Head title="Roles Management" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ $t('Roles Management') }}
                </h2>
                <v-spacer></v-spacer>
                <Link :href="route('dashboard.roles.create')" class="text-decoration-none">
                <v-btn color="primary" prepend-icon="mdi-plus">
                    {{ $t('Create Role') }}
                </v-btn>
                </Link>
            </div>
        </template>

        <v-container fluid class="py-8">
            <!-- Enhanced Filters -->
            <FilterBar :loading="loading" :total-items="roles.total" items-label="roles" :active-filters="activeFilters"
                @reset-filters="resetFilters" @clear-filter="clearFilter">
                <template #filters>
                    <v-col cols="12">
                        <SearchField v-model="search" :label="$t('Search Roles')" :loading="loading" @search="applyFilters"
                            @clear="applyFilters" />
                    </v-col>
                </template>
            </FilterBar>

            <!-- Roles Table -->
            <v-card elevation="2">
                <v-data-table
                    :headers="headers"
                    :items="roles.data"
                    :items-per-page="roles.per_page"
                    :loading="loading"
                    class="elevation-0"
                    hide-default-footer
                >
                    <template v-slot:item.users_count="{ item }">
                        <v-chip color="primary" size="small">
                            {{ item.users_count }}
                        </v-chip>
                    </template>

                    <template v-slot:item.permissions_count="{ item }">
                        <v-chip color="info" size="small">
                            {{ item.permissions_count }}
                        </v-chip>
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
                                title="Delete" rounded="lg"
                                :disabled="item.name === 'super-admin' || item.name === 'customer'">
                                <v-icon>mdi-delete</v-icon>
                            </v-btn>
                        </div>
                    </template>
                </v-data-table>

                <!-- Pagination -->
                <div class="d-flex justify-center py-4">
                    <span class="mt-4">{{ $t('Rows per page') }}:</span>
                    <v-select
                        v-model="perPage"
                        :items="perPageOptions"
                        class="ml-4"
                        style="max-width: 100px;"
                        @update:model-value="changePerPage"
                        hide-details
                    />
                    <v-pagination
                        v-if="roles.last_page"
                        v-model="page"
                        :length="roles.last_page"
                        total-visible="7"
                        @update:model-value="changePage"
                        rounded
                    />
                </div>
            </v-card>
        </v-container>

        <!-- Delete Confirmation Dialog -->
        <v-dialog v-model="deleteDialog" max-width="500px">
            <v-card>
                <v-card-title class="text-h5">{{ $t('Delete Role') }}</v-card-title>
                <v-card-text>
                    {{ $t('Are you sure you want to delete this role? This action cannot be undone.') }}
                    <p class="mt-4 font-weight-bold" v-if="roleToDelete">{{ roleToDelete.name }}</p>
                    <p class="text-red" v-if="roleToDelete && roleToDelete.users_count > 0">
                        Warning: This role is assigned to {{ roleToDelete.users_count }} user(s).
                    </p>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue-darken-1" variant="text" @click="closeDeleteDialog">
                        {{ $t('Cancel') }}
                    </v-btn>
                    <v-btn color="error" variant="flat" @click="deleteRole" :loading="deleting">
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
    import { debounce } from 'lodash';

    const props = defineProps({
        roles: Object,
        filters: Object
    });

    // Table headers
    const headers = [
        { title: 'Name', key: 'name' },
        { title: 'Users Count', key: 'users_count', align: 'center' },
        { title: 'Permissions', key: 'permissions_count', align: 'center' },
        { title: 'Created At', key: 'created_at' },
        { title: 'Actions', key: 'actions', sortable: false, align: 'center' },
    ];

    // State
    const search = ref(props.filters?.search || '');
    const page = ref(props.roles.current_page || 1);
    const loading = ref(false);
    const deleteDialog = ref(false);
    const roleToDelete = ref(null);
    const deleting = ref(false);
    const perPageOptions = [10, 25, 50, 100];
    const perPage = ref(Number(props.filters.per_page) || 10);

    // Computed property for active filters
    const activeFilters = computed(() => ({
        search: {
            label: 'Search',
            value: search.value,
            displayValue: search.value,
            active: !!search.value
        }
    }));

    // Apply filters
    const applyFilters = () => {
        loading.value = true;
        router.get(route('dashboard.roles.index'), {
            search: search.value,
            page: 1, // Reset to first page when filtering
            per_page: perPage.value, // Include per_page parameter
        }, {
            preserveState: true,
            replace: true,
            onFinish: () => {
                loading.value = false;
            },
        });
    };

    // Clear specific filter
    const clearFilter = (filterKey) => {
        if (filterKey === 'search') {
            search.value = '';
            applyFilters();
        }
    };

    // Reset all filters
    const resetFilters = () => {
        search.value = '';
        loading.value = true;
        router.get(route('dashboard.roles.index'), {}, {
            onFinish: () => {
                loading.value = false;
            }
        });
    };

    // Pagination
    const changePage = (newPage) => {
        loading.value = true;
        router.get(route('dashboard.roles.index'), {
            search: search.value,
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
        applyFilters();
    };

    // Delete role methods
    const confirmDelete = (item) => {
        if (item.name === 'super-admin' || item.name === 'customer') {
            return; // Cannot delete protected roles
        }
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
