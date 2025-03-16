<template>

    <Head :title="role.name" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Role Details: {{ role.name }}
                </h2>
                <v-spacer></v-spacer>
                <Link :href="route('dashboard.roles.edit', role.id)" class="text-decoration-none">
                <v-btn color="warning" prepend-icon="mdi-pencil" class="mx-2">
                    Edit
                </v-btn>
                </Link>
                <Link :href="route('dashboard.roles.index')">
                <v-btn color="secondary" prepend-icon="mdi-arrow-left" variant="outlined">
                    Back to Roles
                </v-btn>
                </Link>
            </div>
        </template>

        <v-container fluid class="py-8">
            <v-row>
                <!-- Role Info -->
                <v-col cols="12" md="6">
                    <v-card class="mb-4">
                        <v-card-title>
                            <v-icon class="mr-2">mdi-shield-account</v-icon>
                            Role Information
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <v-list>
                                <v-list-item>
                                    <v-list-item-title>Name</v-list-item-title>
                                    <v-list-item-subtitle class="mt-1">{{ role.name }}</v-list-item-subtitle>
                                </v-list-item>

                                <v-list-item>
                                    <v-list-item-title>Created</v-list-item-title>
                                    <v-list-item-subtitle class="mt-1">
                                        {{ new Date(role.created_at).toLocaleString() }}
                                    </v-list-item-subtitle>
                                </v-list-item>

                                <v-list-item>
                                    <v-list-item-title>Last Updated</v-list-item-title>
                                    <v-list-item-subtitle class="mt-1">
                                        {{ new Date(role.updated_at).toLocaleString() }}
                                    </v-list-item-subtitle>
                                </v-list-item>
                            </v-list>
                        </v-card-text>
                    </v-card>
                </v-col>

                <!-- Permissions -->
                <v-col cols="12" md="6">
                    <v-card>
                        <v-card-title>
                            <v-icon class="mr-2">mdi-key</v-icon>
                            Permissions
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <div v-if="role.permissions.length">
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <v-chip v-for="permission in role.permissions" :key="permission.id" color="info"
                                        size="small" class="ma-1">
                                        {{ formatPermissionName(permission.name) }}
                                    </v-chip>
                                </div>
                            </div>
                            <div v-else class="text-center py-4">
                                <v-icon size="large" color="grey-lighten-1" class="mb-2">mdi-lock-off</v-icon>
                                <p class="text-subtitle-1 text-grey">No permissions assigned to this role</p>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Users with this Role -->
            <v-row class="mt-4">
                <v-col cols="12">
                    <v-card>
                        <v-card-title>
                            <v-icon class="mr-2">mdi-account-group</v-icon>
                            Users with this Role
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <div v-if="users.data.length > 0">
                                <v-data-table :headers="userHeaders" :items="users.data" class="elevation-0">
                                    <template v-slot:item.name="{ item }">
                                        <div>{{ item.name }}</div>
                                        <div class="text-caption text-grey">{{ item.email }}</div>
                                    </template>

                                    <template v-slot:item.email_verified_at="{ item }">
                                        <v-chip :color="item.email_verified_at ? 'success' : 'error'" size="small">
                                            {{ item.email_verified_at ? 'Verified' : 'Not Verified' }}
                                        </v-chip>
                                    </template>

                                    <template v-slot:item.created_at="{ item }">
                                        {{ new Date(item.created_at).toLocaleDateString() }}
                                    </template>

                                    <template v-slot:item.actions="{ item }">
                                        <div class="d-flex justify-center justify-sm-end">
                                            <Link :href="route('dashboard.users.show', item.uuid)"
                                                class="text-decoration-none">
                                            <v-btn icon size="x-small" color="info" class="mr-1" title="View User">
                                                <v-icon>mdi-eye</v-icon>
                                            </v-btn>
                                            </Link>
                                        </div>
                                    </template>
                                </v-data-table>

                                <!-- Pagination -->
                                <div class="d-flex justify-center py-4">
                                    <v-pagination v-if="users.last_page > 1" v-model="currentPage"
                                        :length="users.last_page" @update:model-value="changePage"
                                        rounded></v-pagination>
                                </div>
                            </div>
                            <div v-else class="text-center py-6">
                                <v-icon size="large" color="grey-lighten-1" class="mb-4">mdi-account-off</v-icon>
                                <p class="text-h6 text-grey">No users have been assigned this role</p>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Delete Section -->
            <v-row class="mt-4">
                <v-col cols="12">
                    <v-card color="error" variant="outlined">
                        <v-card-title class="text-warning">Danger Zone</v-card-title>
                        <v-card-text>
                            <p class="text-warning">Once you delete this role, there is no going back. Please be sure.</p>
                            <v-btn color="white" variant="outlined" class="mt-2" @click="confirmDelete">Delete
                                Role</v-btn>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Delete Confirmation Dialog -->
            <v-dialog v-model="deleteDialog" max-width="500px">
                <v-card>
                    <v-card-title class="text-h5">Delete Role</v-card-title>
                    <v-card-text>
                        Are you sure you want to delete this role? This action cannot be undone.
                        <p class="mt-4 font-weight-bold">{{ role.name }}</p>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue-darken-1" variant="text" @click="closeDeleteDialog">Cancel</v-btn>
                        <v-btn color="error" :loading="deleting" @click="deleteRole">Delete</v-btn>
                        <v-spacer></v-spacer>
                    </v-card-actions>
                </v-card>
            </v-dialog>
        </v-container>
    </DashboardLayout>
</template>

<script setup>
    import { ref } from 'vue';
    import { Head, Link, router } from '@inertiajs/vue3';
    import DashboardLayout from '@/Layouts/DashboardLayout.vue';

    const props = defineProps({
        role: Object,
        users: Object
    });

    const currentPage = ref(props.users.current_page || 1);
    const deleteDialog = ref(false);
    const deleting = ref(false);

    const userHeaders = [
        { title: 'Name/Email', key: 'name', sortable: false },
        { title: 'Email Status', key: 'email_verified_at', sortable: false },
        { title: 'Registered', key: 'created_at', sortable: false },
        { title: 'Actions', key: 'actions', sortable: false, align: 'center' }
    ];

    // Format permission name to be more readable
    const formatPermissionName = (name) => {
        return name
            .replace(/([A-Z])/g, ' $1')
            .replace(/_/g, ' ')
            .replace(/\w\S*/g, (txt) => txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase());
    };

    // Pagination
    const changePage = (newPage) => {
        router.get(route('dashboard.roles.show', props.role.id), {
            page: newPage
        }, {
            preserveState: true,
            replace: true
        });
    };

    // Delete handling
    const confirmDelete = () => {
        deleteDialog.value = true;
    };

    const closeDeleteDialog = () => {
        deleteDialog.value = false;
    };

    const deleteRole = () => {
        deleting.value = true;
        router.delete(route('dashboard.roles.destroy', props.role.id), {
            onSuccess: () => {
                // Will redirect to roles index page
            },
            onError: () => {
                closeDeleteDialog();
            },
            onFinish: () => {
                deleting.value = false;
            }
        });
    };
</script>
