<template>
    <v-breadcrumbs :items="items" class="px-0">
        <template v-slot:divider>
            <v-icon icon="mdi-chevron-right"></v-icon>
        </template>
    </v-breadcrumbs>
</template>

<script setup>
    defineProps({
        items: {
            type: Array,
            required: true
        }
    });
</script>
