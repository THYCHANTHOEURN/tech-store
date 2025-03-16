<template>

    <Head title="Create Role" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Create New Role
                </h2>
                <v-spacer></v-spacer>
                <Link :href="route('dashboard.roles.index')">
                <v-btn color="secondary" prepend-icon="mdi-arrow-left" variant="outlined">
                    Back to Roles
                </v-btn>
                </Link>
            </div>
        </template>

        <v-container fluid class="py-8">
            <v-row justify="center">
                <v-col cols="12" md="10" lg="8">
                    <v-card>
                        <v-card-text>
                            <RoleForm :modelValue="formData" @update:modelValue="formData = $event"
                                :permissions="permissions" :processing="processing" :errors="errors"
                                @submit="createRole" />
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </DashboardLayout>
</template>

<script setup>
    import DashboardLayout from '@/Layouts/DashboardLayout.vue';
    import RoleForm from '@/Components/Dashboard/RoleForm.vue';
    import { Head, router, Link } from '@inertiajs/vue3';
    import { ref } from 'vue';

    const props = defineProps({
        permissions: {
            type: Array,
            required: true
        },
        errors: Object,
    });

    const processing = ref(false);

    // Form data with default values
    const formData = ref({
        name: '',
        permissions: [],
    });

    // Create new role
    const createRole = (data) => {
        processing.value = true;

        router.post(route('dashboard.roles.store'), data, {
            onFinish: () => {
                processing.value = false;
            }
        });
    };
</script>
