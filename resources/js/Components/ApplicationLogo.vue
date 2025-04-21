<template>
    <v-img :src="logoUrl" :width="width" :height="height" :contain="true"></v-img>
</template>

<script setup>
    import { computed } from 'vue';
    import { usePage } from '@inertiajs/vue3';

    const props = defineProps({
        width: {
            type: [Number, String],
            default: 180
        },
        height: {
            type: [Number, String],
            default: 60
        }
    });

    // Get logo URL from settings with fallback to default
    const logoUrl = computed(() => {
        const page = usePage();
        return page.props.siteSettings?.logoUrl || '/images/logo.png';
    });
</script>
