<template>
    <section class="py-8">
        <v-container>
            <v-row>
                <v-col v-for="(item, index) in items" :key="index" cols="12" sm="6" md="3">
                    <v-hover v-slot="{ isHovering, props }">
                        <v-card v-bind="props" :elevation="isHovering ? 4 : 1" class="pa-4 text-center h-100">
                            <v-icon :icon="item.icon" size="x-large" color="primary" class="mb-4"></v-icon>
                            <h3 class="text-h6 font-weight-bold mb-2">{{ item.title }}</h3>
                            <p class="text-body-2 text-grey-darken-1">{{ item.description }}</p>
                        </v-card>
                    </v-hover>
                </v-col>
            </v-row>
        </v-container>
    </section>
</template>

<script setup>
    const items = [
        {
            icon: 'mdi-truck-fast-outline',
            title: 'Fast Delivery',
            description: 'Fast delivery service to 25 provinces'
        },
        {
            icon: 'mdi-currency-usd',
            title: 'Best Prices',
            description: 'Offering the best prices in the city'
        },
        {
            icon: 'mdi-shield-check-outline',
            title: 'Warranty',
            description: 'Comprehensive warranty on all products'
        },
        {
            icon: 'mdi-headphones',
            title: 'Online Support',
            description: '24/7 online customer support'
        }
    ];
</script>
