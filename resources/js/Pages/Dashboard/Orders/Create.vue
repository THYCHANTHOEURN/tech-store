<template>
    <Head title="Create Order" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Create New Order
                </h2>
                <v-spacer></v-spacer>
                <Link :href="route('dashboard.orders.index')">
                <v-btn color="secondary" prepend-icon="mdi-arrow-left" variant="outlined">
                    Back to Orders
                </v-btn>
                </Link>
            </div>
        </template>

        <v-container fluid class="py-8">
            <v-row justify="center">
                <v-col cols="12">
                    <v-card>
                        <v-card-text>
                            <OrderForm :modelValue="formData" @update:modelValue="formData = $event"
                                :users="users" :products="products" :orderStatuses="orderStatuses" 
                                :paymentStatuses="paymentStatuses" :processing="processing" 
                                :errors="errors" @submit="createOrder" />
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </DashboardLayout>
</template>

<script setup>
    import DashboardLayout from '@/Layouts/DashboardLayout.vue';
    import OrderForm from '@/Components/Dashboard/OrderForm.vue';
    import { Head, router, Link } from '@inertiajs/vue3';
    import { ref } from 'vue';

    const props = defineProps({
        users: Array,
        products: Array,
        orderStatuses: Array,
        paymentStatuses: Array,
        errors: Object,
        flash: Object
    });

    const processing = ref(false);

    // Form data with default values
    const formData = ref({
        user_id: null,
        shipping_address: '',
        payment_method: 'cash',
        status: 'pending',
        payment_status: 'unpaid',
        items: []
    });

    // Create new order
    const createOrder = (data) => {
        processing.value = true;
        router.post(route('dashboard.orders.store'), data, {
            onFinish: () => {
                processing.value = false;
            }
        });
    };
</script>