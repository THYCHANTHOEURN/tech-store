<template>

    <Head :title="`Edit ${customer.name}`" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Edit Customer: {{ customer.name }}
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
                                :customer="customer" :processing="processing" :errors="errors"
                                @submit="updateCustomer" />
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
        customer: Object,
        errors: Object,
        flash: Object
    });

    const processing = ref(false);

    // Form data initialized from customer
    const formData = ref({
        name: props.customer.name,
        email: props.customer.email,
        password: '', // Don't populate password for security reasons
        phone: props.customer.phone || '',
        address: props.customer.address || '',
    });

    const updateCustomer = (data) => {
        processing.value = true;
        router.put(route('dashboard.customers.update', props.customer.uuid), data, {
            onFinish: () => {
                processing.value = false;
            },
            preserveScroll: true
        });
    };
</script>
