<template>

    <Head :title="category.name" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Category Details: {{ category.name }}
                </h2>
                <v-spacer></v-spacer>
                <Link :href="route('dashboard.categories.edit', category.uuid)">
                <v-btn color="warning" prepend-icon="mdi-pencil" class="mr-2">
                    Edit
                </v-btn>
                </Link>
                <Link :href="route('dashboard.categories.index')">
                <v-btn color="secondary" prepend-icon="mdi-arrow-left" variant="outlined">
                    Back to Categories
                </v-btn>
                </Link>
            </div>
        </template>

        <v-container fluid class="py-8">
            <v-row>

            </v-row>
        </v-container>
    </DashboardLayout>
</template>

<script setup>
    import DashboardLayout from '@/Layouts/DashboardLayout.vue';
    import { Head, Link } from '@inertiajs/vue3';

    const props = defineProps({
        category: Object
    });

</script>

<style scoped>

</style>
