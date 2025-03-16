<template>

    <Head title="Users" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Users
                </h2>
                <v-spacer></v-spacer>
                <Link :href="route('dashboard.users.create')">
                <v-btn color="primary" prepend-icon="mdi-plus" variant="flat">
                    Create User
                </v-btn>
                </Link>
            </div>
        </template>

        <v-container fluid>
            <!-- Filters -->
            <v-card class="mb-6">
                <v-card-title>
                    <v-icon class="mr-2">mdi-filter-variant</v-icon>
                    Filters
                </v-card-title>
                <v-card-text>
                    <v-row>
                        <v-col cols="12" md="4">
                            <v-text-field v-model="search" label="Search Users" prepend-inner-icon="mdi-magnify"
                                single-line hide-details clearable @update:model-value="debouncedSearch"
                                @click:clear="clearSearch">
                            </v-text-field>
                        </v-col>

                        <v-col cols="12" md="3">
                            <v-select v-model="filters.role" label="Role" :items="[{ title: 'All Roles', value: 'all' },
                            ...roles.map(role => ({ title: role.name, value: role.name }))]" item-title="title"
                                item-value="value" hide-details clearable @update:model-value="filterUsers"></v-select>
                        </v-col>

                        <v-col cols="12" md="3">
                            <v-select v-model="filters.status" label="Status" :items="[
                                { title: 'All Status', value: 'all' },
                                { title: 'Verified', value: 'verified' },
                                { title: 'Unverified', value: 'unverified' }
                            ]" item-title="title" item-value="value" hide-details clearable
                                @update:model-value="filterUsers"></v-select>
                        </v-col>

                        <v-col cols="12" md="2">
                            <v-btn color="error" variant="outlined" block @click="resetFilters">
                                Reset
                            </v-btn>
                        </v-col>
                    </v-row>

                    <!-- Total count indicator -->
                    <div class="d-flex justify-end mt-2">
                        <p class="text-caption mb-0">Total {{ users.total }} users</p>
                    </div>
                </v-card-text>
            </v-card>

            <!-- Users Table -->
            <v-card>
                <v-data-table :headers="headers" :items="users.data" :loading="loading" class="elevation-1">
                    <template v-slot:item.roles="{ item }">
                        <v-chip v-for="role in item.roles" :key="role.id" :color="getRoleColor(role.name)" size="small"
                            class="mr-1">
                            {{ role.name }}
                        </v-chip>
                    </template>

                    <template v-slot:item.email_verified_at="{ item }">
                        <v-chip :color="item.email_verified_at ? 'success' : 'error'" size="small">
                            {{ item.email_verified_at ? 'Verified' : 'Unverified' }}
                        </v-chip>
                    </template>

                    <template v-slot:item.actions="{ item }">
                        <div class="d-flex flex-nowrap justify-center justify-sm-end">
                            <Link :href="route('dashboard.users.show', item.uuid)" class="text-decoration-none">
                            <v-btn icon size="x-small" color="info" class="mr-1" title="View" rounded="lg">
                                <v-icon>mdi-eye</v-icon>
                            </v-btn>
                            </Link>
                            <Link :href="route('dashboard.users.edit', item.uuid)" class="text-decoration-none">
                            <v-btn icon size="x-small" color="warning" class="mr-1" title="Edit" rounded="lg">
                                <v-icon>mdi-pencil</v-icon>
                            </v-btn>
                            </Link>
                            <v-btn icon size="x-small" color="error" class="mr-1" @click="confirmDelete(item)"
                                :disabled="item.id === $page.props.auth.user.id" title="Delete" rounded="lg">
                                <v-icon>mdi-delete</v-icon>
                            </v-btn>
                        </div>
                    </template>
                </v-data-table>

                <!-- Pagination -->
                <div class="d-flex justify-center py-4">
                    <Pagination :links="users.links" />
                </div>
            </v-card>
        </v-container>

        <!-- Delete Dialog -->
        <v-dialog v-model="deleteDialog" max-width="500px">
            <v-card>
                <v-card-title class="text-h6">
                    Delete User
                </v-card-title>
                <v-card-text>
                    Are you sure you want to delete the user "{{ userToDelete?.name }}"? This action cannot be
                    undone.
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue-darken-1" variant="text" @click="closeDeleteDialog">
                        Cancel
                    </v-btn>
                    <v-btn color="error" variant="flat" @click="deleteUser" :loading="deleting">
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
    import Pagination from '@/Components/Pagination.vue';

    const props = defineProps({
        users: Object,
        roles: Array,
        filters: Object
    });

    // Table headers
    const headers = [
        { title: 'Name', key: 'name' },
        { title: 'Email', key: 'email' },
        { title: 'Role', key: 'roles' },
        { title: 'Status', key: 'email_verified_at' },
        { title: 'Created At', key: 'created_at' },
        { title: 'Actions', key: 'actions', sortable: false }
    ];

    // State
    const loading = ref(false);
    const search = ref(props.filters.search || '');
    const filters = ref({
        role: props.filters.role || 'all',
        status: props.filters.status || 'all',
    });
    const userToDelete = ref(null);
    const deleteDialog = ref(false);
    const deleting = ref(false);

    // Watch for search input and apply debounce
    const debouncedSearch = debounce(() => {
        applyFilters();
    }, 300);

    // Apply filters
    const filterUsers = () => {
        applyFilters();
    };

    // Apply all filters and reload users
    const applyFilters = () => {
        loading.value = true;
        router.get(route('dashboard.users.index'), {
            search: search.value,
            role: filters.value.role,
            status: filters.value.status,
            page: 1, // Reset to first page when filtering
        }, {
            preserveState: true,
            replace: true,
            onSuccess: () => {
                loading.value = false;
            },
        });
    };

    // Clear search
    const clearSearch = () => {
        search.value = '';
        applyFilters();
    };

    // Reset all filters
    const resetFilters = () => {
        search.value = '';
        filters.value.role = 'all';
        filters.value.status = 'all';
        loading.value = true;
        router.get(route('dashboard.users.index'), {}, {
            onFinish: () => {
                loading.value = false;
            }
        });
    };

    // Get role color for chips
    const getRoleColor = (role) => {
        switch (role) {
            case 'super-admin': return 'indigo';
            case 'admin': return 'purple';
            case 'manager': return 'blue';
            case 'staff': return 'green';
            default: return 'grey';
        }
    };

    // Delete user functions
    const confirmDelete = (item) => {
        userToDelete.value = item;
        deleteDialog.value = true;
    };

    const closeDeleteDialog = () => {
        deleteDialog.value = false;
        userToDelete.value = null;
    };

    const deleteUser = () => {
        if (!userToDelete.value) return;

        deleting.value = true;
        router.delete(route('dashboard.users.destroy', userToDelete.value.uuid), {
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
