<template>

    <Head :title="`Edit ${role.name}`" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Edit Role: {{ role.name }}
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
                            <RoleForm :modelValue="formData" @update:modelValue="formData = $event" :role="role"
                                :permissions="permissions" :rolePermissions="rolePermissions" :processing="processing"
                                :errors="errors" @submit="updateRole" />
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
    import { Head, Link, router } from '@inertiajs/vue3';
    import { ref } from 'vue';

    const props = defineProps({
        role: Object,
        permissions: Array,
        rolePermissions: Array,
        errors: Object,
    });

    const processing = ref(false);

    // Form data initialized from role
    const formData = ref({
        name: props.role.name,
        permissions: [...props.rolePermissions],
    });

    const updateRole = (data) => {
        processing.value = true;

        router.put(route('dashboard.roles.update', props.role.id), data, {
            onFinish: () => {
                processing.value = false;
            }
        });
    };
</script>
