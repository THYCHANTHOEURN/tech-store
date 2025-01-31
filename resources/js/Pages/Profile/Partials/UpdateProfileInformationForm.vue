<template>
    <v-container>
        <v-row>
            <v-col cols="12" sm="8" md="6">
                <v-card flat>
                    <v-card-title class="text-lg font-medium text-gray-900">
                        Profile Information
                    </v-card-title>
                    <v-card-text>
                        <p class="mt-1 text-sm text-gray-600">
                            Update your account's profile information and email address.
                        </p>
                        <form @submit.prevent="form.patch(route('profile.update'))" class="mt-6 space-y-6">
                            <div>
                                <v-text-field label="Name" v-model="form.name" required autofocus
                                    :error-messages="form.errors.name" variant="solo" />
                            </div>
                            <div>
                                <v-text-field label="Email" type="email" v-model="form.email" required
                                    :error-messages="form.errors.email" variant="solo" />
                            </div>
                            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                                <p class="mt-2 text-sm text-gray-800">
                                    Your email address is unverified.
                                    <Link :href="route('verification.send')" method="post" as="button"
                                        class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    Click here to re-send the verification email.
                                    </Link>
                                </p>
                                <v-alert v-show="status === 'verification-link-sent'" type="success" class="mt-2">
                                    A new verification link has been sent to your email address.
                                </v-alert>
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
    import { useForm, usePage } from '@inertiajs/vue3';

    defineProps({
        mustVerifyEmail: {
            type: Boolean,
        },
        status: {
            type: String,
        },
    });

    const user = usePage().props.auth.user;

    const form = useForm({
        name: user.name,
        email: user.email,
    });
</script>
