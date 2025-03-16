<template>
    <form @submit.prevent="submitForm">
        <v-row>
            <!-- Basic Information Card -->
            <v-col cols="12" md="8">
                <v-card class="mb-4">
                    <v-card-title>
                        <v-icon class="mr-2">mdi-account-outline</v-icon>
                        User Information
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-card-text>
                        <!-- Name & Email -->
                        <v-row>
                            <v-col cols="12" md="6">
                                <div class="mb-4">
                                    <label class="text-subtitle-1 d-block mb-1">Name*</label>
                                    <v-text-field v-model="form.name" :error-messages="errors.name" hide-details="auto"
                                        placeholder="Enter user name" variant="outlined"
                                        density="comfortable"></v-text-field>
                                </div>
                            </v-col>
                            <v-col cols="12" md="6">
                                <div class="mb-4">
                                    <label class="text-subtitle-1 d-block mb-1">Email Address*</label>
                                    <v-text-field v-model="form.email" type="email" :error-messages="errors.email"
                                        hide-details="auto" placeholder="Enter email address" variant="outlined"
                                        density="comfortable"></v-text-field>
                                </div>
                            </v-col>
                        </v-row>

                        <!-- Password -->
                        <div class="mb-4">
                            <label class="text-subtitle-1 d-block mb-1">Password {{ user ? '' : '*' }}</label>
                            <v-text-field v-model="form.password" type="password" :error-messages="errors.password"
                                hide-details="auto"
                                :placeholder="user ? 'Leave blank to keep current password' : 'Enter password'"
                                variant="outlined" density="comfortable"></v-text-field>
                            <p class="text-caption mt-1" v-if="user">Leave blank to keep the current password</p>
                        </div>

                        <!-- Phone -->
                        <div class="mb-4">
                            <label class="text-subtitle-1 d-block mb-1">Phone Number</label>
                            <v-text-field v-model="form.phone" :error-messages="errors.phone" hide-details="auto"
                                placeholder="Enter phone number" variant="outlined"
                                density="comfortable"></v-text-field>
                        </div>

                        <!-- Address -->
                        <div class="mb-4">
                            <label class="text-subtitle-1 d-block mb-1">Address</label>
                            <v-textarea v-model="form.address" :error-messages="errors.address" hide-details="auto"
                                placeholder="Enter address" variant="outlined" density="comfortable"
                                rows="3"></v-textarea>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>

            <!-- User Settings Card -->
            <v-col cols="12" md="4">
                <v-card class="sticky-card">
                    <v-card-title>
                        <v-icon class="mr-2">mdi-cog-outline</v-icon>
                        User Settings
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-card-text>
                        <!-- Role -->
                        <div class="mb-4">
                            <label class="text-subtitle-1 d-block mb-1">Role*</label>
                            <v-select v-model="form.role" :items="roles" item-title="name" item-value="name"
                                :error-messages="errors.role" hide-details="auto" variant="outlined"
                                density="comfortable">
                            </v-select>
                            <p class="text-caption mt-1">User permissions are determined by their role</p>
                        </div>

                        <v-divider class="my-4"></v-divider>

                        <!-- Save Button -->
                        <v-btn color="primary" type="submit" block size="large" :loading="processing">
                            {{ user ? 'Update User' : 'Create User' }}
                        </v-btn>

                        <!-- Cancel Button -->
                        <v-btn variant="text" block class="mt-3">
                            Cancel
                        </v-btn>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </form>
</template>

<script setup>
    import { ref, watch, onMounted } from 'vue';

    const props = defineProps({
        modelValue: Object,
        user: Object,
        roles: Array,
        processing: Boolean,
        errors: Object,
    });

    const emit = defineEmits(['update:modelValue', 'submit']);

    const form = ref({
        name: '',
        email: '',
        password: '',
        phone: '',
        address: '',
        role: '',
    });

    // Watch for changes in the form and emit them to the parent
    watch(form.value, () => {
        emitFormChanges();
    }, { deep: true });

    // Watch for changes in the modelValue prop
    watch(() => props.modelValue, (newVal) => {
        if (newVal) {
            Object.keys(form.value).forEach(key => {
                if (newVal[key] !== undefined) {
                    form.value[key] = newVal[key];
                }
            });
        }
    }, { deep: true });

    onMounted(() => {
        // Initialize form data from props
        if (props.modelValue && Object.keys(props.modelValue).length) {
            Object.keys(form.value).forEach(key => {
                if (props.modelValue[key] !== undefined) {
                    form.value[key] = props.modelValue[key];
                }
            });
        }

        // For edit mode, use existing user data
        if (props.user) {
            form.value.name = props.user.name;
            form.value.email = props.user.email;
            form.value.password = ''; // Don't populate password for security
            form.value.phone = props.user.phone || '';
            form.value.address = props.user.address || '';
            form.value.role = props.user.role || '';
        }

        // Emit initial form data
        emitFormChanges();
    });

    // Emit changes to the parent component
    const emitFormChanges = () => {
        emit('update:modelValue', { ...form.value });
    };

    // Submit the form
    const submitForm = () => {
        emit('submit', form.value);
    };
</script>

<style scoped>
    .sticky-card {
        position: sticky;
        top: 24px;
    }
</style>
