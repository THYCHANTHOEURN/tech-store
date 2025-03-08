<template>

    <Head :title="product.name" />
    <WebLayout>
        <v-container>
            <Breadcrumbs :items="breadcrumbs" />

            <v-row>
                <!-- Product Images -->
                <v-col cols="12" md="6">
                    <v-carousel v-model="currentSlide" hide-delimiters show-arrows="hover" height="400">
                        <v-carousel-item v-for="image in product.images" :key="image.id">
                            <v-img :src="image.image_url" height="400" cover></v-img>
                        </v-carousel-item>
                    </v-carousel>

                    <!-- Thumbnail Navigation -->
                    <div class="d-flex mt-4 gap-2">
                        <v-img v-for="(image, index) in product.images" :key="image.id" :src="image.image_url"
                            width="80" height="80" cover class="cursor-pointer"
                            :class="{ 'border-primary': currentSlide === index }" @click="currentSlide = index" />
                    </div>
                </v-col>

                <!-- Product Details -->
                <v-col cols="12" md="6">
                    <!-- Stock Status -->
                    <div class="mb-4">
                        <v-chip :color="product.stock > 0 ? 'success' : 'error'">
                            {{ product.stock > 0 ? 'In Stock' : 'Out of Stock' }}
                        </v-chip>
                    </div>

                    <h1 class="text-h4 mb-2">{{ product.name }}</h1>

                    <!-- Category -->
                    <div class="d-flex align-center mb-4">
                        <Link :href="route('categories.show', { slug: product.category.slug })"
                            class="mr-2 v-chip v-chip--clickable">
                        {{ product.category.name }}
                        </Link>
                    </div>

                    <!-- Price Section -->
                    <div class="mb-4">
                        <div class="d-flex align-center">
                            <span class="text-h5" :class="{ 'text-decoration-line-through': product.sale_price }">
                                ${{ product.price }}
                            </span>
                            <span v-if="product.sale_price" class="text-h5 error--text ml-4">
                                ${{ product.sale_price }}
                            </span>
                        </div>
                        <v-chip v-if="product.sale_price" color="error" class="mt-2">
                            Save ${{ (product.price - product.sale_price).toFixed(2) }}
                        </v-chip>
                    </div>

                    <!-- Add to Cart and Wishlist -->
                    <div class="mb-6">
                        <v-row>
                            <v-col cols="4">
                                <v-text-field v-model="quantity" type="number" min="1" :max="product.stock"
                                    density="compact" hide-details></v-text-field>
                            </v-col>
                            <v-col cols="8" class="d-flex gap-2">
                                <v-btn color="primary" :disabled="product.stock === 0" @click="addToCart"
                                    prepend-icon="mdi-cart-plus">
                                    Add to Cart
                                </v-btn>
                                <v-btn :color="isInWishlist ? 'error' : 'primary'" variant="outlined"
                                    @click="toggleWishlist" prepend-icon="mdi-heart">
                                    {{ isInWishlist ? 'Remove from Wishlist' : 'Add to Wishlist' }}
                                </v-btn>
                            </v-col>
                        </v-row>
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <h3 class="text-h6 mb-2">Description</h3>
                        <p class="text-body-1">{{ product.description }}</p>
                    </div>

                    <!-- Product Meta Information -->
                    <div class="product-meta mt-6">
                        <v-list>
                            <!-- SKU -->
                            <v-list-item>
                                <template v-slot:prepend>
                                    <v-icon>mdi-barcode</v-icon>
                                </template>
                                <div class="d-flex align-center gap-2">
                                    <v-list-item-title>SKU:</v-list-item-title>
                                    <v-list-item-subtitle>{{ product.sku }}</v-list-item-subtitle>
                                </div>
                            </v-list-item>

                            <!-- Brand -->
                            <v-list-item v-if="product.brand">
                                <template v-slot:prepend>
                                    <v-icon>mdi-tag</v-icon>
                                </template>
                                <div class="d-flex align-center gap-2">
                                    <v-list-item-title>Brand:</v-list-item-title>
                                    <v-list-item-subtitle>{{ product.brand.name }}</v-list-item-subtitle>
                                </div>
                            </v-list-item>

                            <!-- Categories -->
                            <v-list-item>
                                <template v-slot:prepend>
                                    <v-icon>mdi-folder-outline</v-icon>
                                </template>
                                <div class="d-flex align-center gap-2">
                                    <v-list-item-title>Categories:</v-list-item-title>
                                    <v-list-item-subtitle>
                                        <Link v-for="(category, index) in productCategories" :key="category.id"
                                            :href="route('categories.show', { slug: category.slug })"
                                            class="text-decoration-none">
                                        {{ category.name }}{{ index < productCategories.length - 1 ? ', ' : '' }}</Link>
                                    </v-list-item-subtitle>
                                </div>
                            </v-list-item>

                            <!-- Share -->
                            <v-list-item>
                                <template v-slot:prepend>
                                    <v-icon>mdi-share-variant</v-icon>
                                </template>
                                <div class="d-flex align-center gap-2">
                                    <v-list-item-title>Share:</v-list-item-title>
                                    <v-list-item-subtitle>
                                        <div class="d-flex gap-2">
                                            <v-btn icon size="small" color="#3b5998"
                                                :href="'https://www.facebook.com/sharer/sharer.php?u=' + currentUrl"
                                                target="_blank">
                                                <v-icon>mdi-facebook</v-icon>
                                            </v-btn>
                                            <v-btn icon size="small" color="#1da1f2"
                                                :href="'https://twitter.com/intent/tweet?url=' + currentUrl + '&text=' + product.name"
                                                target="_blank">
                                                <v-icon>mdi-instagram</v-icon>
                                            </v-btn>
                                            <v-btn icon size="small" color="#1da1f2"
                                                :href="'https://twitter.com/intent/tweet?url=' + currentUrl + '&text=' + product.name"
                                                target="_blank">
                                                <v-icon>mdi-twitter</v-icon>
                                            </v-btn>
                                            <v-btn icon size="small" color="#25D366"
                                                :href="'https://wa.me/?text=' + product.name + ' ' + currentUrl"
                                                target="_blank">
                                                <v-icon>mdi-whatsapp</v-icon>
                                            </v-btn>
                                            <v-btn icon size="small" color="#0088cc"
                                                :href="'https://t.me/share/url?url=' + currentUrl + '&text=' + product.name"
                                                target="_blank">
                                                <v-icon>mdi-send</v-icon>
                                            </v-btn>
                                            <v-btn icon size="small" color="#333" @click="copyToClipboard">
                                                <v-tooltip activator="parent">
                                                    <span>{{ copySuccess ? 'Copied!' : 'Copy Link' }}</span>
                                                </v-tooltip>
                                                <v-icon>{{ copySuccess ? 'mdi-check' : 'mdi-content-copy' }}</v-icon>
                                            </v-btn>
                                        </div>
                                    </v-list-item-subtitle>
                                </div>
                            </v-list-item>
                        </v-list>
                    </div>
                </v-col>
            </v-row>

            <!-- Related Products -->
            <div v-if="relatedProducts.length" class="mt-16">
                <h2 class="text-h5 mb-6">Related Products</h2>
                <v-row>
                    <v-col v-for="product in relatedProducts" :key="product.id" cols="12" sm="6" md="3">
                        <ProductCard :product="product" />
                    </v-col>
                </v-row>
            </div>
        </v-container>
    </WebLayout>
