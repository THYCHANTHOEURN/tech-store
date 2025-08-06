<template>
    <form @submit.prevent="submitForm">
        <v-row>
            <!-- Main Content Column -->
            <v-col cols="12" md="8">
                <v-card>
                    <v-card-title class="d-flex align-center bg-primary text-white">
                        <v-icon class="mr-2" color="white">mdi-shield-account</v-icon>
                        Role Information
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-card-text class="pt-4">
                        <!-- Role Name -->
                        <div class="mb-6">
                            <label class="text-subtitle-1 font-weight-bold d-block mb-2">Role Name*</label>
                            <v-text-field
                                v-model="form.name"
                                :error-messages="errors?.name"
                                hide-details="auto"
                                placeholder="Enter role name"
                                variant="outlined"
                                density="comfortable"
                                class="rounded-lg"
                                bg-color="grey-lighten-4"
                            ></v-text-field>
                        </div>

                        <!-- Permissions Section -->
                        <div class="mt-8">
                            <div class="d-flex align-center mb-4">
                                <h3 class="text-h6 font-weight-bold">Permissions</h3>
                                <v-spacer></v-spacer>

                                <!-- View Mode Toggle -->
                                <v-btn-toggle v-model="viewMode" color="primary" density="compact" rounded="lg">
                                    <v-btn value="tabs" variant="text">
                                        <v-icon>mdi-tab</v-icon>
                                        <span class="ml-1 d-none d-sm-inline">Tabs</span>
                                    </v-btn>
                                    <v-btn value="list" variant="text">
                                        <v-icon>mdi-format-list-bulleted</v-icon>
                                        <span class="ml-1 d-none d-sm-inline">List</span>
                                    </v-btn>
                                </v-btn-toggle>
                            </div>

                            <div class="permission-container">
                                <v-card flat border class="rounded-lg mb-4">
                                    <!-- Tab Navigation (always visible) -->
                                    <v-tabs
                                        v-model="activePermissionTab"
                                        slider-color="primary"
                                        bg-color="grey-lighten-4"
                                        class="rounded-t"
                                        show-arrows
                                        density="comfortable"
                                    >
                                        <v-tab
                                            v-for="(group, groupName) in permissionGroups"
                                            :key="groupName"
                                            :value="groupName"
                                            class="text-capitalize"
                                        >
                                            <v-icon start class="mr-1">{{ getGroupIcon(groupName) }}</v-icon>
                                            {{ formatSimpleGroupName(groupName) }}
                                            <v-badge
                                                :content="group.length"
                                                color="primary"
                                                inline
                                                class="ml-1"
                                            ></v-badge>
                                        </v-tab>
                                    </v-tabs>

                                    <v-divider></v-divider>

                                    <!-- Tab View (shown when viewMode is "tabs") -->
                                    <v-window v-model="activePermissionTab" v-if="viewMode === 'tabs'" class="permission-panels-container">
                                        <v-window-item
                                            v-for="(group, groupName) in permissionGroups"
                                            :key="`tab-${groupName}`"
                                            :value="groupName"
                                        >
                                            <div class="permission-group pa-4">
                                                <div class="permission-group-header d-flex align-center mb-4 pb-2 border-bottom">
                                                    <div class="d-flex align-center">
                                                        <v-icon :icon="getGroupIcon(groupName)" class="mr-2" color="primary" size="large"></v-icon>
                                                        <div>
                                                            <h3 class="text-h6 font-weight-bold mb-1">
                                                                {{ formatGroupName(groupName) }}
                                                            </h3>
                                                            <p class="text-caption text-grey">
                                                                Manage {{ formatSimpleGroupName(groupName).toLowerCase() }} permissions
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <v-btn
                                                        color="primary"
                                                        size="small"
                                                        variant="flat"
                                                        :prepend-icon="isAllGroupSelected(groupName) ? 'mdi-close' : 'mdi-check-all'"
                                                        @click="toggleGroupPermissions(groupName)"
                                                        class="ml-auto"
                                                    >
                                                        {{ isAllGroupSelected(groupName) ? 'Deselect All' : 'Select All' }}
                                                    </v-btn>
                                                </div>

                                                <div class="permission-cards">
                                                    <v-card
                                                        v-for="permission in group"
                                                        :key="`tab-perm-${permission.id}`"
                                                        :color="isPermissionSelected(permission.id) ? 'primary' : undefined"
                                                        :class="{
                                                            'selected': isPermissionSelected(permission.id),
                                                            'elevation-1': !isPermissionSelected(permission.id),
                                                            'elevation-3': isPermissionSelected(permission.id)
                                                        }"
                                                        class="permission-card"
                                                        @click="togglePermission(permission.id)"
                                                        rounded="lg"
                                                        variant="elevated"
                                                    >
                                                        <v-card-text class="pa-3 d-flex align-center">
                                                            <div>
                                                                <div :class="{ 'text-white': isPermissionSelected(permission.id) }">
                                                                    <strong>{{ formatActionName(permission.name) }}</strong>
                                                                </div>
                                                                <div class="text-caption" :class="{ 'text-white': isPermissionSelected(permission.id) }">
                                                                    {{ getPermissionDescription(permission.name) }}
                                                                </div>
                                                            </div>
                                                            <v-icon
                                                                size="small"
                                                                :color="isPermissionSelected(permission.id) ? 'white' : 'grey'"
                                                                class="ml-auto"
                                                            >
                                                                {{ isPermissionSelected(permission.id) ? 'mdi-check-circle' : 'mdi-checkbox-blank-circle-outline' }}
                                                            </v-icon>
                                                        </v-card-text>
                                                    </v-card>
                                                </div>
                                            </div>
                                        </v-window-item>
                                    </v-window>

                                    <!-- List View (shown when viewMode is "list") -->
                                    <v-card-text v-else class="permission-panels-container pt-4">
                                        <div v-for="(group, groupName) in permissionGroups" :key="`list-${groupName}`" class="permission-group mb-8 pa-4">
                                            <div class="permission-group-header d-flex align-center mb-3 pb-2 border-bottom">
                                                <div class="d-flex align-center">
                                                    <v-icon :icon="getGroupIcon(groupName)" class="mr-2" color="primary" size="large"></v-icon>
                                                    <div>
                                                        <h3 class="text-h6 font-weight-bold mb-1">
                                                            {{ formatGroupName(groupName) }}
                                                        </h3>
                                                        <p class="text-caption text-grey">
                                                            Manage {{ formatSimpleGroupName(groupName).toLowerCase() }} permissions
                                                            <v-chip color="primary" size="x-small" class="ml-2">
                                                                {{ group.length }} permissions
                                                            </v-chip>
                                                        </p>
                                                    </div>
                                                </div>
                                                <v-btn
                                                    color="primary"
                                                    size="small"
                                                    variant="flat"
                                                    :prepend-icon="isAllGroupSelected(groupName) ? 'mdi-close' : 'mdi-check-all'"
                                                    @click="toggleGroupPermissions(groupName)"
                                                    class="ml-auto"
                                                >
                                                    {{ isAllGroupSelected(groupName) ? 'Deselect All' : 'Select All' }}
                                                </v-btn>
                                            </div>

                                            <div class="permission-cards">
                                                <v-card
                                                    v-for="permission in group"
                                                    :key="`list-perm-${permission.id}`"
                                                    :color="isPermissionSelected(permission.id) ? 'primary' : undefined"
                                                    :class="{
                                                        'selected': isPermissionSelected(permission.id),
                                                        'elevation-1': !isPermissionSelected(permission.id),
                                                        'elevation-3': isPermissionSelected(permission.id)
                                                    }"
                                                    class="permission-card"
                                                    @click="togglePermission(permission.id)"
                                                    rounded="lg"
                                                    variant="elevated"
                                                >
                                                    <v-card-text class="pa-3 d-flex align-center">
                                                        <div>
                                                            <div :class="{ 'text-white': isPermissionSelected(permission.id) }">
                                                                <strong>{{ formatActionName(permission.name) }}</strong>
                                                            </div>
                                                            <div class="text-caption" :class="{ 'text-white': isPermissionSelected(permission.id) }">
                                                                {{ getPermissionDescription(permission.name) }}
                                                            </div>
                                                        </div>
                                                        <v-icon
                                                            size="small"
                                                            :color="isPermissionSelected(permission.id) ? 'white' : 'grey'"
                                                            class="ml-auto"
                                                        >
                                                            {{ isPermissionSelected(permission.id) ? 'mdi-check-circle' : 'mdi-checkbox-blank-circle-outline' }}
                                                        </v-icon>
                                                    </v-card-text>
                                                </v-card>
                                            </div>
                                        </div>
                                    </v-card-text>
                                </v-card>
                            </div>

                            <p class="text-caption mt-3 text-grey-darken-1">
                                Click on cards to select permissions for this role.
                                Use the toggle above to switch between tab and list view.
                            </p>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>

            <!-- Settings Column -->
            <v-col cols="12" md="4">
                <v-card class="sticky-card">
                    <v-card-title class="bg-grey-lighten-3">
                        <v-icon class="mr-2">mdi-cog-outline</v-icon>
                        Actions
                    </v-card-title>
                    <v-divider></v-divider>
                    <v-card-text class="pt-4">
                        <!-- Save Button -->
                        <v-btn color="primary" type="submit" block size="large" :loading="processing" variant="elevated"
                            class="mb-3">
                            {{ role ? 'Update Role' : 'Create Role' }}
                        </v-btn>

                        <!-- Cancel Button -->
                        <Link :href="route('dashboard.roles.index')">
                        <v-btn block variant="outlined" color="grey" class="mt-3" prepend-icon="mdi-arrow-left">
                            Back to Roles List
                        </v-btn>
                        </Link>

                        <div class="mt-6 pa-3 bg-grey-lighten-4 rounded">
                            <h4 class="text-subtitle-2 font-weight-bold mb-2">Role Permissions</h4>
                            <p class="text-caption">
                                Roles control what users can do in the system. Select permissions to define what actions
                                users with this role can perform.
                            </p>
                        </div>
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
    </form>
