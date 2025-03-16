<template>
    <form @submit.prevent="submitForm">
        <v-row>
            <!-- Basic Information Card -->
            <v-col cols="12" md="8">
                <v-card class="mb-4">
                    <v-card-title>
                        <v-icon class="mr-2">mdi-account-outline</v-icon>
                        Customer Information
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-card-text>
                        <!-- Customer Name -->
                        <v-text-field v-model="form.name" label="Full Name*" :error-messages="errors?.name" required
                            variant="outlined" density="comfortable"></v-text-field>

                        <!-- Email -->
                        <v-text-field v-model="form.email" label="Email Address*" :error-messages="errors?.email"
                            type="email" required variant="outlined" density="comfortable"></v-text-field>

                        <!-- Password -->
                        <v-text-field v-model="form.password" label="Password"
                            :placeholder="customer ? 'Leave blank to keep current password' : 'Password for the customer'"
                            :error-messages="errors?.password" :required="!customer" type="password" variant="outlined"
                            density="comfortable"></v-text-field>

                        <!-- Phone -->
                        <v-text-field v-model="form.phone" label="Phone Number" :error-messages="errors?.phone"
                            variant="outlined" density="comfortable"></v-text-field>

                        <!-- Address -->
                        <v-textarea v-model="form.address" label="Address" :error-messages="errors?.address" auto-grow
                            rows="3" variant="outlined" density="comfortable"></v-textarea>
                    </v-card-text>
                </v-card>
            </v-col>

            <!-- Submit Button Column -->
            <v-col cols="12" md="4">
                <v-card>
                    <v-card-title>
                        <v-icon class="mr-2">mdi-cog-outline</v-icon>
                        Actions
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-card-text>
                        <v-btn block color="primary" type="submit" :loading="processing" :disabled="processing">
                            {{ customer ? 'Update Customer' : 'Create Customer' }}
                        </v-btn>

                        <!-- Cancel Button -->
                        <Link :href="route('dashboard.customers.index')"
                            class="v-btn v-btn--block v-btn--text v-btn--secondary mt-3">
                        Cancel
                        </Link>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </form>
</template>

<script setup>
    import { ref, onMounted, watch } from 'vue';
    import { Link } from '@inertiajs/vue3';

    const props = defineProps({
        customer: {
            type: Object,
            default: null
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

    // Initialize form with default values
    const form = ref({
        name: '',
        email: '',
        password: '',
        phone: '',
        address: '',
    });

    // Watch for changes in the form and emit them
    watch(form, (newVal) => {
        emit('update:modelValue', { ...newVal });
    }, { deep: true });

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

        // Then initialize from customer if available (for edit mode)
        if (props.customer) {
            form.value.name = props.customer.name;
            form.value.email = props.customer.email;
            form.value.password = ''; // Don't populate password for security reasons
            form.value.phone = props.customer.phone || '';
            form.value.address = props.customer.address || '';
        }
    });

    // Validate and submit form
    const submitForm = () => {
        emit('submit', form.value);
    };
</script>
