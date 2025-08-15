<template>
    <form @submit.prevent="submitForm">
        <v-row>
            <!-- Main Content Column -->
            <v-col cols="12" md="8">
                <v-card class="mb-4">
                    <v-card-title>
                        <v-icon class="mr-2">mdi-account-outline</v-icon>
                        {{ $t('Customer Information') }}
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-card-text>
                        <!-- Customer Selector -->
                        <div class="mb-4">
                            <label class="text-subtitle-1 d-block mb-1">{{ $t('Customer') }}*</label>
                            <v-select v-model="form.user_id" :items="users" item-title="name" item-value="id"
                                :error-messages="errors?.user_id" hide-details="auto" variant="outlined"
                                density="comfortable" :disabled="!!order" :readonly="!!order">
                                <template v-slot:item="{ props, item }">
                                    <v-list-item v-bind="props">
                                        <v-list-item-subtitle>{{ item.raw.email }}</v-list-item-subtitle>
                                    </v-list-item>
                                </template>
                            </v-select>
                        </div>

                        <!-- Shipping Address -->
                        <div class="mb-4">
                            <label class="text-subtitle-1 d-block mb-1">{{ $t('Shipping Address') }}*</label>
                            <v-textarea v-model="form.shipping_address" :error-messages="errors?.shipping_address"
                                hide-details="auto" placeholder="Enter shipping address" variant="outlined"
                                density="comfortable" rows="3"></v-textarea>
                        </div>

                        <!-- Phone Number -->
                        <div class="mb-4">
                            <label class="text-subtitle-1 d-block mb-1">{{ $t('Phone Number') }}*</label>
                            <v-text-field v-model="form.phone" :error-messages="errors?.phone" hide-details="auto"
                                placeholder="Enter phone number" variant="outlined" density="comfortable"
                                prepend-inner-icon="mdi-phone"></v-text-field>
                        </div>
                    </v-card-text>
                </v-card>

                <template v-if="!order">
                    <v-card class="mb-4">
                        <v-card-title class="d-flex align-center">
                            <v-icon class="mr-2">mdi-cart-outline</v-icon>
                            {{ $t('Order Items') }}
                            <v-spacer></v-spacer>
                            <v-btn color="primary" size="small" @click="addItem" prepend-icon="mdi-plus">
                                {{ $t('Add Item') }}
                            </v-btn>
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <div v-if="form.items.length === 0" class="text-center pa-4">
                                <p>No items added to the order yet.</p>
                                <v-btn color="primary" variant="text" @click="addItem">Add Item</v-btn>
                            </div>

                            <template v-for="(item, index) in form.items" :key="index">
                                <div class="d-flex align-center item-row mb-4">
                                    <div class="flex-grow-1">
                                        <v-select v-model="item.product_id" :items="availableProducts" item-title="name"
                                            item-value="id" :label="$t('Product')" variant="outlined" density="comfortable"
                                            @update:model-value="updateItemPrice(index)">
                                            <template v-slot:item="{ props, item }">
                                                <v-list-item v-bind="props" :title="item.raw.name">
                                                    <template v-slot:prepend>
                                                        <v-img :src="item.raw.primary_image_url" width="40" height="40"
                                                            class="rounded mr-2"></v-img>
                                                    </template>
                                                    <template v-slot:subtitle>
                                                        <div class="d-flex">
                                                            <span class="text-primary">${{ item.raw.sale_price ||
                                                                item.raw.price }}</span>
                                                            <span class="ml-2 text-grey">Stock: {{ item.raw.stock
                                                                }}</span>
                                                        </div>
                                                    </template>
                                                </v-list-item>
                                            </template>
                                        </v-select>
                                    </div>

                                    <div class="mx-2" style="width: 100px;">
                                        <v-text-field v-model.number="item.quantity" type="number" label="Qty" min="1"
                                            variant="outlined" density="comfortable"
                                            @update:model-value="updateSubtotal"></v-text-field>
                                    </div>

                                    <div class="mx-2" style="width: 120px;">
                                        <v-text-field v-model.number="item.price" type="number" label="Price" min="0"
                                            variant="outlined" density="comfortable"
                                            @update:model-value="updateSubtotal"></v-text-field>
                                    </div>

                                    <div class="mx-2 text-right" style="width: 100px;">
                                        ${{ formatCurrency(item.price * item.quantity) }}
                                    </div>

                                    <div class="ml-2">
                                        <v-btn icon color="error" size="x-small" @click="removeItem(index)"
                                            variant="text">
                                            <v-icon>mdi-delete</v-icon>
                                        </v-btn>
                                    </div>
                                </div>
                            </template>

                            <v-divider class="my-4" v-if="form.items.length > 0"></v-divider>

                            <!-- Order Summary -->
                            <div class="text-right" v-if="form.items.length > 0">
                                <p class="text-h6">Total: ${{ calculateTotal }}</p>
                            </div>
                        </v-card-text>
                    </v-card>
                </template>

                <template v-else>
                    <v-card class="mb-4">
                        <v-card-title>
                            <v-icon class="mr-2">mdi-cart-outline</v-icon>
                            {{ $t('Order Items') }} ({{ order.order_items?.length || 0 }})
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <v-table>
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-right">Price</th>
                                        <th class="text-right">Qty</th>
                                        <th class="text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in order.order_items" :key="item.id">
                                        <td>
                                            <div class="d-flex align-center">
                                                <v-img :src="item.product?.primary_image_url" width="40" height="40"
                                                    class="rounded mr-2"></v-img>
                                                <div>
                                                    {{ item.product?.name }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-right">${{ formatCurrency(item.price) }}</td>
                                        <td class="text-right">{{ item.quantity }}</td>
                                        <td class="text-right">${{ formatCurrency(item.price * item.quantity) }}</td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" class="text-right font-weight-bold">Total:</td>
                                        <td class="text-right">${{ formatCurrency(order.total_amount) }}</td>
                                    </tr>
                                </tfoot>
                            </v-table>
                        </v-card-text>
                    </v-card>
                </template>
            </v-col>

            <!-- Order Settings Card -->
            <v-col cols="12" md="4">
                <v-card class="sticky-card">
                    <v-card-title>
                        <v-icon class="mr-2">mdi-cog-outline</v-icon>
                        {{ $t('Order Settings') }}
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-card-text>
                        <!-- Payment Method -->
                        <div class="mb-4">
                            <label class="text-subtitle-1 d-block mb-1">{{ $t('Payment Method') }}*</label>
                            <v-select v-model="form.payment_method" :items="paymentMethods" item-title="title"
                                item-value="value" :error-messages="errors?.payment_method" hide-details="auto"
                                variant="outlined" density="comfortable"></v-select>
                        </div>

                        <!-- Order Status -->
                        <div class="mb-4">
                            <label class="text-subtitle-1 d-block mb-1">{{ $t('Order Status') }}*</label>
                            <v-select v-model="form.status" :items="orderStatuses" item-title="label" item-value="value"
                                :error-messages="errors?.status" hide-details="auto" variant="outlined"
                                density="comfortable"></v-select>
                        </div>

                        <!-- Payment Status -->
                        <div class="mb-4">
                            <label class="text-subtitle-1 d-block mb-1">{{ $t('Payment Status') }}*</label>
                            <v-select v-model="form.payment_status" :items="paymentStatuses" item-title="label"
                                item-value="value" :error-messages="errors?.payment_status" hide-details="auto"
                                variant="outlined" density="comfortable"></v-select>
                        </div>

                        <v-divider class="my-4"></v-divider>

                        <!-- Save Button -->
                        <v-btn color="primary" type="submit" block size="large" :loading="processing">
                            {{ order ? $t('Update Order') : $t('Create Order') }}
                        </v-btn>

                        <!-- Cancel Button -->
                        <Link :href="route('dashboard.orders.index')"
                            class="v-btn v-btn--block v-btn--text v-btn--secondary mt-3">
                        {{ $t('Cancel') }}
                        </Link>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </form>
</template>

<script setup>
    import { ref, computed, onMounted, watch } from 'vue';
    import { Link } from '@inertiajs/vue3';

    const props = defineProps({
        order: {
            type: Object,
            default: null
        },
        users: {
            type: Array,
            required: true
        },
        products: {
            type: Array,
            required: true
        },
        orderStatuses: {
            type: Array,
            required: true
        },
        paymentStatuses: {
            type: Array,
            required: true
        },
        errors: {
            type: Object,
            default: () => ({})
        },
        processing: {
            type: Boolean,
            default: false
        },
        modelValue: {
            type: Object,
            default: () => ({})
        }
    });

    const emit = defineEmits(['submit', 'update:modelValue']);

    // Payment method options
    const paymentMethods = [
        { title: 'Cash on Delivery', value: 'cash' },
        { title: 'Credit Card', value: 'card' },
        { title: 'Bank Transfer', value: 'bank_transfer' },
    ];

    // Initialize form with default values
    const form = ref({
        user_id: null,
        shipping_address: '',
        phone: '',
        payment_method: 'cash',
        status: 'pending',
        payment_status: 'unpaid',
        items: []
    });

    // Order items management
    const addItem = () => {
        form.value.items.push({
            product_id: null,
            quantity: 1,
            price: 0
        });
    };

    const removeItem = (index) => {
        form.value.items.splice(index, 1);
        updateSubtotal();
    };

    const updateItemPrice = (index) => {
        const item = form.value.items[index];
        const product = props.products.find(p => p.id === item.product_id);

        if (product) {
            // Set price to sale price if available, otherwise to regular price
            item.price = product.sale_price || product.price;
            updateSubtotal();
        }
    };

    const updateSubtotal = () => {
        // Recalculate total and emit changes
        emitFormChanges();
    };

    // Filter out products that are already in the order to avoid duplicates
    const availableProducts = computed(() => {
        return props.products.filter(product => {
            // If not in stock, don't show
            if (product.stock <= 0) return false;

            // Allow selecting a product again if we're editing its quantity
            return true;
        });
    });

    // Calculate order total
    const calculateTotal = computed(() => {
        return form.value.items.reduce((total, item) => {
            return total + (item.price || 0) * (item.quantity || 0);
        }, 0).toFixed(2);
    });

    // Format currency
    const formatCurrency = (amount) => {
        return amount ? Number(amount).toFixed(2) : '0.00';
    };

    // Set initial form values on component mount
    onMounted(() => {
        // Initialize from props.modelValue first
        if (props.modelValue && Object.keys(props.modelValue).length) {
            Object.keys(form.value).forEach(key => {
                if (props.modelValue[key] !== undefined) {
                    form.value[key] = props.modelValue[key];
                }
            });
        }

        // Then initialize from order if available (for edit mode)
        if (props.order) {
            form.value.user_id = props.order.user_id;
            form.value.shipping_address = props.order.shipping_address || '';
            form.value.phone = props.order.phone || '';
            form.value.payment_method = props.order.payment_method;
            form.value.status = props.order.status;
            form.value.payment_status = props.order.payment_status;
        }

        // Emit initial form data
        emitFormChanges();
    });

    // Emit changes to the parent component
    const emitFormChanges = () => {
        emit('update:modelValue', { ...form.value });
    };

    // Validate and submit form
    const submitForm = () => {
        // Basic validation
        if (!form.value.user_id && !props.order) {
            alert('Please select a customer');
            return;
        }

        if (!form.value.shipping_address) {
            alert('Shipping address is required');
            return;
        }

        if (!form.value.items.length && !props.order) {
            alert('Please add at least one item to the order');
            return;
        }

        for (const item of form.value.items) {
            if (!item.product_id) {
                alert('Please select a product for all items');
                return;
            }
        }

        // Emit the submit event with form data
        emit('submit', { ...form.value });
    };
</script>

<style scoped>
    .sticky-card {
        position: sticky;
        top: 24px;
    }

    .item-row {
        border: 1px solid rgba(0, 0, 0, 0.12);
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 8px;
    }
</style>
