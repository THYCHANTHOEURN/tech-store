<template>
    <v-hover v-slot="{ isHovering, props }">
        <v-card v-bind="props" :elevation="isHovering ? 4 : 1" :class="{ 'on-hover': isHovering }">
            <!-- Primary image -->
            <v-img :src="product.primary_image_url" height="200" cover>
                <div class="product-overlay" v-if="product.sale_price">
                    <v-chip color="error" small>Sale</v-chip>
                </div>
            </v-img>

            <v-card-item>
                <v-card-title class="text-subtitle-1">{{ product.name }}</v-card-title>
                <v-card-subtitle>
                    <div class="d-flex align-center">
                        <span :class="{ 'text-decoration-line-through': product.sale_price }" class="mr-2">
                            ${{ product.price }}
                        </span>
                        <span v-if="product.sale_price" class="error--text">${{ product.sale_price }}</span>
                    </div>
                </v-card-subtitle>
            </v-card-item>

            <v-card-actions>
                <v-btn color="primary" variant="tonal" block @click="addToCart">
                    Add to Cart
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-hover>
</template>

<script setup>
    const props = defineProps({
        product: {
            type: Object,
            required: true
        }
    });

    const addToCart = () => {
        // Implement cart functionality
        console.log('Adding to cart:', props.product.id);
    };
</script>

<style scoped>
    .product-overlay {
        position: absolute;
        top: 10px;
        right: 10px;
    }

    .on-hover {
        transform: scale(1.02);
        transition: all 0.3s;
    }
</style>
