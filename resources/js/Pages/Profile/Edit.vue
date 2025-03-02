<template>

    <Head title="Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Profile</h2>
        </template>

        <v-container class="py-12">
            <v-row justify="center">
                <v-col cols="12" sm="10" md="8" lg="6">
                    <v-card class="mb-6">
                        <v-card-text>
                            <UpdateProfileInformationForm :must-verify-email="mustVerifyEmail" :status="status" />
                        </v-card-text>
                    </v-card>

                    <v-card class="mb-6">
                        <v-card-text>
                            <UpdatePasswordForm />
                        </v-card-text>
                    </v-card>

                    <v-card>
                        <v-card-text>
                            <DeleteUserForm />
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </AuthenticatedLayout>
</template>

<script setup>
    import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
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
