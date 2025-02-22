<template>
    <div class="pagination">
        <template v-for="(link, key) in links" :key="key">
            <Link v-if="link.url" :href="link.url" class="v-btn v-btn--variant-text mx-1"
                :class="{ 'v-btn--active': link.active }" v-html="link.label" />
            <span v-else class="v-btn v-btn--variant-text v-btn--disabled mx-1" v-html="link.label" />
        </template>
    </div>
</template>

<script setup>
    import { Link } from '@inertiajs/vue3'

    defineProps({
        links: {
            type: Array,
            required: true
        }
    })
</script>

<style scoped>
    .v-btn--active {
        background-color: rgb(var(--v-theme-primary));
        color: white;
    }
</style>
