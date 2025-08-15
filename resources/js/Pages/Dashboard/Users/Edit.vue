<template>

    <Head :title="`Edit ${user.name}`" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ $t('Edit User') }}: {{ user.name }}
                </h2>
                <v-spacer></v-spacer>
                <Link :href="route('dashboard.users.index')">
                <v-btn color="secondary" prepend-icon="mdi-arrow-left" variant="outlined">
                    {{ $t('Back to Users') }}
                </v-btn>
                </Link>
            </div>
        </template>

        <v-container fluid class="py-8">
            <v-row justify="center">
                <v-col cols="12" md="10" lg="8">
                    <v-card>
                        <v-card-text>
                            <UserForm :modelValue="formData" @update:modelValue="formData = $event" :user="user"
                                :roles="roles" :processing="processing" :errors="errors" @submit="updateUser" />
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
        user: Object,
        userRole: String,
        roles: Array,
        errors: Object,
        flash: Object
    });

    const processing = ref(false);

    // Form data initialized from user
    const formData = ref({
        name: props.user.name,
        email: props.user.email,
        password: '', // Don't populate password for security reasons
        phone: props.user.phone || '',
        address: props.user.address || '',
        role: props.userRole || '',
    });

    const updateUser = (data) => {
        processing.value = true;
        router.put(route('dashboard.users.update', props.user.uuid), data, {
            onFinish: () => {
                processing.value = false;
            },
            preserveScroll: true
        });
    };
</script>
