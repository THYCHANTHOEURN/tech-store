<template>
    <v-app>
        <v-main>
            <v-container fluid>
                <v-row style="min-height: 100vh;" align="center" justify="center">
                    <v-col cols="12" sm="8" md="6" lg="4">
                        <v-row style="min-height: 40vh;" align="center" justify="center">
                            <div class="text-center">
                                <Link :href="route('index')">
                                    <ApplicationLogo width="180" height="60" />
                                </Link>
                            </div>
                        </v-row>
                        <v-card class="elevation-2 rounded-lg">
                            <v-card-text class="pa-6">
                                <slot />
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </v-main>
    </v-app>
</template>

<script setup>
    import ApplicationLogo from '@/Components/ApplicationLogo.vue';
    import { Link } from '@inertiajs/vue3';
</script>

<style scoped>
    .v-container {
        background-color: rgb(243, 244, 246);
    }
</style>