</template>

<script setup>
    import { Head, Link } from '@inertiajs/vue3'
    import Breadcrumbs from '@/Components/Breadcrumbs.vue'
    import ProductCard from '@/Components/ProductCard.vue'
    import WebLayout from '@/Layouts/WebLayout.vue'
    import { ref, computed } from 'vue'
    import { router, usePage } from '@inertiajs/vue3'

    const props = defineProps({
        product: {
            required: true,
            type: Object,
        },
        relatedProducts: {
            default: () => [],
            type: Array,
        },
        breadcrumbs: {
            required: true,
            type: Array,
        },
    })

    const currentSlide = ref(0)
    const quantity = ref(1)
    const copySuccess = ref(false)

    const addToCart = () => {
        router.post(route('cart.store'), {
            product_id: props.product.id,
            quantity: quantity.value
        }, {
            preserveScroll: true,
            onSuccess: () => {
                // Optional: Show success message
            },
        });
    }

    // Check if product is in user's wishlist
    const isInWishlist = computed(() => {
        if (!usePage().props.auth.user) return false;
        
        const items = usePage().props.auth.wishlistItems || [];
        return items.some(item => item.product_id === props.product.id);
    });
    
    // Add toggle wishlist function
    const toggleWishlist = () => {
        if (!usePage().props.auth.user) {
            router.visit(route('login'));
            return;
        }
        
        router.post(route('wishlist.toggle'), {
            product_id: props.product.id
        }, {
            preserveScroll: true,
        });
    };

    const productCategories = computed(() => {
        const categories = []
        let category = props.product.category
        while (category) {
            categories.unshift(category)
            category = category.parent
        }
        return categories
    })

    const currentUrl = computed(() => {
        return window.location.href
    })

    const copyToClipboard = async () => {
        try {
            // First try using the Clipboard API
            if (navigator.clipboard && window.isSecureContext) {
                await navigator.clipboard.writeText(currentUrl.value)
            } else {
                // Fallback for older browsers
                const textArea = document.createElement('textarea')
                textArea.value = currentUrl.value
                textArea.style.position = 'fixed'
                textArea.style.left = '-999999px'
                textArea.style.top = '-999999px'
                document.body.appendChild(textArea)
                textArea.focus()
                textArea.select()
                document.execCommand('copy')
                textArea.remove()
            }
            copySuccess.value = true
            setTimeout(() => {
                copySuccess.value = false
            }, 2000) // Reset after 2 seconds
        } catch (err) {
            console.error('Failed to copy:', err)
            copySuccess.value = false
        }
    }
</script>

<style scoped>
    .cursor-pointer {
        cursor: pointer;
    }

    .border-primary {
        border: 2px solid rgb(var(--v-theme-primary));
    }

    .product-meta {
        border-top: 1px solid rgba(0, 0, 0, 0.12);
        border-bottom: 1px solid rgba(0, 0, 0, 0.12);
    }
</style>
