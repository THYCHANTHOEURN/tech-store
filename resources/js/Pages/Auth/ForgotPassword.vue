<template>
    <GuestLayout>

        <Head title="Forgot Password" />

        <v-container fluid>
            <v-row justify="center">
                <v-col cols="12" sm="8" md="12">
                    <v-card flat>
                        <v-card-title class="text-lg font-medium text-gray-900">
                            Forgot Password
                        </v-card-title>
                        <v-card-text>
                            <p class="mb-4 text-sm text-gray-600">
                                Forgot your password? No problem. Just let us know your email address and we will email
                                you a password reset link that will allow you to choose a new one.
                            </p>
                            <v-alert v-if="status" type="success" class="mb-4">
                                {{ status }}
                            </v-alert>
                            <form @submit.prevent="submit">
                                <div>
                                    <v-text-field label="Email" type="email" v-model="form.email" required autofocus
                                        :error-messages="form.errors.email" variant="solo" />
                                </div>
                                <div class="mt-4 flex items-center justify-end">
                                    <v-btn :loading="form.processing" type="submit" color="primary">
                                        Email Password Reset Link
                                    </v-btn>
                                </div>
                            </form>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </GuestLayout>
</template>

<script setup>
    import GuestLayout from '@/Layouts/GuestLayout.vue';
    import { useForm } from '@inertiajs/vue3';

    defineProps({
        status: {
            type: String,
        },
    });

    const form = useForm({
        email: '',
    });

    const submit = () => {
        form.post(route('password.email'));
    };
</script>
