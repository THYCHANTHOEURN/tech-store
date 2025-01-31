<template>
    <GuestLayout>

        <Head title="Log in" />

        <v-container>
            <v-row>
                <v-col>
                    <v-alert v-if="status" type="success" class="mb-4">
                        {{ status }}
                    </v-alert>
                </v-col>
            </v-row>

            <v-form @submit.prevent="submit">
                <v-row>
                    <v-col>
                        <v-text-field v-model="form.email" label="Email" type="email" required autofocus
                            :error-messages="form.errors.email" prepend-inner-icon="mdi-email" variant="outlined" />
                    </v-col>
                </v-row>

                <v-row class="mt-4">
                    <v-col>
                        <v-text-field v-model="form.password" label="Password" type="password" required
                            :error-messages="form.errors.password" prepend-inner-icon="mdi-lock" variant="outlined" />
                    </v-col>
                </v-row>

                <v-row class="mt-4">
                    <v-col>
                        <v-checkbox v-model="form.remember" label="Remember me" />
                    </v-col>
                </v-row>

                <v-row class="mt-4">
                    <v-col class="d-flex justify-end">
                        <Link v-if="canResetPassword" :href="route('password.request')"
                            class="text-sm text-gray-600 underline hover:text-gray-900">
                        Forgot your password?
                        </Link>

                        <v-btn class="ms-4" :loading="form.processing" :disabled="form.processing" type="submit"
                            color="primary" prepend-icon="mdi-login">
                            Log in
                        </v-btn>
                    </v-col>
                </v-row>
            </v-form>
        </v-container>
    </GuestLayout>
</template>

<script setup>
    import GuestLayout from '@/Layouts/GuestLayout.vue';
    import { useForm } from '@inertiajs/vue3';

    defineProps({
        canResetPassword: {
            type: Boolean,
        },
        status: {
            type: String,
        },
    });

    const form = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const submit = () => {
        form.post(route('login'), {
            onFinish: () => form.reset('password'),
        });
    };
</script>
