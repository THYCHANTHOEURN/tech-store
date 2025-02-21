<template>
    <section class="banner-section py-8">
        <v-container>
            <v-row>
                <v-col v-for="banner in banners" :key="banner.id" cols="12" md="4">
                    <v-hover v-slot="{ isHovering, props }">
                        <v-card v-bind="props" :elevation="isHovering ? 4 : 1" class="banner-card">
                            <v-img :src="banner.image_url" height="200" cover>
                                <div class="banner-content">
                                    <h3 class="text-h6 white--text font-weight-bold">{{ banner.title }}</h3>
                                    <!-- <p class="text-subtitle-1 white--text mt-2">{{ banner.subtitle }}</p> -->
                                    <v-btn color="white" class="mt-4" :to="banner.link">Shop Now</v-btn>
                                </div>
                            </v-img>
                        </v-card>
                    </v-hover>
                </v-col>
            </v-row>
        </v-container>
    </section>
</template>

<script setup>
    defineProps({
        banners: {
            type: Array,
            required: true
        }
    });
</script>

<style scoped>
    .banner-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
        width: 100%;
        padding: 1rem;
        background: rgba(0, 0, 0, 0.4);
        color: white;
    }
</style>
