<template>

    <Head title="Profile" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">My Profile</h2>
            </div>
        </template>

        <v-container fluid class="py-8">
            <v-row justify="center">
                <v-col cols="12" sm="10" md="8" lg="6">
                    <v-card class="mb-6">
                        <v-card-title class="bg-light-blue-lighten-5">
                            <v-icon class="mr-2">mdi-account-edit</v-icon>
                            Profile Information
                        </v-card-title>
                        <v-card-text class="pt-4">
                            <UpdateProfileInformationForm :must-verify-email="mustVerifyEmail" :status="status" />
                        </v-card-text>
                    </v-card>

                    <v-card class="mb-6">
                        <v-card-title class="bg-light-blue-lighten-5">
                            <v-icon class="mr-2">mdi-lock-reset</v-icon>
                            Update Password
                        </v-card-title>
                        <v-card-text class="pt-4">
                            <UpdatePasswordForm />
                        </v-card-text>
                    </v-card>

                    <v-card>
                        <v-card-title class="bg-red-lighten-5">
                            <v-icon class="mr-2" color="error">mdi-delete-alert</v-icon>
                            Delete Account
                        </v-card-title>
                        <v-card-text class="pt-4">
                            <DeleteUserForm />
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </DashboardLayout>
</template>

<script setup>
    import DashboardLayout from '@/Layouts/DashboardLayout.vue';
    import DeleteUserForm from './Partials/DeleteUserForm.vue';
    import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
    import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
    import { Head } from '@inertiajs/vue3';

    defineProps({
        mustVerifyEmail: {
            type: Boolean,
        },
        status: {
            type: String,
        },
    });
</script>
