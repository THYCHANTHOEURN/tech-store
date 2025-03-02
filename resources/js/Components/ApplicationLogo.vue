<template>
    <v-img src="/images/logo.png" :width="width" :height="height" :contain="true"></v-img>
</template>

<script setup>
    defineProps({
        width: {
            type: [Number, String],
            default: 180
        },
        height: {
            type: [Number, String],
            default: 60
        }
    });
</script>
