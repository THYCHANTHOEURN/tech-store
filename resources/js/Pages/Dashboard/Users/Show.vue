<template>

    <Head :title="user.name" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ $t('User Details') }}: {{ user.name }}
                </h2>
                <v-spacer></v-spacer>
                <Link :href="route('dashboard.users.edit', user.uuid)">
                <v-btn color="warning" prepend-icon="mdi-pencil" variant="flat" class="mr-2">
                    {{ $t('Edit User') }}
                </v-btn>
                </Link>
                <v-btn color="error" prepend-icon="mdi-delete" variant="flat" @click="confirmDelete"
                    :disabled="user.id === $page.props.auth.user.id" class="mr-2">
                    {{ $t('Delete User') }}
                </v-btn>
                <Link :href="route('dashboard.users.index')">
                <v-btn color="secondary" prepend-icon="mdi-arrow-left" variant="outlined">
                    {{ $t('Back to Users') }}
                </v-btn>
                </Link>
            </div>
        </template>

        <v-container fluid class="py-8">
            <v-row>
                <v-col cols="12" md="8">
                    <v-card>
                        <v-card-title class="text-h6 bg-light-blue-lighten-5">
                            {{ $t('User Information') }}
                        </v-card-title>
                        <v-card-text>
                            <v-row>
                                <v-col cols="12" md="6">
                                    <p class="text-subtitle-1 font-weight-bold mb-1">{{ $t('Name') }}</p>
                                    <p>{{ user.name }}</p>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <p class="text-subtitle-1 font-weight-bold mb-1">{{ $t('Email') }}</p>
                                    <p>{{ user.email }}</p>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <p class="text-subtitle-1 font-weight-bold mb-1">{{ $t('Phone') }}</p>
                                    <p>{{ user.phone || 'Not provided' }}</p>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <p class="text-subtitle-1 font-weight-bold mb-1">{{ $t('Role') }}</p>
                                    <v-chip v-for="role in user.roles" :key="role.id" :color="getRoleColor(role.name)"
                                        size="small" class="mr-1">
                                        {{ role.name }}
                                    </v-chip>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <p class="text-subtitle-1 font-weight-bold mb-1">{{ $t('Status') }}</p>
                                    <v-chip :color="user.email_verified_at ? 'success' : 'error'" size="small">
                                        {{ user.email_verified_at ? 'Verified' : 'Unverified' }}
                                    </v-chip>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <p class="text-subtitle-1 font-weight-bold mb-1">{{ $t('Created At') }}</p>
                                    <p>{{ new Date(user.created_at).toLocaleString() }}</p>
                                </v-col>
                                <v-col cols="12">
                                    <p class="text-subtitle-1 font-weight-bold mb-1">{{ $t('Address') }}</p>
                                    <p>{{ user.address || 'Not provided' }}</p>
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </v-card>
                </v-col>

                <v-col cols="12" md="4">
                    <v-card>
                        <v-card-title class="text-h6 bg-light-blue-lighten-5">
                            {{ $t('User Actions') }}
                        </v-card-title>
                        <v-card-text>
                            <v-list>
                                <v-list-item>
                                    <Link :href="route('dashboard.users.edit', user.uuid)" class="w-100">
                                    <v-btn color="primary" variant="outlined" block prepend-icon="mdi-pencil">
                                        {{ $t('Edit User') }}
                                    </v-btn>
                                    </Link>
                                </v-list-item>

                                <v-list-item v-if="user.id !== $page.props.auth.user.id">
                                    <v-btn color="error" variant="outlined" block prepend-icon="mdi-delete"
                                        @click="confirmDelete">
                                        {{ $t('Delete User') }}
                                    </v-btn>
                                </v-list-item>
                            </v-list>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>

        <!-- Delete Dialog -->
        <v-dialog v-model="deleteDialog" max-width="500px">
            <v-card>
                <v-card-title class="text-h6">
                    {{ $t('Delete User') }}
                </v-card-title>
                <v-card-text>
                    Are you sure you want to delete the user "{{ user.name }}"? This action cannot be undone.
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue-darken-1" variant="text" @click="closeDeleteDialog">
                        {{ $t('Cancel') }}
                    </v-btn>
                    <v-btn color="error" variant="flat" @click="deleteUser" :loading="deleting">
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
    import { ref } from 'vue';

    const props = defineProps({
        user: Object,
    });

    // Delete functionality
    const deleteDialog = ref(false);
    const deleting = ref(false);

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

    const confirmDelete = () => {
        deleteDialog.value = true;
    };

    const closeDeleteDialog = () => {
        deleteDialog.value = false;
    };

    const deleteUser = () => {
        deleting.value = true;
        router.delete(route('dashboard.users.destroy', props.user.uuid), {
            preserveScroll: true,
            onSuccess: () => {
                // Will be redirected to index page by controller
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
