<template>
    <v-app>
        <v-snackbar
            v-model="showMessage"
            :color="messageType"
            :timeout="3000"
            location="top"
        >
            {{ message }}
        </v-snackbar>
        <!-- Top Bar with Contact Info -->
        <v-app-bar class="bg-primary" density="compact">
            <v-container class="d-flex align-center justify-space-between">
                <div class="d-none d-sm-flex align-center">
                    <span class="text-white mr-4">
                        <v-icon size="small" class="mr-1">mdi-phone</v-icon>
                        +855 12 345 678
                    </span>
                    <span class="text-white">
                        <v-icon size="small" class="mr-1">mdi-email</v-icon>
                        info@techstore.com
                    </span>
                </div>
                <div class="d-flex align-center">
                    <v-btn variant="text" class="text-white" href="https://www.facebook.com/" target="_blank">
                        <v-icon>mdi-facebook</v-icon>
                    </v-btn>
                    <v-btn variant="text" class="text-white" href="https://www.tiktok.com/" target="_blank">
                        <v-icon>mdi-music-box-outline</v-icon>
                    </v-btn>
                    <v-btn variant="text" class="text-white" href="https://www.instagram.com/" target="_blank">
                        <v-icon>mdi-instagram</v-icon>
                    </v-btn>
                    <v-btn variant="text" class="text-white" href="https://t.me/" target="_blank">
                        <v-icon>mdi-send</v-icon>
                    </v-btn>
                    <v-btn variant="text" class="text-white" href="https://www.youtube.com/" target="_blank">
                        <v-icon>mdi-youtube</v-icon>
                    </v-btn>
                </div>
            </v-container>
        </v-app-bar>

        <!-- Main Header with Logo and Search -->
        <v-app-bar class="bg-white" height="80">
            <v-container class="d-flex align-center">
                <!-- Mobile Menu Button -->
                <v-app-bar-nav-icon class="d-flex d-sm-none" @click="drawer = !drawer"></v-app-bar-nav-icon>

                <!-- Logo -->
                <Link :href="route('index')" class="mr-4">
                <v-img src="/images/logo.png" width="180" height="60" contain></v-img>
                </Link>

                <!-- Search Bar -->
                <v-text-field v-model="search" density="compact" placeholder="Search products..."
                    append-inner-icon="mdi-magnify" single-line hide-details variant="outlined"
                    class="search-field mx-4" @keyup.enter="searchCallback" @click:append-inner="searchCallback">
                </v-text-field>

                <!-- Cart & Account -->
                <div class="d-flex align-center">
                    <v-btn prepend-icon="mdi-heart-outline" variant="text" class="mr-2">
                        <span class="d-none d-sm-block">Wishlist</span>
                        <template v-slot:append>
                            <v-badge color="error" content="0" floating></v-badge>
                        </template>
                    </v-btn>

                    <v-btn prepend-icon="mdi-cart-outline" variant="text" class="mr-2" :href="route('cart.index')">
                        <span class="d-none d-sm-block">Cart</span>
                        <template v-slot:append>
                            <v-badge color="error" :content="cartCount" :model-value="cartCount > 0" floating></v-badge>
                        </template>
                    </v-btn>

                    <!-- User Account Menu -->
                    <template v-if="$page.props.auth.user">
                        <v-menu location="bottom end">
                            <template v-slot:activator="{ props }">
                                <v-btn variant="text" v-bind="props" prepend-icon="mdi-account-circle">
                                    <span class="d-none d-sm-block">{{ $page.props.auth.user.name }}</span>
                                </v-btn>
                            </template>
                            <v-list>
                                <v-list-item :href="route('profile.edit')" link>
                                    <v-list-item-title>Profile</v-list-item-title>
                                </v-list-item>
                                <v-list-item @click="logout" link>
                                    <v-list-item-title>Logout</v-list-item-title>
                                </v-list-item>
                            </v-list>
                        </v-menu>
                    </template>
                    <template v-else>
                        <v-btn variant="text" :href="route('login')" class="mr-2">
                            <span>Login</span>
                        </v-btn>
                        <v-btn color="primary" :href="route('register')">
                            <span>Register</span>
                        </v-btn>
                    </template>
                </div>
            </v-container>
        </v-app-bar>

        <!-- Navigation Bar with Categories -->
        <v-app-bar class="bg-grey-lighten-4" height="48">
            <v-container class="d-flex align-center">
                <!-- All Categories Dropdown -->
                <v-menu open-on-hover>
                    <template v-slot:activator="{ props }">
                        <v-btn color="primary" v-bind="props" class="text-none mr-4" prepend-icon="mdi-menu">
                            All Categories
                        </v-btn>
                    </template>
                    <v-list width="250">
                        <template v-for="(category, index) in categories" :key="index">
                            <Link v-if="!category.children?.length" :href="route('categories.show', category.slug)"
                                :title="category.name">
                            <v-list-item link>
                                {{ category.name }}
                            </v-list-item>
                            </Link>

                            <v-list-group v-else>
                                <template v-slot:activator="{ props }">
                                    <v-list-item v-bind="props" :title="category.name">
                                        <template v-slot:append>
                                            <v-icon>mdi-chevron-right</v-icon>
                                        </template>
                                    </v-list-item>
                                </template>
                                <Link v-for="child in category.children" :key="child.id"
                                    :href="route('categories.show', child.slug)" :title="child.name">
                                <v-list-item link density="compact">
                                    {{ child.name }}
                                </v-list-item>
                                </Link>
                            </v-list-group>
                        </template>
                    </v-list>
                </v-menu>

                <!-- Main Navigation Links -->
                <div class="d-none d-sm-flex">
                    <v-btn variant="text" class="text-none">
                        <Link :href="route('index')">
                        Home
                        </Link>
                    </v-btn>
                    <template v-for="(category, index) in categories.slice(0, 6)" :key="index">
                        <v-btn variant="text" class="text-none">
                            <Link :href="route('categories.show', category.slug)">
                            {{ category.name }}
                            </Link>
                        </v-btn>
                    </template>
                </div>
            </v-container>
        </v-app-bar>

        <!-- Navigation Drawer -->
        <v-navigation-drawer v-model="drawer" temporary>
            <v-list>
                <v-list-item prepend-avatar="/images/logo.png" :title="'Tech Store'"></v-list-item>
            </v-list>

            <v-divider></v-divider>

            <!-- Main Menu -->
            <v-list density="compact" nav>
                <v-list-item v-for="(item, i) in menuItems" :key="i" :value="item" :to="item.route" link>
                    <template v-slot:prepend>
                        <v-icon :icon="item.icon"></v-icon>
                    </template>
                    <v-list-item-title>{{ item.text }}</v-list-item-title>
                </v-list-item>
            </v-list>

            <v-divider class="my-2"></v-divider>

            <!-- All Categories Menu -->
            <v-list nav>
                <v-list-subheader>Shop By Category</v-list-subheader>
                <template v-for="(category, index) in categories" :key="index">
                    <v-list-group v-if="category.children?.length > 0" :value="category.name">
                        <template v-slot:activator="{ props }">
                            <v-list-item v-bind="props" :prepend-icon="category.icon || 'mdi-shape-outline'"
                                :title="category.name">
                            </v-list-item>
                        </template>

                        <Link v-for="child in category.children" :key="child.id"
                            :href="route('categories.show', child.slug)" :title="child.name">
                        <v-list-item link density="compact">
                            {{ child.name }}
                        </v-list-item>
                        </Link>
                    </v-list-group>

                    <Link v-else :href="route('categories.show', category.slug)" :title="category.name">
                    <v-list-item link :prepend-icon="category.icon || 'mdi-shape-outline'">
                        {{ category.name }}
                    </v-list-item>
                    </Link>
                </template>
            </v-list>
        </v-navigation-drawer>

        <!-- Main Content -->
        <v-main>
            <slot />
        </v-main>

        <!-- Footer -->
        <v-footer class="bg-grey-lighten-3">
            <v-container>
                <v-row>
                    <v-col cols="12" md="3">
                        <h3>Contact</h3>
                        <p class="text-body-2">
                            123 Street Name<br>
                            City, State 12345<br>
                            Phone: (123) 456-7890<br>
                            Email: info@techstore.com
                        </p>
                    </v-col>

                    <v-col cols="12" md="3">
                        <h3>Quick Links</h3>
                        <v-list density="compact" nav>
                            <v-list-item>About Us</v-list-item>
                            <v-list-item>Contact</v-list-item>
                            <v-list-item>Terms & Conditions</v-list-item>
                        </v-list>
                    </v-col>

                    <v-col cols="12" md="3">
                        <h3>Categories</h3>
                        <v-list density="compact" nav>
                            <v-list-item v-for="(category, i) in categories.slice(0, 4)" :key="i">
                                {{ category.name }}
                            </v-list-item>
                        </v-list>
                    </v-col>

                    <v-col cols="12" md="3">
                        <h3>Follow Us</h3>
                        <div class="d-flex gap-2 mt-2">
                            <v-btn icon="mdi-facebook" variant="text"></v-btn>
                            <v-btn icon="mdi-twitter" variant="text"></v-btn>
                            <v-btn icon="mdi-instagram" variant="text"></v-btn>
                        </div>
                    </v-col>

                    <v-col cols="12" class="text-center mt-4">
                        <small>&copy; {{ new Date().getFullYear() }} Tech Store. All rights reserved.</small>
                    </v-col>
                </v-row>
            </v-container>
        </v-footer>
    </v-app>
