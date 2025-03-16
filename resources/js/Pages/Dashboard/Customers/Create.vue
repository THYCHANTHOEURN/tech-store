<template>

    <Head title="Create Customer" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Create New Customer
                </h2>
                <v-spacer></v-spacer>
                <Link :href="route('dashboard.customers.index')">
                <v-btn color="secondary" prepend-icon="mdi-arrow-left" variant="outlined">
                    Back to Customers
                </v-btn>
                </Link>
            </div>
        </template>

        <v-container fluid class="py-8">
            <v-row justify="center">
                <v-col cols="12" md="10" lg="8">
                    <v-card>
                        <v-card-text>
                            <CustomerForm :modelValue="formData" @update:modelValue="formData = $event"
                                :processing="processing" :errors="errors" @submit="createCustomer" />
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </DashboardLayout>
</template>

<script setup>
    import DashboardLayout from '@/Layouts/DashboardLayout.vue';
    import CustomerForm from '@/Components/Dashboard/CustomerForm.vue';
    import { Head, router, Link } from '@inertiajs/vue3';
    import { ref } from 'vue';

    const props = defineProps({
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
    });

    const createCustomer = (data) => {
        processing.value = true;
        router.post(route('dashboard.customers.store'), data, {
            onFinish: () => {
                processing.value = false;
            }
        });
    };
</script>
