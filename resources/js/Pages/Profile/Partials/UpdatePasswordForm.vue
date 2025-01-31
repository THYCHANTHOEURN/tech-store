<template>
    <v-container>
        <v-row>
            <v-col cols="12" sm="8" md="6">
                <v-card flat>
                    <v-card-title class="text-lg font-medium text-gray-900">
                        Update Password
                    </v-card-title>
                    <v-card-text>
                        <p class="mt-1 text-sm text-gray-600">
                            Ensure your account is using a long, random password to stay secure.
                        </p>
                        <form @submit.prevent="updatePassword" class="mt-6 space-y-6">
                            <div>
                                <v-text-field label="Current Password" ref="currentPasswordInput"
                                    v-model="form.current_password" type="password"
                                    :error-messages="form.errors.current_password" variant="solo" />
                            </div>
                            <div>
                                <v-text-field label="New Password" ref="passwordInput" v-model="form.password"
                                    type="password" :error-messages="form.errors.password" variant="solo" />
                            </div>
                            <div>
                                <v-text-field label="Confirm Password" v-model="form.password_confirmation"
                                    type="password" :error-messages="form.errors.password_confirmation"
                                    variant="solo" />
                            </div>
                            <div class="flex items-center gap-4">
                                <v-btn :loading="form.processing" type="submit" color="primary">Save</v-btn>
                                <transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0"
                                    leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">
                                        Saved.
                                    </p>
                                </transition>
                            </div>
                        </form>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </v-container>
</template>

<script setup>
    import { useForm } from '@inertiajs/vue3';
    import { ref } from 'vue';

    const passwordInput = ref(null);
    const currentPasswordInput = ref(null);

    const form = useForm({
        current_password: '',
        password: '',
        password_confirmation: '',
    });

    const updatePassword = () => {
        form.put(route('password.update'), {
            preserveScroll: true,
            onSuccess: () => form.reset(),
            onError: () => {
                if (form.errors.password) {
                    form.reset('password', 'password_confirmation');
                    passwordInput.value.focus();
                }
                if (form.errors.current_password) {
                    form.reset('current_password');
                    currentPasswordInput.value.focus();
                }
            },
        });
    };
</script>