</template>

<script setup>
    import { ref, onMounted, computed, watch } from 'vue'
    import { router, usePage } from '@inertiajs/vue3'

    const drawer = ref(false)
    const tab = ref('menu')
    const search = ref('')
    const categories = ref([])

    // Updated menu items (removed categories since they'll be dynamic)
    const menuItems = [
        { text: 'New Arrivals', icon: 'mdi-star', route: '/new' },
        { text: 'Best Sellers', icon: 'mdi-fire', route: '/best' },
        { text: 'Special Offers', icon: 'mdi-tag', route: '/offers' },
        { text: 'Brands', icon: 'mdi-tag-multiple', route: '/brands' }
    ]

    // Get categories
    const getCategories = async () => {
        try {
            const { data } = await axios.get(route("data.categories"));
            if (data.success) {
                categories.value = data.data;
            }
        } catch (error) {
            console.error(error);
        }
    };

    onMounted(() => {
        getCategories()
    })

    const searchCallback = () => {
        if (!search.value.trim()) return  // Check for empty or whitespace-only search
        router.visit(route('search', {
            searchTerm: search.value.trim()
        }), {
            preserveScroll: true
        })
    }

    const cartCount = computed(() => {
        return usePage().props.cartCount || 0;
    });

    const showMessage = ref(false);
    const message = ref('');
    const messageType = ref('');

    // Watch for flash messages
    watch(() => usePage().props.flash, (flash) => {
        if (flash.success) {
            message.value = flash.success;
            messageType.value = 'success';
            showMessage.value = true;
        }
        if (flash.error) {
            message.value = flash.error;
            messageType.value = 'error';
            showMessage.value = true;
        }
    }, { deep: true });

    // Add logout function
    const logout = () => {
        router.post(route('logout'));
    };
