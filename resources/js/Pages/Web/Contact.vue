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

                        <v-form @submit.prevent="submitForm" v-model="isFormValid" ref="contactForm">
                            <v-row>
                                <v-col cols="12" sm="6">
                                    <v-text-field v-model="form.name" label="Your Name*" :rules="[rules.required]"
                                        variant="outlined" density="comfortable"
                                        :disabled="isSubmitting || isAuthenticated"></v-text-field>
                                </v-col>

                                <v-col cols="12" sm="6">
                                    <v-text-field v-model="form.email" label="Your Email*"
                                        :rules="[rules.required, rules.email]" variant="outlined" density="comfortable"
                                        :disabled="isSubmitting || isAuthenticated"></v-text-field>
                                </v-col>
                            </v-row>

                            <v-text-field v-model="form.subject" label="Subject*" :rules="[rules.required]"
                                variant="outlined" density="comfortable" class="mb-4"
                                :disabled="isSubmitting"></v-text-field>

                            <v-textarea v-model="form.message" label="Message*" :rules="[rules.required]"
                                variant="outlined" rows="5" class="mb-4" :disabled="isSubmitting"></v-textarea>

                            <v-file-input v-model="form.attachment" label="Attachment (optional)"
                                :error-messages="errors.attachment" prepend-icon="mdi-paperclip"
                                accept="image/*, application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                                variant="outlined" density="comfortable" class="mb-4"
                                :disabled="isSubmitting"></v-file-input>

                            <v-alert v-if="flash.success" type="success" class="mb-4">
                                {{ flash.success }}
                            </v-alert>

                            <v-alert v-if="flash.error || errors.error" type="error" class="mb-4">
                                {{ flash.error || errors.error }}
                            </v-alert>

                            <v-btn type="submit" color="primary" :loading="isSubmitting"
                                :disabled="!isFormValid || isSubmitting" block>
                                Send Message
                            </v-btn>

                            <div v-if="isAuthenticated && hasMessages" class="mt-4 text-center">
                                <Link :href="route('messages.index')" class="text-decoration-none">
                                <v-btn color="secondary" variant="text" prepend-icon="mdi-email-outline">
                                    View Your Message History
                                </v-btn>
                                </Link>
                            </div>
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
                                    {{ companyInfo.address }}<br>
                                    {{ companyInfo.state }}, {{ companyInfo.country }}
                                </v-list-item-subtitle>
                            </v-list-item>

                            <v-list-item>
                                <template v-slot:prepend>
                                    <v-icon color="primary" class="mr-3">mdi-phone</v-icon>
                                </template>
                                <v-list-item-title class="font-weight-bold">Phone</v-list-item-title>
                                <v-list-item-subtitle>
                                    {{ companyInfo.phone }}<br>
                                    {{ companyInfo.phoneSecondary }}
                                </v-list-item-subtitle>
                            </v-list-item>

                            <v-list-item>
                                <template v-slot:prepend>
                                    <v-icon color="primary" class="mr-3">mdi-email</v-icon>
                                </template>
                                <v-list-item-title class="font-weight-bold">Email</v-list-item-title>
                                <v-list-item-subtitle>
                                    {{ companyInfo.email }}<br>
                                    {{ companyInfo.emailSupport }}
                                </v-list-item-subtitle>
                            </v-list-item>

                            <v-list-item>
                                <template v-slot:prepend>
                                    <v-icon color="primary" class="mr-3">mdi-clock</v-icon>
                                </template>
                                <v-list-item-title class="font-weight-bold">Business Hours</v-list-item-title>
                                <v-list-item-subtitle>
                                    <span v-for="(hours, index) in formatBusinessHours(companyInfo.hours)" :key="index">
                                        {{ hours }}<br>
                                    </span>
                                </v-list-item-subtitle>
                            </v-list-item>
                        </v-list>
                    </v-card>

                    <v-card class="pa-4">
                        <h2 class="text-h5 mb-4">Connect With Us</h2>
                        <div class="d-flex gap-2">
                            <v-btn icon="mdi-facebook" color="primary" variant="text"
                                :href="companyInfo.social.facebook" target="_blank"></v-btn>
                            <v-btn icon="mdi-twitter" color="primary" variant="text" :href="companyInfo.social.twitter"
                                target="_blank"></v-btn>
                            <v-btn icon="mdi-instagram" color="primary" variant="text"
                                :href="companyInfo.social.instagram" target="_blank"></v-btn>
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
                        <v-sheet height="400" class="d-flex justify-center align-center bg-grey-lighten-3">
                            <div class="text-center">
                                <v-icon size="x-large" color="grey">mdi-map</v-icon>
                                <p class="mt-2 text-medium-emphasis">Map Goes Here</p>
                            </div>
                        </v-sheet>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </WebLayout>
</template>

<script setup>
    import WebLayout from '@/Layouts/WebLayout.vue';
    import { Head, Link, router } from '@inertiajs/vue3';
    import { ref, computed } from 'vue';

    defineProps({
        companyInfo: Object,
        errors: Object,
        flash: Object
    });

    const isFormValid = ref(false);
    const isSubmitting = ref(false);

    // Check if user is authenticated
    const isAuthenticated = computed(() => !!document.querySelector('meta[name="user-id"]'));
    const hasMessages = ref(false);

    // Form state
    const form = ref({
        name: '',
        email: '',
        subject: '',
        message: '',
        attachment: null
    });

    // Set user data if authenticated
    if (isAuthenticated.value) {
        // Get user info from meta tags
        const userName = document.querySelector('meta[name="user-name"]')?.content;
        const userEmail = document.querySelector('meta[name="user-email"]')?.content;

        if (userName) form.value.name = userName;
        if (userEmail) form.value.email = userEmail;

        // Check if user has messages
        if (document.querySelector('meta[name="user-id"]')?.content) {
            // You could make an API call here to check if the user has messages
            // For now we'll just set it to false until implemented
            hasMessages.value = false;
        }
    }

    // Form validation rules
    const rules = {
        required: value => !!value || 'This field is required',
        email: value => {
            const pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return pattern.test(value) || 'Invalid email address';
        }
    };

    const submitForm = () => {
        if (!isFormValid.value) return;

        isSubmitting.value = true;

        // Create FormData to handle file upload
        const formData = new FormData();
        formData.append('name', form.value.name);
        formData.append('email', form.value.email);
        formData.append('subject', form.value.subject);
        formData.append('message', form.value.message);

        if (form.value.attachment) {
            formData.append('attachment', form.value.attachment);
        }

        router.post(route('contact.store'), formData, {
            onSuccess: () => {
                // Reset form on success
                form.value = {
                    name: isAuthenticated.value ? form.value.name : '',
                    email: isAuthenticated.value ? form.value.email : '',
                    subject: '',
                    message: '',
                    attachment: null
                };

                // Check if user has messages after successful submission
                if (isAuthenticated.value) {
                    hasMessages.value = true;
                }
            },
            onError: () => {
                // Errors will be handled by the error display in the template
            },
            onFinish: () => {
                isSubmitting.value = false;
            }
        });
    };

    // Format business hours string into an array of lines
    const formatBusinessHours = (hoursString) => {
        return hoursString.split(';').map(hour => hour.trim());
    };
</script>
