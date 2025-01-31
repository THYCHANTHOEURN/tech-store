<template>
    <GuestLayout>

        <Head title="Register" />

        <v-container>
            <v-form @submit.prevent="submit">
                <v-row>
                    <v-col>
                        <v-text-field v-model="form.name" label="Name" type="text" required autofocus
                            :error-messages="form.errors.name" prepend-inner-icon="mdi-account" variant="outlined" />
                    </v-col>
                </v-row>

                <v-row class="mt-4">
                    <v-col>
                        <v-text-field v-model="form.email" label="Email" type="email" required
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
                        <v-text-field v-model="form.password_confirmation" label="Confirm Password" type="password"
                            required :error-messages="form.errors.password_confirmation"
                            prepend-inner-icon="mdi-lock-check" variant="outlined" />
                    </v-col>
                </v-row>

                <v-row class="mt-4 d-flex justify-end">
                    <v-col>
                        <Link :href="route('login')" class="text-sm text-gray-600 underline hover:text-gray-900">
                        Already registered?
                        </Link>

                        <v-btn class="ms-4" :loading="form.processing" :disabled="form.processing" type="submit"
                            color="primary" prepend-icon="mdi-account-plus">
                            Register
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

    const form = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    });

    const submit = () => {
        form.post(route('register'), {
            onFinish: () => form.reset('password', 'password_confirmation'),
        });
    };

</script>