</script>

<style scoped>
    .search-field {
        max-width: 500px;
    }

    .search-field :deep(.v-field) {
        border-radius: 4px;
    }

    :deep(.v-btn) {
        text-transform: none;
        letter-spacing: 0;
    }

    .search-field {
        max-width: 500px;
        border-radius: 8px;
    }

    .search-field :deep(.v-field__outline__start),
    .search-field :deep(.v-field__outline__end) {
        border-radius: 8px;
    }

    :deep(.v-btn) {
        text-transform: none;
        letter-spacing: 0;
    }

    .header-bar {
        background: #fff;
        border-bottom: 1px solid #eee;
    }

    .top-bar {
        min-height: 80px;
        border-bottom: 1px solid #eee;
    }

    .nav-bar {
        min-height: 50px;
        background: #f8f9fa;
    }

    .search-field {
        max-width: 600px;
        margin: 0 auto;
    }

    .search-field :deep(.v-field__outline__start) {
        border-radius: 20px 0 0 20px;
    }

    .search-field :deep(.v-field__outline__end) {
        border-radius: 0 20px 20px 0;
    }

    .categories-btn {
        height: 40px;
        border-radius: 4px;
        text-transform: none;
        font-weight: 500;
    }

    .nav-link {
        color: #333;
        text-transform: none;
        font-weight: 400;
        letter-spacing: 0;
    }

    /* Mobile Styles */
    @media (max-width: 960px) {
        .top-bar {
            min-height: 60px;
        }

        .search-field {
            display: none;
        }
    }

    /* Dropdown Menu Styles */
    :deep(.v-list) {
        border-radius: 4px;
        padding: 8px 0;
    }

    :deep(.v-list-item) {
        min-height: 40px;
        padding: 0 16px;
    }

    :deep(.v-list-item:hover) {
        background: #f5f5f5;
    }

    /* Footer Styles */
    .v-footer {
        background: #f8f9fa;
        border-top: 1px solid #eee;
    }

    .footer-title {
        font-size: 1.1rem;
        font-weight: 500;
        margin-bottom: 1rem;
    }

    .footer-link {
        color: #666;
        text-decoration: none;
        display: block;
        padding: 4px 0;
    }

    .footer-link:hover {
        color: var(--v-primary-base);
    }

    /* Category Menu Styles */
    :deep(.v-list-group__items) {
        margin-left: 12px;
    }

    :deep(.v-list-item--density-compact) {
        min-height: 32px;
    }

    :deep(.v-list-group__items .v-list-item) {
        padding-left: 24px;
    }

    /* Add these new styles */
    .v-btn.text-none {
        text-transform: none;
        font-weight: 400;
        letter-spacing: 0;
        margin-right: 4px;
    }

    :deep(.v-list-item--density-compact) {
        min-height: 32px;
    }
</style>
