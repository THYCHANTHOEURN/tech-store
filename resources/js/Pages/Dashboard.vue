
<template>
    <Head title="Dashboard" />

    <DashboardLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template>

        <v-container class="py-12">
            <v-row justify="center">
                <v-col cols="12" sm="8" md="6">
                    <v-card>
                        <v-card-title class="text-gray-800">You're logged in!</v-card-title>
                        <v-card-text class="text-gray-900">
                            Welcome to the dashboard.
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </DashboardLayout>
</template>

<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
</script>
