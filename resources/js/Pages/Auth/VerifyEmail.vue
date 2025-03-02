<script setup>
    import { computed } from 'vue';
    import GuestLayout from '@/Layouts/GuestLayout.vue';
    import { Head, Link, useForm, router } from '@inertiajs/vue3';

    const props = defineProps({
        status: {
            type: String,
        },
    });

    const form = useForm({});

    const submit = () => {
        form.post(route('verification.send'));
    };

    const verificationLinkSent = computed(
        () => props.status === 'verification-link-sent',
    );

    const logout = () => {
        router.post(route('logout'));
    };
</script>

<template>
    <GuestLayout>

        <Head title="Email Verification" />

        <v-card flat>
            <v-card-text>
                <v-alert type="info" variant="tonal" class="mb-4">
                    Thanks for signing up! Before getting started, could you verify your
                    email address by clicking on the link we just emailed to you? If you
                    didn't receive the email, we will gladly send you another.
                </v-alert>

                <v-alert v-if="verificationLinkSent" type="success" variant="tonal" class="mb-4">
                    A new verification link has been sent to the email address you
                    provided during registration.
                </v-alert>

                <form @submit.prevent="submit">
                    <v-row align="center" class="mt-4">
                        <v-col class="d-flex justify-space-between align-center">
                            <v-btn color="primary" type="submit" :loading="form.processing" :disabled="form.processing">
                                Resend Verification Email
                            </v-btn>

                            <v-btn variant="text" @click="logout" color="grey-darken-1">
                                Resend Verification Email
                            </v-btn>

                            <v-btn variant="text" @click="logout" color="grey-darken-1">
                                Log Out
                            </v-btn>
                        </v-col>
                    </v-row>
                </form>
            </v-card-text>
        </v-card>
    </GuestLayout>
</template>
