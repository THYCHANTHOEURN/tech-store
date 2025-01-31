<template>
    <v-container>
        <v-row>
            <v-col cols="12" sm="8" md="6">
                <v-card flat>
                    <v-card-title class="text-lg font-medium text-gray-900">
                        Delete Account
                    </v-card-title>
                    <v-card-text>
                        <p class="mt-1 text-sm text-gray-600">
                            Once your account is deleted, all of its resources and data will be permanently deleted.
                            Before deleting your account, please download any data or information that you wish to
                            retain.
                        </p>
                        <v-btn class="my-5" color="error" @click="confirmUserDeletion">Delete Account</v-btn>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <v-dialog v-model="confirmingUserDeletion" max-width="500">
            <v-card>
                <v-card-title class="text-lg font-medium text-gray-900">
                    Are you sure you want to delete your account?
                </v-card-title>
                <v-card-text>
                    <p class="mt-1 text-sm text-gray-600">
                        Once your account is deleted, all of its resources and data will be permanently deleted. Please
                        enter your password to confirm you would like to permanently delete your account.
                    </p>
                    <v-text-field label="Password" ref="passwordInput" v-model="form.password" type="password"
                        :error-messages="form.errors.password" variant="solo" @keyup.enter="deleteUser" />
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn text @click="closeModal">Cancel</v-btn>
                    <v-btn color="error" :loading="form.processing" @click="deleteUser">Delete Account</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-container>
</template>

<script setup>
    import { useForm } from '@inertiajs/vue3';
    import { nextTick, ref } from 'vue';

    const confirmingUserDeletion = ref(false);
    const passwordInput = ref(null);

    const form = useForm({
        password: '',
    });

    const confirmUserDeletion = () => {
        confirmingUserDeletion.value = true;

        nextTick(() => passwordInput.value.focus());
    };

    const deleteUser = () => {
        form.delete(route('profile.destroy'), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
            onError: () => passwordInput.value.focus(),
            onFinish: () => form.reset(),
        });
    };

    const closeModal = () => {
        confirmingUserDeletion.value = false;

        form.clearErrors();
        form.reset();
    };
</script>
