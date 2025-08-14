<script setup>
    import { ref, onMounted, onUnmounted, computed, watch } from 'vue';
    import ApplicationLogo from '@/Components/ApplicationLogo.vue';
    import { Link, usePage, router } from '@inertiajs/vue3';
    import FlashMessage from '@/Components/FlashMessage.vue';
    import NotificationDropdown from '@/Components/Dashboard/NotificationDropdown.vue';
    import ThemeToggle from '@/Components/Dashboard/ThemeToggle.vue';
    import { useMessages } from '@/Composables/useMessages';
    import { useI18n } from 'vue-i18n';

    const { t, locale } = useI18n();

    const rail = ref(false);
    const drawer = ref(true);
    const user = usePage().props.auth.user;

    const toggleDrawer = () => {
        if (drawer.value) {
            rail.value = !rail.value;
        } else {
            drawer.value = true;
            rail.value = false;
        }
    };

    const logout = () => {
        router.post(route('logout'));
    };

    // Add messages state
    const { unreadCount: unreadMessagesCount, fetchUnreadCount } = useMessages();

    // Theme settings
    const currentTheme = ref(localStorage.getItem('theme') || 'light');

    const updateTheme = () => {
        currentTheme.value = localStorage.getItem('theme') || 'light';
        // Additional theme updates can be performed here if needed
    };

    onMounted(() => {
        // Fetch unread message count on mount
        fetchUnreadCount();

        // Set up interval to check for new messages
        const messagesInterval = setInterval(() => {
            fetchUnreadCount();
        }, 30000); // Check every 30 seconds

        // Listen for theme changes
        window.addEventListener('theme-changed', updateTheme);

        // Clean up
        onUnmounted(() => {
            clearInterval(messagesInterval);
            window.removeEventListener('theme-changed', updateTheme);
        });
    });

    // Add computed property for site name
    const siteName = computed(() => {
        return usePage().props.siteSettings?.siteName ||
            usePage().props.siteSettings?.companyInfo?.name ||
            'Tech Store';
    });
</script>

