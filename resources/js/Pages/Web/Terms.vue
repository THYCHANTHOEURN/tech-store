<template>

    <Head title="Terms & Conditions - Tech Store" />

    <WebLayout>
        <v-container class="py-10">
            <v-row>
                <v-col cols="12">
                    <h1 class="text-h3 font-weight-bold mb-6">Terms & Conditions</h1>
                    <p class="text-body-1 mb-4">Last Updated: {{ formatDate(lastUpdated) }}</p>
                    <p class="text-body-1 mb-8">
                        Please read these Terms & Conditions carefully before using the Tech Store website and services.
                    </p>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12">
                    <v-card class="pa-6 mb-6">
                        <h2 class="text-h5 mb-4">1. Agreement to Terms</h2>
                        <p class="text-body-1 mb-4">
                            By accessing or using our website, you agree to be bound by these Terms & Conditions and our
                            Privacy Policy.
                            If you disagree with any part of the terms, then you may not access our services.
                        </p>
                    </v-card>

                    <v-card class="pa-6 mb-6">
                        <h2 class="text-h5 mb-4">2. Intellectual Property</h2>
                        <p class="text-body-1 mb-4">
                            The Tech Store website and its original content, features, and functionality are owned by
                            Tech Store and are
                            protected by international copyright, trademark, patent, trade secret, and other
                            intellectual property
                            laws.
                        </p>
                    </v-card>

                    <v-card class="pa-6 mb-6">
                        <h2 class="text-h5 mb-4">3. User Accounts</h2>
                        <p class="text-body-1 mb-2">
                            When you create an account with us, you must provide accurate, complete, and up-to-date
                            information. Failure
                            to do so constitutes a breach of the Terms, which may result in immediate termination of
                            your account.
                        </p>
                        <p class="text-body-1 mb-2">
                            You are responsible for safeguarding the password you use to access our service and for any
                            activities or
                            actions under your password.
                        </p>
                        <p class="text-body-1">
                            You agree not to disclose your password to any third party. You must notify us immediately
                            upon becoming
                            aware of any breach of security or unauthorized use of your account.
                        </p>
                    </v-card>

                    <v-card class="pa-6 mb-6">
                        <h2 class="text-h5 mb-4">4. Products and Pricing</h2>
                        <p class="text-body-1 mb-2">
                            We strive to display accurate product information and pricing on our website. However, we do
                            not warrant
                            that product descriptions, pricing, or other content is accurate, complete, reliable,
                            current, or error-free.
                        </p>
                        <p class="text-body-1 mb-2">
                            In the event of a pricing error, we reserve the right to refuse or cancel any orders placed
                            for products
                            listed at the incorrect price. We will contact you if this occurs.
                        </p>
                        <p class="text-body-1">
                            Prices for our products are subject to change without notice. We reserve the right to modify
                            or discontinue
                            any product without notice at any time.
                        </p>
                    </v-card>

                    <v-card class="pa-6 mb-6">
                        <h2 class="text-h5 mb-4">5. Orders and Payments</h2>
                        <p class="text-body-1 mb-2">
                            All orders are subject to acceptance and availability. We reserve the right to refuse any
                            order you place
                            with us.
                        </p>
                        <p class="text-body-1 mb-2">
                            Payment must be received prior to the acceptance of an order. Once payment has been
                            received, an order
                            confirmation will be sent to you by email.
                        </p>
                        <p class="text-body-1">
                            We accept various forms of payment, including credit cards, debit cards, and other
                            electronic payments.
                            All credit/debit card holders are subject to validation checks and authorization by the card
                            issuer.
                        </p>
                    </v-card>

                    <v-card class="pa-6 mb-6">
                        <h2 class="text-h5 mb-4">6. Shipping and Delivery</h2>
                        <p class="text-body-1 mb-2">
                            Delivery times are estimates only and commence from the date of shipping, not the date of
                            order.
                        </p>
                        <p class="text-body-1 mb-2">
                            We are not responsible for any delays caused by destination customs clearance processes,
                            local duties or
                            taxes, or any other circumstances beyond our control.
                        </p>
                        <p class="text-body-1">
                            Risk of loss and title for items purchased from our website pass to you upon delivery of the
                            items to the
                            carrier.
                        </p>
                    </v-card>

                    <v-card class="pa-6 mb-6">
                        <h2 class="text-h5 mb-4">7. Returns and Refunds</h2>
                        <p class="text-body-1 mb-2">
                            You have the right to return items within 30 days of delivery for a full refund, provided
                            the products are in
                            their original condition and packaging.
                        </p>
                        <p class="text-body-1 mb-2">
                            To initiate a return, please contact our customer service department with your order number
                            and details about
                            the product you would like to return.
                        </p>
                        <p class="text-body-1">
                            Some items are non-returnable for hygiene reasons or due to their nature. These will be
                            clearly marked on the
                            product page.
                        </p>
                    </v-card>

                    <v-card class="pa-6 mb-6">
                        <h2 class="text-h5 mb-4">8. Warranty</h2>
                        <p class="text-body-1 mb-2">
                            All products sold by Tech Store come with a manufacturer's warranty. The length and terms of
                            the warranty
                            vary by product and manufacturer.
                        </p>
                        <p class="text-body-1">
                            For warranty claims, please contact our customer service with your order details and a
                            description of the
                            defect.
                        </p>
                    </v-card>

                    <v-card class="pa-6 mb-6">
                        <h2 class="text-h5 mb-4">9. Limitation of Liability</h2>
                        <p class="text-body-1 mb-2">
                            In no event shall Tech Store, nor its directors, employees, partners, agents, suppliers, or
                            affiliates, be
                            liable for any indirect, incidental, special, consequential, or punitive damages, including
                            without
                            limitation, loss of profits, data, use, goodwill, or other intangible losses, resulting
                            from:
                        </p>
                        <ul class="text-body-1 mb-2 pl-6">
                            <li>Your access to or use of or inability to access or use our service;</li>
                            <li>Any conduct or content of any third party on our service;</li>
                            <li>Any content obtained from our service; and</li>
                            <li>Unauthorized access, use, or alteration of your transmissions or content.</li>
                        </ul>
                    </v-card>

                    <v-card class="pa-6 mb-6">
                        <h2 class="text-h5 mb-4">10. Changes to Terms</h2>
                        <p class="text-body-1 mb-2">
                            We reserve the right, at our sole discretion, to modify or replace these Terms at any time.
                            If a revision is
                            material, we will try to provide at least 30 days' notice prior to any new terms taking
                            effect.
                        </p>
                        <p class="text-body-1">
                            By continuing to access or use our service after those revisions become effective, you agree
                            to be bound by
                            the revised terms. If you do not agree to the new terms, please stop using our service.
                        </p>
                    </v-card>

                    <v-card class="pa-6">
                        <h2 class="text-h5 mb-4">11. Contact Us</h2>
                        <p class="text-body-1">
                            If you have any questions about these Terms, please contact us at:
                        </p>
                        <p class="text-body-1">
                            <strong>Email:</strong> legal@techstore.com<br>
                            <strong>Phone:</strong> +855 12 345 678<br>
                            <strong>Address:</strong> 123 Tech Street, Innovation City, State 12345, Country
                        </p>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </WebLayout>
</template>

<script setup>
    import { Head } from '@inertiajs/vue3';
    import WebLayout from '@/Layouts/WebLayout.vue';

    // Last updated date for the terms & conditions
    const lastUpdated = new Date(2023, 11, 15); // December 15, 2023

    // Format date helper function
    const formatDate = (date) => {
        return new Date(date).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    };
</script>