</template>

<script setup>
    import { ref, watch, onMounted, computed } from 'vue';

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

    // View mode toggle (tabs or list)
    const viewMode = ref('tabs');
    const activePermissionTab = ref(null);

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

        // Set initial active tab to the first group
        if (Object.keys(permissionGroups.value).length > 0) {
            activePermissionTab.value = Object.keys(permissionGroups.value)[0];
        }

        // Try to load user preference for view mode from localStorage
        const savedViewMode = localStorage.getItem('roleForm.viewMode');
        if (savedViewMode && ['tabs', 'list'].includes(savedViewMode)) {
            viewMode.value = savedViewMode;
        }
    });

    // Save view mode preference when it changes
    watch(viewMode, (newMode) => {
        localStorage.setItem('roleForm.viewMode', newMode);
    });

    // Group permissions by resource type
    const permissionGroups = computed(() => {
        const groups = {};

        props.permissions.forEach(permission => {
            const nameSegments = permission.name.split(' ');

            // Skip if no proper format
            if (nameSegments.length < 2) return;

            // Get the last word which is typically the resource name (user, role, product, etc.)
            let resourceType = nameSegments[nameSegments.length - 1];

            // Some special cases need handling
            if (resourceType === 'item') {
                if (nameSegments[nameSegments.length - 2] === 'cart') {
                    resourceType = 'cart item';
                } else if (nameSegments[nameSegments.length - 2] === 'order') {
                    resourceType = 'order item';
                }
            } else if (resourceType === 'image' && nameSegments[nameSegments.length - 2] === 'product') {
                resourceType = 'product image';
            }

            if (!groups[resourceType]) {
                groups[resourceType] = [];
            }

            groups[resourceType].push(permission);
        });

        // Sort groups alphabetically
        return Object.fromEntries(
            Object.entries(groups).sort((a, b) => a[0].localeCompare(b[0]))
        );
    });

    const formatPermissionName = (name) => {
        return name
            .replace(/([A-Z])/g, ' $1')
            .replace(/_/g, ' ')
            .replace(/\w\S*/g, (txt) => {
                return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
            });
    };

    const formatSimpleGroupName = (name) => {
        return name.split(' ')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ');
    };

    const formatGroupName = (name) => {
        return formatSimpleGroupName(name) + ' Management';
    };

    const formatActionName = (name) => {
        const parts = name.split(' ');
        if (parts.length >= 2) {
            let action = parts[0];
            if (parts[0] === 'view' && parts[1] === 'any') {
                action = 'View All';
            } else if (parts[0] === 'force' && parts[1] === 'delete') {
                action = 'Force Delete';
            } else {
                action = action.charAt(0).toUpperCase() + action.slice(1);
            }
            return action;
        }
        return formatPermissionName(name);
    };

    const getPermissionDescription = (name) => {
        const parts = name.split(' ');
        if (parts.length < 2) return '';

        const action = parts[0];
        let resourceType = parts[parts.length - 1];

        // Handle special cases
        if (resourceType === 'item') {
            if (parts[parts.length - 2] === 'cart') {
                resourceType = 'cart items';
            } else if (parts[parts.length - 2] === 'order') {
                resourceType = 'order items';
            }
        } else if (resourceType === 'image' && parts[parts.length - 2] === 'product') {
            resourceType = 'product images';
        } else {
            // Pluralize resource type
            resourceType = resourceType + 's';
        }

        const actionDescriptions = {
            'view': 'Can view',
            'create': 'Can create new',
            'update': 'Can edit existing',
            'delete': 'Can remove',
            'restore': 'Can restore deleted',
            'force': 'Can permanently delete'
        };

        const baseAction = action === 'force' ? 'force delete' : action;
        let desc = actionDescriptions[baseAction] || `Can ${baseAction}`;

        if (action === 'view' && parts.includes('any')) {
            desc = 'Can view all';
        }

        return `${desc} ${resourceType}`;
    };

    const getGroupIcon = (groupName) => {
        const icons = {
            'user': 'mdi-account',
            'role': 'mdi-shield-account',
            'category': 'mdi-shape',
            'brand': 'mdi-tag',
            'product': 'mdi-package-variant-closed',
            'product image': 'mdi-image',
            'order': 'mdi-cart',
            'order item': 'mdi-cart-variant',
            'review': 'mdi-star',
            'banner': 'mdi-image-multiple',
            'cart item': 'mdi-cart-plus',
            'customer' : 'mdi-account-multiple',
            'inventory' : 'mdi-warehouse',
            'notification': 'mdi-bell',
            'setting': 'mdi-cog',
            'thread': 'mdi-forum',
        };

        return icons[groupName] || 'mdi-shield';
    };

    const isPermissionSelected = (permissionId) => {
        return form.value.permissions.includes(permissionId);
    };

    const togglePermission = (permissionId) => {
        const index = form.value.permissions.indexOf(permissionId);
        if (index === -1) {
            form.value.permissions.push(permissionId);
        } else {
            form.value.permissions.splice(index, 1);
        }
    };

    const isAllGroupSelected = (groupName) => {
        const groupPermissionIds = permissionGroups.value[groupName].map(p => p.id);
        return groupPermissionIds.every(id => form.value.permissions.includes(id));
    };

    const toggleGroupPermissions = (groupName) => {
        const groupPermissionIds = permissionGroups.value[groupName].map(p => p.id);

        if (isAllGroupSelected(groupName)) {
            // Deselect all permissions in this group
            form.value.permissions = form.value.permissions.filter(id => !groupPermissionIds.includes(id));
        } else {
            // Select all permissions in this group
            const newPermissions = [...form.value.permissions];

            groupPermissionIds.forEach(id => {
                if (!newPermissions.includes(id)) {
                    newPermissions.push(id);
                }
            });

            form.value.permissions = newPermissions;
        }
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
    .permission-container {
        border-radius: 8px;
        overflow: hidden;
    }

    .permission-panels-container {
        max-height: 600px;
        overflow-y: auto;
        background-color: #fafafa;
    }

    .permission-cards {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 12px;
    }

    .permission-card {
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .permission-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .permission-card.selected {
        transform: translateY(-3px);
        border: none;
    }

    .permission-group-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .permission-group {
        background-color: white;
        border-radius: 8px;
        margin-bottom: 16px;
    }

    .border-bottom {
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    }

    .sticky-card {
        position: sticky;
        top: 24px;
    }

    /* Animation for transitioning between views */
    .v-window-item, .v-card-text {
        transition: opacity 0.3s ease;
    }
</style>
