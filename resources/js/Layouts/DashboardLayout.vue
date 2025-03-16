<script setup>
    import { ref } from 'vue';
    import ApplicationLogo from '@/Components/ApplicationLogo.vue';
    import { Link, usePage, router } from '@inertiajs/vue3';
    import FlashMessage from '@/Components/FlashMessage.vue';

    const drawer = ref(true);
    const user = usePage().props.auth.user;

    const logout = () => {
        router.post(route('logout'));
    };

</script>

<template>
    <v-app>
        <!-- Flash messages component -->
        <FlashMessage />

        <!-- Main Toolbar -->
        <v-app-bar elevation="1">
            <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon>

            <v-toolbar-title>
                <Link :href="route('dashboard.index')">
                <ApplicationLogo class="h-9 w-auto" />
                </Link>
            </v-toolbar-title>

            <v-spacer></v-spacer>

            <!-- User Menu -->
            <v-menu>
                <template v-slot:activator="{ props }">
                    <v-btn v-bind="props" variant="text">
                        {{ user.name }}
                        <v-icon right>mdi-chevron-down</v-icon>
                    </v-btn>
                </template>
                <v-list>
                    <Link :href="route('profile.edit')" class="text-decoration-none">
                    <v-list-item link>
                        <template v-slot:prepend>
                            <v-icon>mdi-account-circle</v-icon>
                        </template>
                        <v-list-item-title>Profile</v-list-item-title>
                    </v-list-item>
                    </Link>
                    <Link :href="route('index')" class="text-decoration-none">
                    <v-list-item link>
                        <template v-slot:prepend>
                            <v-icon>mdi-web</v-icon>
                        </template>
                        <v-list-item-title>Visit Site</v-list-item-title>
                    </v-list-item>
                    </Link>
                    <v-list-item @click="logout" link>
                        <template v-slot:prepend>
                            <v-icon>mdi-logout</v-icon>
                        </template>
                        <v-list-item-title>Log Out</v-list-item-title>
                    </v-list-item>
                </v-list>
            </v-menu>
        </v-app-bar>

        <!-- Sidebar Navigation -->
        <v-navigation-drawer v-model="drawer" permanent>
            <v-list>
                <Link :href="route('dashboard.index')" class="text-decoration-none">
                <v-list-item :active="route().current('dashboard.index')">
                    <template v-slot:prepend>
                        <v-icon>mdi-view-dashboard</v-icon>
                    </template>
                    <v-list-item-title>Dashboard</v-list-item-title>
                </v-list-item>
                </Link>

                <!-- Products Menu Item -->
                <Link :href="route('dashboard.products.index')" class="text-decoration-none">
                <v-list-item :active="route().current('dashboard.products.*')">
                    <template v-slot:prepend>
                        <v-icon>mdi-package-variant-closed</v-icon>
                    </template>
                    <v-list-item-title>Products</v-list-item-title>
                </v-list-item>
                </Link>

                <!-- Categories Menu Item -->
                <Link :href="route('dashboard.categories.index')" class="text-decoration-none">
                <v-list-item :active="route().current('dashboard.categories.*')">
                    <template v-slot:prepend>
                        <v-icon>mdi-format-list-bulleted</v-icon>
                    </template>
                    <v-list-item-title>Categories</v-list-item-title>
                </v-list-item>
                </Link>

                <!-- Brands Menu Item -->
                <Link :href="route('dashboard.brands.index')" class="text-decoration-none">
                <v-list-item :active="route().current('dashboard.brands.*')">
                    <template v-slot:prepend>
                        <v-icon>mdi-label</v-icon>
                    </template>
                    <v-list-item-title>Brands</v-list-item-title>
                </v-list-item>
                </Link>

                <!-- Banners Menu Item -->
                <Link :href="route('dashboard.banners.index')" class="text-decoration-none">
                <v-list-item :active="route().current('dashboard.banners.*')">
                    <template v-slot:prepend>
                        <v-icon>mdi-image-multiple</v-icon>
                    </template>
                    <v-list-item-title>Banners</v-list-item-title>
                </v-list-item>
                </Link>

                <!-- Orders Menu Item -->
                <Link :href="route('dashboard.orders.index')" class="text-decoration-none">
                <v-list-item :active="route().current('dashboard.orders.*')">
                    <template v-slot:prepend>
                        <v-icon>mdi-cart</v-icon>
                    </template>
                    <v-list-item-title>Orders</v-list-item-title>
                </v-list-item>
                </Link>

                <!-- Customers Menu Item -->
                <Link :href="route('dashboard.customers.index')" class="text-decoration-none">
                <v-list-item :active="route().current('dashboard.customers.*')">
                    <template v-slot:prepend>
                        <v-icon>mdi-account-group</v-icon>
                    </template>
                    <v-list-item-title>Customers</v-list-item-title>
                </v-list-item>
                </Link>

                <!-- Users Menu Item -->
                <Link :href="route('dashboard.users.index')" class="text-decoration-none">
                <v-list-item :active="route().current('dashboard.users.*')">
                    <template v-slot:prepend>
                        <v-icon>mdi-account-multiple</v-icon>
                    </template>
                    <v-list-item-title>Users</v-list-item-title>
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
</style>
