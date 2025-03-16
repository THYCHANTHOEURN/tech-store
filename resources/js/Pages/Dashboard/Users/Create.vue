<template>

    <Head title="Create User" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Create New User
                </h2>
                <v-spacer></v-spacer>
                <Link :href="route('dashboard.users.index')">
                <v-btn color="secondary" prepend-icon="mdi-arrow-left" variant="outlined">
                    Back to Users
                </v-btn>
                </Link>
            </div>
        </template>

        <v-container fluid class="py-8">
            <v-row justify="center">
                <v-col cols="12" md="10" lg="8">
                    <v-card>
                        <v-card-text>
                            <UserForm :modelValue="formData" @update:modelValue="formData = $event" :roles="roles"
                                :processing="processing" :errors="errors" @submit="createUser" />
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </DashboardLayout>
</template>

<script setup>
    import DashboardLayout from '@/Layouts/DashboardLayout.vue';
    import UserForm from '@/Components/Dashboard/UserForm.vue';
    import { Head, router, Link } from '@inertiajs/vue3';
    import { ref } from 'vue';

    const props = defineProps({
        roles: Array,
        errors: Object,
        flash: Object
    });

    const processing = ref(false);

    // Form data with default values
    const formData = ref({
        name: '',
        email: '',
        password: '',
        phone: '',
        address: '',
        role: '',
    });

    const createUser = (data) => {
        processing.value = true;
        router.post(route('dashboard.users.store'), data, {
            onFinish: () => {
                processing.value = false;
            }
        });
    };
</script>
