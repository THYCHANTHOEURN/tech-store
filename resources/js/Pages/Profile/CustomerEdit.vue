<template>

    <Head title="Profile" />
    <WebLayout>
        <v-container>
            <h1 class="text-h4 mb-6">Profile</h1>

            <v-row>
                <v-col cols="12" md="8">
                    <UpdateProfileInformationForm :must-verify-email="mustVerifyEmail" :status="status" class="mb-6" />

                    <UpdatePasswordForm class="mb-6" />

                    <DeleteUserForm />
                </v-col>
            </v-row>
        </v-container>
    </WebLayout>
</template>

<script setup>
    import WebLayout from '@/Layouts/WebLayout.vue'
    import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue'
    import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue'
    import DeleteUserForm from './Partials/DeleteUserForm.vue'

    defineProps({
        mustVerifyEmail: {
            type: Boolean,
        },
        status: {
            type: String,
        },
    })
</script>
