<template>
    <v-container>
        <v-row>
            <v-col cols="12" sm="8" md="6">
                <v-card flat>
                    <v-card-title class="text-lg font-medium text-gray-900">
                        {{ $t('Delete Account') }}
                    </v-card-title>
                    <v-card-text>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ $t('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                        </p>
                        <v-btn class="my-5" color="error" @click="confirmUserDeletion" prepend-icon="mdi-delete">
                            {{ $t('Delete Account') }}
                        </v-btn>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>

        <v-dialog v-model="confirmingUserDeletion" max-width="500">
            <v-card>
                <v-card-title class="text-lg font-medium text-gray-900">
                    {{ $t('Are you sure you want to delete your account?') }}
                </v-card-title>
                <v-card-text>
                    <p class="mt-1 text-sm text-gray-600">
                        {{ $t('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                    </p>
                    <v-text-field :label="$t('Password')" ref="passwordInput" v-model="form.password"
                        :type="passwordVisible ? 'text' : 'password'" :error-messages="form.errors.password"
                        prepend-inner-icon="mdi-key-outline"
                        :append-inner-icon="passwordVisible ? 'mdi-eye-off' : 'mdi-eye'"
                        @click:append-inner="passwordVisible = !passwordVisible" variant="solo"
                        @keyup.enter="deleteUser" />
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn text @click="closeModal" prepend-icon="mdi-close">{{ $t('Cancel') }}</v-btn>
                    <v-btn color="error" :loading="form.processing" @click="deleteUser"
                        prepend-icon="mdi-delete-forever">
                        {{ $t('Delete Account') }}
                    </v-btn>
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
    const passwordVisible = ref(false);

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
        passwordVisible.value = false;

        form.clearErrors();
        form.reset();
    };
</script>
