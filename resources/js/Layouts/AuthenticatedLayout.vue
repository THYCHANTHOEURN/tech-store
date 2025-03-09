<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link, router } from '@inertiajs/vue3';

const drawer = ref(false);

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <v-app>
        <!-- Main Toolbar -->
        <v-app-bar elevation="1">
            <v-app-bar-nav-icon class="d-sm-none" @click="drawer = !drawer"></v-app-bar-nav-icon>

            <v-toolbar-title>
                <Link :href="route('dashboard.index')">
                <ApplicationLogo class="h-9 w-auto" />
                </Link>
            </v-toolbar-title>

            <v-spacer></v-spacer>

            <!-- Desktop Navigation -->
            <div class="d-none d-sm-flex align-center">
                <v-btn variant="text" :href="route('dashboard.index')"
                    :color="route().current('dashboard.index') ? 'primary' : 'default'">
                    Dashboard
                </v-btn>

                <v-menu>
                    <template v-slot:activator="{ props }">
                        <v-btn v-bind="props" variant="text">
                            {{ $page.props.auth.user.name }}
                            <v-icon>mdi-chevron-down</v-icon>
                        </v-btn>
                    </template>
                    <v-list>
                        <v-list-item :href="route('profile.edit')" link>
                            <v-list-item-title>Profile</v-list-item-title>
                        </v-list-item>
                        <v-list-item @click="logout" link>
                            <v-list-item-title>Log Out</v-list-item-title>
                        </v-list-item>
                    </v-list>
                </v-menu>
            </div>
        </v-app-bar>

        <!-- Mobile Navigation Drawer -->
        <v-navigation-drawer v-model="drawer" temporary>
            <v-list>
                <v-list-item>
                    <v-list-item-title class="text-body-2">
                        {{ $page.props.auth.user.name }}
                    </v-list-item-title>
                    <v-list-item-subtitle>
                        {{ $page.props.auth.user.email }}
                    </v-list-item-subtitle>
                </v-list-item>

                <v-divider></v-divider>

                <v-list-item :href="route('dashboard.index')" link
                    :color="route().current('dashboard.index') ? 'primary' : 'default'">
                    <template v-slot:prepend>
                        <v-icon>mdi-view-dashboard</v-icon>
                    </template>
                    Dashboard
                </v-list-item>

                <v-list-item :href="route('profile.edit')" link>
                    <template v-slot:prepend>
                        <v-icon>mdi-account</v-icon>
                    </template>
                    Profile
                </v-list-item>

                <v-list-item @click="logout" link>
                    <template v-slot:prepend>
                        <v-icon>mdi-logout</v-icon>
                    </template>
                    Log Out
                </v-list-item>
            </v-list>
        </v-navigation-drawer>

        <!-- Main Content -->
        <v-main>
            <!-- Page Header -->
            <template v-if="$slots.header">
                <v-container fluid class="py-6 bg-grey-lighten-4">
                    <v-row>
                        <v-col>
                            <slot name="header" />
                        </v-col>
                    </v-row>
                </v-container>
            </template>

            <!-- Page Content -->
            <v-container fluid>
                <slot />
            </v-container>
        </v-main>
    </v-app>
</template>

<style scoped>
    :deep(.v-btn) {
        text-transform: none;
        letter-spacing: 0;
    }
</style>
