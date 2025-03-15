<template>
    <Head :title="`Edit Order #${order.uuid.slice(-8).toUpperCase()}`" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Edit Order: #{{ order.uuid.slice(-8).toUpperCase() }}
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
                            <OrderForm :modelValue="formData" @update:modelValue="formData = $event" :order="order"
                                :users="users" :products="products" :orderStatuses="orderStatuses"
                                :paymentStatuses="paymentStatuses" :processing="processing" 
                                :errors="errors" @submit="updateOrder" />
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
    import { Head, Link, router } from '@inertiajs/vue3';
    import { ref } from 'vue';

    const props = defineProps({
        order: Object,
        users: Array,
        products: Array,
        orderStatuses: Array,
        paymentStatuses: Array,
        errors: Object,
        flash: Object
    });

    const processing = ref(false);

    // Form data initialized from order
    const formData = ref({
        shipping_address: props.order.shipping_address,
        payment_method: props.order.payment_method,
        status: props.order.status,
        payment_status: props.order.payment_status,
    });

    const updateOrder = (data) => {
        processing.value = true;
        router.put(route('dashboard.orders.update', props.order.uuid), {
            shipping_address: data.shipping_address,
            payment_method: data.payment_method,
            status: data.status,
            payment_status: data.payment_status,
        }, {
            onFinish: () => {
                processing.value = false;
            },
            preserveScroll: true
        });
    };
</script>