<template>
    <v-app :theme="currentTheme">
        <!-- Flash messages component -->
        <FlashMessage />

        <!-- Enhanced Main Toolbar -->
        <v-app-bar elevation="2" color="white" class="border-b">
            <v-app-bar-nav-icon @click="toggleDrawer"
                :aria-label="rail ? 'Expand menu' : 'Collapse menu'"></v-app-bar-nav-icon>

            <v-toolbar-title class="d-flex align-center">
                <Link :href="route('dashboard.index')" class="text-decoration-none d-flex align-center">
                <ApplicationLogo :width="rail ? 40 : 125" height="40" class="ml-2" />
                <span class="ml-2 site-name text-truncate" :class="{ 'd-none d-sm-block': rail }">{{ siteName }}</span>
                </Link>
            </v-toolbar-title>

            <v-spacer></v-spacer>

            <div class="d-flex align-center">
                <!-- Theme Toggle Button -->
                <ThemeToggle :size="$vuetify.display.xs ? 'default' : 'large'" :show-full-options="true" class="mr-2" />

                <!-- Add badge to messages button - Add responsive margin -->
                <Link :href="route('dashboard.messages.index')" class="text-decoration-none"
                    :class="{ 'mr-2': $vuetify.display.width > 400 }">
                <v-btn icon variant="text" :size="$vuetify.display.xs ? 'default' : 'large'">
                    <v-badge :content="unreadMessagesCount" :value="unreadMessagesCount > 0" color="error" overlap>
                        <v-icon>mdi-email-outline</v-icon>
                    </v-badge>
                </v-btn>
                </Link>

                <!-- Notification Dropdown -->
                <NotificationDropdown :initial-notifications="$page.props.notifications"
                    :initial-unread-count="$page.props.unreadNotificationsCount" />

                                    <!-- Language Switcher -->
                                    <v-select
                                        v-model="locale"
                                        :items="[
                                            { value: 'en', title: 'English' },
                                            { value: 'km', title: 'ភាសាខ្មែរ' }
                                        ]"
                                        item-title="title"
                                        item-value="value"
                                        variant="outlined"
                                        density="compact"
                                        hide-details
                                        style="max-width: 120px; margin-left: 12px;"
                                    />
            </div>

            <!-- User Menu - Enhanced -->
            <v-menu transition="slide-y-transition" location="bottom end">
                <template v-slot:activator="{ props }">
                    <v-btn v-bind="props" variant="text" rounded="pill" class="text-none px-2 px-sm-3">
                        <v-avatar size="32" color="primary" class="mr-1 mr-sm-2">
                            <span class="text-white text-subtitle-2">{{ user.name.charAt(0).toUpperCase() }}</span>
                        </v-avatar>
                        <span class="d-none d-sm-block text-truncate user-name">{{ user.name }}</span>
                        <v-icon size="small" class="ml-1 ml-sm-2">mdi-chevron-down</v-icon>
                    </v-btn>
                </template>
                <v-list density="compact" width="200">
                    <Link :href="route('profile.edit')" class="text-decoration-none">
                    <v-list-item link>
                        <template v-slot:prepend>
                            <v-icon>mdi-account-circle</v-icon>
                        </template>
                        <v-list-item-title>{{ t('Profile') }}</v-list-item-title>
                    </v-list-item>
                    </Link>
                    <Link :href="route('index')" class="text-decoration-none" target="_blank">
                    <v-list-item link>
                        <template v-slot:prepend>
                            <v-icon>mdi-web</v-icon>
                        </template>
                        <v-list-item-title>{{ t('Visit Site') }}</v-list-item-title>
                    </v-list-item>
                    </Link>
                    <v-divider class="my-1"></v-divider>
                    <v-list-item @click="logout" link>
                        <template v-slot:prepend>
                            <v-icon>mdi-logout</v-icon>
                        </template>
                        <v-list-item-title>{{ t('Logout') }}</v-list-item-title>
                    </v-list-item>
                </v-list>
            </v-menu>
        </v-app-bar>

        <!-- Responsive Sidebar Navigation with Rail Mode -->
        <v-navigation-drawer v-model="drawer" :rail="rail" permanent :width="240" @click="rail = false">
            <v-list>
                <v-list-item v-if="rail" class="justify-center mb-2">
                    <v-btn size="small" icon variant="text" @click.stop="rail = false">
                        <v-icon>mdi-chevron-right</v-icon>
                    </v-btn>
                </v-list-item>
                <v-list-item v-else>
                    <v-list-item-title class="text-center text-subtitle-2 font-weight-bold">
                        {{ t('Dasboard Menu') }}
                    </v-list-item-title>
                    <template v-slot:append>
                        <v-btn size="small" icon variant="text" @click.stop="rail = true">
                            <v-icon>mdi-chevron-left</v-icon>
                        </v-btn>
                    </template>
                </v-list-item>

                <v-divider class="mb-2"></v-divider>

                <Link :href="route('dashboard.index')" class="text-decoration-none">
                <v-list-item :active="route().current('dashboard.index')" rounded="lg" class="mb-1">
                    <template v-slot:prepend>
                        <v-icon>mdi-view-dashboard</v-icon>
                    </template>
                    <v-list-item-title>{{ t('Dashboard') }}</v-list-item-title>
                    <v-tooltip v-if="rail" activator="parent" location="right">{{ t('Dashboard') }}</v-tooltip>
                </v-list-item>
                </Link>

                <!-- Products Menu Item -->
                <Link :href="route('dashboard.products.index')" class="text-decoration-none">
                <v-list-item :active="route().current('dashboard.products.*')" rounded="lg" class="mb-1">
                    <template v-slot:prepend>
                        <v-icon>mdi-package-variant-closed</v-icon>
                    </template>
                    <v-list-item-title>{{ t('Products') }}</v-list-item-title>
                    <v-tooltip v-if="rail" activator="parent" location="right">{{ t('Products') }}</v-tooltip>
                </v-list-item>
                </Link>

                <!-- Inventory Management -->
                <Link :href="route('dashboard.inventory.index')" class="text-decoration-none">
                <v-list-item :active="route().current('dashboard.inventory.*')" rounded="lg" class="mb-1">
                    <template v-slot:prepend>
                        <v-icon>mdi-package-variant</v-icon>
                    </template>
                    <v-list-item-title>{{ t('Inventory') }}</v-list-item-title>
                    <v-tooltip v-if="rail" activator="parent" location="right">{{ t('Inventory') }}</v-tooltip>
                </v-list-item>
                </Link>

                <!-- Categories Menu Item -->
                <Link :href="route('dashboard.categories.index')" class="text-decoration-none">
                <v-list-item :active="route().current('dashboard.categories.*')" rounded="lg" class="mb-1">
                    <template v-slot:prepend>
                        <v-icon>mdi-format-list-bulleted</v-icon>
                    </template>
                    <v-list-item-title>{{ t('Categories') }}</v-list-item-title>
                    <v-tooltip v-if="rail" activator="parent" location="right">{{ t('Categories') }}</v-tooltip>
                </v-list-item>
                </Link>

                <!-- Brands Menu Item -->
                <Link :href="route('dashboard.brands.index')" class="text-decoration-none">
                <v-list-item :active="route().current('dashboard.brands.*')" rounded="lg" class="mb-1">
                    <template v-slot:prepend>
                        <v-icon>mdi-label</v-icon>
                    </template>
                    <v-list-item-title>{{ t('Brands') }}</v-list-item-title>
                    <v-tooltip v-if="rail" activator="parent" location="right">{{ t('Brands') }}</v-tooltip>
                </v-list-item>
                </Link>

                <!-- Banners Menu Item -->
                <Link :href="route('dashboard.banners.index')" class="text-decoration-none">
                <v-list-item :active="route().current('dashboard.banners.*')" rounded="lg" class="mb-1">
                    <template v-slot:prepend>
                        <v-icon>mdi-image-multiple</v-icon>
                    </template>
                    <v-list-item-title>{{ t('Banners') }}</v-list-item-title>
                    <v-tooltip v-if="rail" activator="parent" location="right">{{ t('Banners') }}</v-tooltip>
                </v-list-item>
                </Link>

                <!-- Orders Menu Item -->
                <Link :href="route('dashboard.orders.index')" class="text-decoration-none">
                <v-list-item :active="route().current('dashboard.orders.*')" rounded="lg" class="mb-1">
                    <template v-slot:prepend>
                        <v-icon>mdi-cart</v-icon>
                    </template>
                    <v-list-item-title>{{ t('Orders') }}</v-list-item-title>
                    <v-tooltip v-if="rail" activator="parent" location="right">{{ t('Orders') }}</v-tooltip>
                </v-list-item>
                </Link>

                <!-- Customers Menu Item -->
                <Link :href="route('dashboard.customers.index')" class="text-decoration-none">
                <v-list-item :active="route().current('dashboard.customers.*')" rounded="lg" class="mb-1">
                    <template v-slot:prepend>
                        <v-icon>mdi-account-group</v-icon>
                    </template>
                    <v-list-item-title>{{ t('Customers') }}</v-list-item-title>
                    <v-tooltip v-if="rail" activator="parent" location="right">{{ t('Customers') }}</v-tooltip>
                </v-list-item>
                </Link>

                <!-- Users Menu Item -->
                <Link :href="route('dashboard.users.index')" class="text-decoration-none">
                <v-list-item :active="route().current('dashboard.users.*')" rounded="lg" class="mb-1">
                    <template v-slot:prepend>
                        <v-icon>mdi-account-multiple</v-icon>
                    </template>
                    <v-list-item-title>{{ t('Users') }}</v-list-item-title>
                    <v-tooltip v-if="rail" activator="parent" location="right">{{ t('Users') }}</v-tooltip>
                </v-list-item>
                </Link>

                <!-- Roles Menu Item -->
                <Link :href="route('dashboard.roles.index')" class="text-decoration-none">
                <v-list-item :active="route().current('dashboard.roles.*')" rounded="lg" class="mb-1">
                    <template v-slot:prepend>
                        <v-icon>mdi-shield-account</v-icon>
                    </template>
                    <v-list-item-title>{{ t('Roles & Permissions') }}</v-list-item-title>
                    <v-tooltip v-if="rail" activator="parent" location="right">{{ t('Roles & Permissions') }}</v-tooltip>
                </v-list-item>
                </Link>

                <!-- Activity Logs Menu Item -->
                <Link :href="route('dashboard.activity-logs.index')" class="text-decoration-none">
                <v-list-item :active="route().current('dashboard.activity-logs.*')" rounded="lg" class="mb-1">
                    <template v-slot:prepend>
                        <v-icon>mdi-history</v-icon>
                    </template>
                    <v-list-item-title>{{ t('Activity Logs') }}</v-list-item-title>
                    <v-tooltip v-if="rail" activator="parent" location="right">{{ t('Activity Logs') }}</v-tooltip>
                </v-list-item>
                </Link>

                <!-- Settings Menu Item -->
                <Link :href="route('dashboard.settings.index')" class="text-decoration-none">
                <v-list-item :active="route().current('dashboard.settings.*')" rounded="lg" class="mb-1">
                    <template v-slot:prepend>
                        <v-icon>mdi-cog</v-icon>
                    </template>
                    <v-list-item-title>{{ t('Settings') }}</v-list-item-title>
                    <v-tooltip v-if="rail" activator="parent" location="right">{{ t('Settings') }}</v-tooltip>
                </v-list-item>
                </Link>

                <!-- Messages Menu Item -->
                <Link :href="route('dashboard.messages.index')" class="text-decoration-none">
                <v-list-item :active="route().current('dashboard.messages.index')" rounded="lg" class="mb-1">
                    <template v-slot:prepend>
                        <v-icon>mdi-email-multiple-outline</v-icon>
                    </template>
                    <v-list-item-title>{{ t('Messages') }}</v-list-item-title>
                    <v-tooltip v-if="rail" activator="parent" location="right">{{ t('Messages') }}</v-tooltip>
                </v-list-item>
                </Link>

                <v-divider class="my-2"></v-divider>

                <!-- Profile Menu Item -->
                <Link :href="route('profile.edit')" class="text-decoration-none">
                <v-list-item :active="route().current('profile.edit')" rounded="lg" class="mb-1">
                    <template v-slot:prepend>
                        <v-icon>mdi-account-circle</v-icon>
                    </template> <v-list-item-title>{{ t('My Profile') }}</v-list-item-title>
                    <v-tooltip v-if="rail" activator="parent" location="right">{{ t('My Profile') }}</v-tooltip>
                </v-list-item>
                </Link>
            </v-list>
        </v-navigation-drawer>

        <!-- Main Content -->
        <v-main>
            <!-- Page Header -->
            <v-container fluid class="py-3 bg-grey-lighten-4" v-if="$slots.header">
                <slot name="header" />
            </v-container>

            <!-- Page Content -->
            <slot />
        </v-main>
    </v-app>
</template>

<style scoped>
    :deep(.v-btn) {
        text-transform: none;
        letter-spacing: 0;
    }

    .v-list-item--active {
        background-color: rgba(var(--v-theme-primary), 0.1);
        color: rgb(var(--v-theme-primary));
    }

    .border-b {
        border-bottom: 1px solid rgba(0, 0, 0, 0.12);
    }

    .v-navigation-drawer {
        transition: width 0.3s ease;
    }

    /* Hover effect on list items */
    .v-list-item:not(.v-list-item--active):hover {
        background-color: rgba(var(--v-theme-primary), 0.05);
    }

    /* Site name styling */
    .site-name {
        font-size: 1.1rem;
        font-weight: 500;
        max-width: 150px;
    }

    /* User name responsiveness */
    .user-name {
        max-width: 120px;
    }

    /* Responsive adjustments */
    @media (max-width: 600px) {
        .site-name {
            font-size: 1rem;
            max-width: 120px;
        }
    }

    @media (max-width: 400px) {
        .site-name {
            max-width: 80px;
        }
    }
</style>
