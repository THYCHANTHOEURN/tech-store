<template>
    <form @submit.prevent="submitForm">
        <v-row>
            <!-- Main Content Column -->
            <v-col cols="12" md="8">
                <v-card>
                    <v-card-title>
                        <v-icon class="mr-2">mdi-shield-account</v-icon>
                        Role Information
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-card-text>
                        <!-- Role Name -->
                        <div class="mb-4">
                            <label class="text-subtitle-1 d-block mb-1">Role Name*</label>
                            <v-text-field v-model="form.name" :error-messages="errors?.name" hide-details="auto"
                                placeholder="Enter role name" variant="outlined" density="comfortable"></v-text-field>
                        </div>

                        <!-- Permissions Section -->
                        <div class="mb-4 mt-6">
                            <label class="text-subtitle-1 d-block mb-3">Permissions</label>

                            <div class="permissions-grid">
                                <v-checkbox v-for="permission in permissions" :key="permission.id"
                                    v-model="form.permissions" :label="formatPermissionName(permission.name)"
                                    :value="permission.id" density="comfortable" color="primary" hide-details
                                    class="mb-1">
                                </v-checkbox>
                            </div>

                            <p class="text-caption mt-2 text-grey">
                                Select the permissions to assign to this role.
                            </p>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>

            <!-- Settings Column -->
            <v-col cols="12" md="4">
                <v-card class="sticky-card">
                    <v-card-title>
                        <v-icon class="mr-2">mdi-cog-outline</v-icon>
                        Actions
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-card-text>
                        <!-- Save Button -->
                        <v-btn color="primary" type="submit" block size="large" :loading="processing">
                            {{ role ? 'Update Role' : 'Create Role' }}
                        </v-btn>
                        <!-- Cancel Button -->
                        <Link :href="route('dashboard.roles.index')"
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
    import { ref, watch, onMounted } from 'vue';

    const props = defineProps({
        modelValue: {
            type: Object,
            default: () => ({})
        },
        role: {
            type: Object,
            default: null
        },
        permissions: {
            type: Array,
            default: () => []
        },
        rolePermissions: {
            type: Array,
            default: () => []
        },
        processing: {
            type: Boolean,
            default: false
        },
        errors: {
            type: Object,
            default: null
        }
    });

    const emit = defineEmits(['update:modelValue', 'submit']);

    const form = ref({
        name: '',
        permissions: [],
    });

    // Watch for changes in the form and emit them
    watch(form, (newVal) => {
        emit('update:modelValue', newVal);
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

        // Then initialize from role if available (for edit mode)
        if (props.role) {
            form.value.name = props.role.name;
        }

        // Set permissions if available
        if (props.rolePermissions && props.rolePermissions.length) {
            form.value.permissions = [...props.rolePermissions];
        }
    });

    const formatPermissionName = (name) => {
        // Convert camelCase or snake_case to readable format
        return name
            .replace(/([A-Z])/g, ' $1') // Insert a space before all caps
            .replace(/_/g, ' ') // Replace underscores with spaces
            .replace(/\w\S*/g, (txt) => {
                return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
            }); // Capitalize first letter of each word
    };

    // Submit form
    const submitForm = () => {
        if (!form.value.name) {
            alert('Role name is required');
            return;
        }

        emit('submit', form.value);
    };
</script>

<style scoped>
    .permissions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 8px;
    }

    .sticky-card {
        position: sticky;
        top: 24px;
    }
</style>
