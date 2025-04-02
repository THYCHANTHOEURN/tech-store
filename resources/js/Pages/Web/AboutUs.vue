<template>

    <Head title="About Us - Tech Store" />

    <WebLayout>
        <v-container class="py-10">
            <v-row>
                <v-col cols="12">
                    <h1 class="text-h3 font-weight-bold mb-6">About Tech Store</h1>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12" md="6">
                    <v-img :src="aboutUsImage" height="400" cover class="rounded-lg"></v-img>
                </v-col>
                <v-col cols="12" md="6">
                    <h2 class="text-h5 mb-4">Our Story</h2>
                    <p class="text-body-1 mb-4">
                        Founded in 2020, Tech Store has quickly established itself as a premier destination for
                        technology enthusiasts and everyday consumers alike. What began as a small shop with
                        a passion for technology has grown into a comprehensive online and retail experience.
                    </p>
                    <p class="text-body-1 mb-4">
                        Our mission is simple: to provide high-quality tech products at competitive prices with
                        exceptional customer service. We believe that technology should be accessible to everyone,
                        and we strive to make the buying experience as seamless and enjoyable as possible.
                    </p>
                    <p class="text-body-1">
                        At Tech Store, we're not just selling products - we're building a community of tech lovers
                        who share our passion for innovation and excellence.
                    </p>
                </v-col>
            </v-row>

            <v-row class="mt-12">
                <v-col cols="12">
                    <h2 class="text-h4 font-weight-bold mb-6">Our Values</h2>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12" md="4">
                    <v-card height="100%" class="pa-4">
                        <v-icon size="large" color="primary" class="mb-4">mdi-star-circle</v-icon>
                        <h3 class="text-h6 mb-2">Quality</h3>
                        <p class="text-body-2">
                            We meticulously select every product in our inventory to ensure the highest quality
                            standards. We work with trusted brands and manufacturers to bring you reliable
                            technology that lasts.
                        </p>
                    </v-card>
                </v-col>

                <v-col cols="12" md="4">
                    <v-card height="100%" class="pa-4">
                        <v-icon size="large" color="primary" class="mb-4">mdi-shield-check</v-icon>
                        <h3 class="text-h6 mb-2">Trust</h3>
                        <p class="text-body-2">
                            Trust is the foundation of our business. We provide transparent information about
                            our products, honest pricing, and reliable customer support. Your satisfaction is
                            our top priority.
                        </p>
                    </v-card>
                </v-col>

                <v-col cols="12" md="4">
                    <v-card height="100%" class="pa-4">
                        <v-icon size="large" color="primary" class="mb-4">mdi-lightbulb-on</v-icon>
                        <h3 class="text-h6 mb-2">Innovation</h3>
                        <p class="text-body-2">
                            We constantly stay ahead of tech trends, bringing the latest innovations to our
                            customers. We embrace change and are always looking for new ways to improve
                            both our product offerings and customer experience.
                        </p>
                    </v-card>
                </v-col>
            </v-row>

            <v-row class="mt-12">
                <v-col cols="12">
                    <h2 class="text-h4 font-weight-bold mb-6">Our Team</h2>
                    <p class="text-body-1 mb-8">
                        Our dedicated team of tech enthusiasts, customer service specialists, and logistics
                        experts work together to deliver an exceptional shopping experience. We're united by
                        our passion for technology and our commitment to customer satisfaction.
                    </p>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12" sm="6" md="3">
                    <v-card class="text-center pa-4">
                        <v-avatar size="100" class="mb-4">
                            <v-img :src="teamImages.ceo" cover></v-img>
                        </v-avatar>
                        <h3 class="text-h6">John Doe</h3>
                        <p class="text-subtitle-1 text-grey">CEO & Founder</p>
                    </v-card>
                </v-col>

                <v-col cols="12" sm="6" md="3">
                    <v-card class="text-center pa-4">
                        <v-avatar size="100" class="mb-4">
                            <v-img :src="teamImages.cto" cover></v-img>
                        </v-avatar>
                        <h3 class="text-h6">Jane Smith</h3>
                        <p class="text-subtitle-1 text-grey">CTO</p>
                    </v-card>
                </v-col>

                <v-col cols="12" sm="6" md="3">
                    <v-card class="text-center pa-4">
                        <v-avatar size="100" class="mb-4">
                            <v-img :src="teamImages.marketing" cover></v-img>
                        </v-avatar>
                        <h3 class="text-h6">Michael Johnson</h3>
                        <p class="text-subtitle-1 text-grey">Marketing Director</p>
                    </v-card>
                </v-col>

                <v-col cols="12" sm="6" md="3">
                    <v-card class="text-center pa-4">
                        <v-avatar size="100" class="mb-4">
                            <v-img :src="teamImages.support" cover></v-img>
                        </v-avatar>
                        <h3 class="text-h6">Sarah Williams</h3>
                        <p class="text-subtitle-1 text-grey">Customer Support Lead</p>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </WebLayout>
</template>

<script setup>
    import { Head } from '@inertiajs/vue3';
    import WebLayout from '@/Layouts/WebLayout.vue';

    // Define props with defaults in case the controller doesn't provide them
    const props = defineProps({
        aboutUsImage: {
            type: String,
            default: '/storage/settings/about-us.jpg'
        },
        teamImages: {
            type: Object,
            default: () => ({
                ceo: '/storage/settings/team-ceo.jpg',
                cto: '/storage/settings/team-cto.jpg',
                marketing: '/storage/settings/team-marketing.jpg',
                support: '/storage/settings/team-support.jpg'
            })
        }
    });
</script>
