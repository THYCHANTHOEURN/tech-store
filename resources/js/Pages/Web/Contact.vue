<template>

    <Head title="Contact Us - Tech Store" />

    <WebLayout>
        <v-container class="py-10">
            <v-row>
                <v-col cols="12">
                    <h1 class="text-h3 font-weight-bold mb-6">Contact Us</h1>
                    <p class="text-body-1 mb-8">
                        We're here to help! Whether you have a question about our products, orders, or anything else,
                        our team is ready to answer all your questions.
                    </p>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12" md="6">
                    <v-card class="pa-4">
                        <h2 class="text-h5 mb-4">Send Us a Message</h2>

                        <v-form @submit.prevent="submitForm" v-model="isFormValid">
                            <v-row>
                                <v-col cols="12" sm="6">
                                    <v-text-field v-model="form.name" label="Your Name*" :rules="[rules.required]"
                                        variant="outlined" density="comfortable"></v-text-field>
                                </v-col>

                                <v-col cols="12" sm="6">
                                    <v-text-field v-model="form.email" label="Your Email*"
                                        :rules="[rules.required, rules.email]" variant="outlined"
                                        density="comfortable"></v-text-field>
                                </v-col>
                            </v-row>

                            <v-text-field v-model="form.subject" label="Subject*" :rules="[rules.required]"
                                variant="outlined" density="comfortable" class="mb-4"></v-text-field>

                            <v-textarea v-model="form.message" label="Message*" :rules="[rules.required]"
                                variant="outlined" rows="5" class="mb-4"></v-textarea>

                            <v-btn type="submit" color="primary" :loading="isSubmitting"
                                :disabled="!isFormValid || isSubmitting" block>
                                Send Message
                            </v-btn>
                        </v-form>
                    </v-card>
                </v-col>

                <v-col cols="12" md="6">
                    <v-card class="pa-4 mb-6">
                        <h2 class="text-h5 mb-4">Contact Information</h2>

                        <v-list>
                            <v-list-item>
                                <template v-slot:prepend>
                                    <v-icon color="primary" class="mr-3">mdi-map-marker</v-icon>
                                </template>
                                <v-list-item-title class="font-weight-bold">Address</v-list-item-title>
                                <v-list-item-subtitle>
                                    123 Tech Street, Innovation City<br>
                                    State 12345, Country
                                </v-list-item-subtitle>
                            </v-list-item>

                            <v-list-item>
                                <template v-slot:prepend>
                                    <v-icon color="primary" class="mr-3">mdi-phone</v-icon>
                                </template>
                                <v-list-item-title class="font-weight-bold">Phone</v-list-item-title>
                                <v-list-item-subtitle>
                                    +855 12 345 678<br>
                                    +855 98 765 432
                                </v-list-item-subtitle>
                            </v-list-item>

                            <v-list-item>
                                <template v-slot:prepend>
                                    <v-icon color="primary" class="mr-3">mdi-email</v-icon>
                                </template>
                                <v-list-item-title class="font-weight-bold">Email</v-list-item-title>
                                <v-list-item-subtitle>
                                    info@techstore.com<br>
                                    support@techstore.com
                                </v-list-item-subtitle>
                            </v-list-item>

                            <v-list-item>
                                <template v-slot:prepend>
                                    <v-icon color="primary" class="mr-3">mdi-clock</v-icon>
                                </template>
                                <v-list-item-title class="font-weight-bold">Business Hours</v-list-item-title>
                                <v-list-item-subtitle>
                                    Monday - Friday: 9:00 AM - 6:00 PM<br>
                                    Saturday: 10:00 AM - 4:00 PM<br>
                                    Sunday: Closed
                                </v-list-item-subtitle>
                            </v-list-item>
                        </v-list>
                    </v-card>

                    <v-card class="pa-4">
                        <h2 class="text-h5 mb-4">Connect With Us</h2>
                        <div class="d-flex gap-2">
                            <v-btn icon="mdi-facebook" color="primary" variant="text" href="https://www.facebook.com/"
                                target="_blank"></v-btn>
                            <v-btn icon="mdi-twitter" color="primary" variant="text" href="https://twitter.com/"
                                target="_blank"></v-btn>
                            <v-btn icon="mdi-instagram" color="primary" variant="text" href="https://www.instagram.com/"
                                target="_blank"></v-btn>
                            <v-btn icon="mdi-linkedin" color="primary" variant="text" href="https://www.linkedin.com/"
                                target="_blank"></v-btn>
                        </div>
                    </v-card>
                </v-col>
            </v-row>

            <v-row class="mt-10">
                <v-col cols="12">
                    <h2 class="text-h5 mb-4">Our Location</h2>
                    <v-card>
                        <!-- Placeholder for Google Map. In a real implementation, you would use Google Maps API -->
                        <v-img src="/images/map-placeholder.jpg" height="400" cover class="grey lighten-2">
                            <div class="d-flex justify-center align-center fill-height">
                                <v-btn color="primary" variant="elevated" href="https://maps.google.com"
                                    target="_blank">
                                    Open in Google Maps
                                </v-btn>
                            </div>
                        </v-img>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </WebLayout>
</template>

<script setup>
    import { Head } from '@inertiajs/vue3';
    import WebLayout from '@/Layouts/WebLayout.vue';
    import { ref } from 'vue';

    const isFormValid = ref(false);
    const isSubmitting = ref(false);

    const form = ref({
        name: '',
        email: '',
        subject: '',
        message: ''
    });

    const rules = {
        required: value => !!value || 'This field is required',
        email: value => {
            const pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return pattern.test(value) || 'Invalid email address';
        }
    };

    const submitForm = async () => {
        if (!isFormValid.value) return;

        isSubmitting.value = true;

        try {
            // In a real implementation, you would send this data to your backend
            // await axios.post('/api/contact', form.value);

            // For now, we'll just simulate a successful submission
            setTimeout(() => {
                alert('Thank you for your message! We will get back to you soon.');
                form.value = {
                    name: '',
                    email: '',
                    subject: '',
                    message: ''
                };
                isSubmitting.value = false;
            }, 1000);
        } catch (error) {
            console.error('Error submitting form:', error);
            alert('There was an error sending your message. Please try again later.');
            isSubmitting.value = false;
        }
    };
</script>
