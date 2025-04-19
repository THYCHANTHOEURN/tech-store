<template>
    <div class="invoice-container">
        <div class="invoice-actions d-print-none mb-4">
            <v-btn color="primary" prepend-icon="mdi-printer" @click="printInvoice" class="mr-2">Print Invoice</v-btn>
            <Link :href="route('dashboard.orders.show', order.uuid)">
            <v-btn color="secondary" prepend-icon="mdi-arrow-left" variant="outlined">Back to Order</v-btn>
            </Link>
        </div>

        <div class="invoice-content" ref="invoiceContent">
            <!-- Invoice Header -->
            <div class="invoice-header">
                <div class="company-info">
                    <h1 class="text-h4">{{ companyInfo.name }}</h1>
                    <p v-if="companyInfo.address">{{ companyInfo.address }}</p>
                    <p v-if="companyInfo.phone">Phone: {{ companyInfo.phone }}</p>
                    <p v-if="companyInfo.email">Email: {{ companyInfo.email }}</p>
                </div>
                <div class="invoice-details">
                    <h2 class="text-h5">INVOICE</h2>
                    <p class="invoice-id">Invoice #{{ order.uuid.slice(-8).toUpperCase() }}</p>
                    <p>Date: {{ formatDate(order.created_at) }}</p>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="customer-info">
                <div class="billing-info">
                    <h3 class="text-h6">Billed To:</h3>
                    <p><strong>{{ order.user.name }}</strong></p>
                    <p>{{ order.user.email }}</p>
                    <p>{{ order.phone }}</p>
                </div>
                <div class="shipping-info">
                    <h3 class="text-h6">Shipped To:</h3>
                    <p style="white-space: pre-line;">{{ order.shipping_address }}</p>
                </div>
            </div>

            <!-- Order Information -->
            <div class="order-info">
                <div class="info-row">
                    <div>
                        <p><strong>Payment Method:</strong></p>
                        <p>{{ formatPaymentMethod(order.payment_method) }}</p>
                    </div>
                    <div>
                        <p><strong>Order Status:</strong></p>
                        <p>{{ formatOrderStatus(order.status) }}</p>
                    </div>
                    <div>
                        <p><strong>Payment Status:</strong></p>
                        <p>{{ formatPaymentStatus(order.payment_status) }}</p>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="order-items">
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in order.order_items" :key="item.id">
                            <td>{{ item.product.name }}</td>
                            <td>${{ formatCurrency(item.price) }}</td>
                            <td>{{ item.quantity }}</td>
                            <td>${{ formatCurrency(item.price * item.quantity) }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-right"><strong>Total:</strong></td>
                            <td><strong>${{ formatCurrency(order.total_amount) }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Thank You Message -->
            <div class="thank-you">
                <p>Thank you for your business!</p>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>Invoice generated on {{ currentDate }}</p>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref, computed } from 'vue';
    import { Head, Link } from '@inertiajs/vue3';

    const props = defineProps({
        order: Object,
        companyInfo: Object
    });

    const invoiceContent = ref(null);

    // Current date for the footer
    const currentDate = computed(() => {
        return new Date().toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    });

    /**
     * Format currency
     *
     * @param {number} amount
     * @return {string}
     */
    function formatCurrency(amount) {
        return amount ? Number(amount).toFixed(2) : '0.00';
    }

    /**
     * Format date
     *
     * @param {string} dateString
     * @return {string}
     */
    function formatDate(dateString) {
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return new Date(dateString).toLocaleDateString('en-US', options);
    }

    /**
     * Format order status
     *
     * @param {string} status
     * @return {string}
     */
    function formatOrderStatus(status) {
        return status.charAt(0).toUpperCase() + status.slice(1);
    }

    /**
     * Format payment status
     *
     * @param {string} status
     * @return {string}
     */
    function formatPaymentStatus(status) {
        return status.charAt(0).toUpperCase() + status.slice(1);
    }

    /**
     * Format payment method
     *
     * @param {string} method
     * @return {string}
     */
    function formatPaymentMethod(method) {
        switch (method) {
            case 'cash': return 'Cash on Delivery';
            case 'card': return 'Credit/Debit Card';
            case 'bank_transfer': return 'Bank Transfer';
            default: return method;
        }
    }

    /**
     * Print the invoice
     */
    function printInvoice() {
        window.print();
    }
</script>

<style>

    /* General styles */
    .invoice-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        font-family: 'Roboto', sans-serif;
        color: #333;
    }

    .invoice-content {
        border: 1px solid #ddd;
        padding: 30px;
        background-color: white;
    }

    /* Header styles */
    .invoice-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
    }

    .invoice-id {
        font-weight: bold;
        font-size: 1.2em;
        color: #1976d2;
    }

    /* Customer info styles */
    .customer-info {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
    }

    .billing-info,
    .shipping-info {
        width: 48%;
    }

    /* Order info styles */
    .order-info {
        margin-bottom: 30px;
        padding: 15px;
        background-color: #f9f9f9;
        border-radius: 4px;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
    }

    /* Items table styles */
    .items-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
    }

    .items-table th,
    .items-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #ddd;
        text-align: left;
    }

    .items-table th {
        background-color: #f5f5f5;
    }

    .items-table tfoot td {
        font-weight: bold;
        border-top: 2px solid #ddd;
    }

    .text-right {
        text-align: right;
    }

    /* Thank you message */
    .thank-you {
        margin: 30px 0;
        font-size: 1.2em;
        text-align: center;
        color: #1976d2;
    }

    /* Footer */
    .footer {
        margin-top: 30px;
        padding-top: 15px;
        border-top: 1px solid #eee;
        font-size: 0.9em;
        color: #777;
        text-align: center;
    }

    /* Print styles */
    @media print {
        body {
            background-color: white;
        }

        .invoice-container {
            width: 100%;
            max-width: none;
            padding: 0;
            margin: 0;
        }

        .invoice-content {
            border: none;
            padding: 0;
        }

        .d-print-none {
            display: none !important;
        }
    }
</style>
