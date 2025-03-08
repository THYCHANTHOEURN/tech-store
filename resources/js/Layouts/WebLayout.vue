<template>
    <v-app>
        <v-snackbar v-model="showMessage" :color="messageType" :timeout="3000" location="top">
            {{ message }}
        </v-snackbar>

        <!-- Top Bar with Contact Info -->
        <v-app-bar class="bg-primary" density="compact" :elevation="0">
            <v-container class="d-flex align-center justify-space-between py-1">
                <div class="d-none d-sm-flex align-center">
                    <span class="text-white mr-4 text-caption">
                        <v-icon size="small" class="mr-1">mdi-phone</v-icon>
                        +855 12 345 678
                    </span>
                    <span class="text-white text-caption">
                        <v-icon size="small" class="mr-1">mdi-email</v-icon>
                        info@techstore.com
                    </span>
                </div>
                <div class="d-flex align-center">
                    <v-btn variant="text" class="text-white icon-btn" href="https://www.facebook.com/" target="_blank">
                        <v-icon size="small">mdi-facebook</v-icon>
                    </v-btn>
                    <v-btn variant="text" class="text-white icon-btn" href="https://www.tiktok.com/" target="_blank">
                        <v-icon size="small">mdi-music-box-outline</v-icon>
                    </v-btn>
                    <v-btn variant="text" class="text-white icon-btn" href="https://www.instagram.com/" target="_blank">
                        <v-icon size="small">mdi-instagram</v-icon>
                    </v-btn>
                    <v-btn variant="text" class="text-white icon-btn" href="https://t.me/" target="_blank">
                        <v-icon size="small">mdi-send</v-icon>
                    </v-btn>
                    <v-btn variant="text" class="text-white icon-btn" href="https://www.youtube.com/" target="_blank">
                        <v-icon size="small">mdi-youtube</v-icon>
                    </v-btn>
                </div>
            </v-container>
        </v-app-bar>

        <!-- Main Header with Logo and Search -->
        <v-app-bar class="bg-white" :height="$vuetify.display.xs ? 70 : 80" :elevation="1">
            <v-container class="px-1 px-sm-2 px-md-4 py-0">
                <div class="d-flex align-center justify-space-between w-100">
                    <!-- Left Side: Mobile Menu Button + Logo -->
                    <div class="d-flex align-center">
                        <!-- Mobile Menu Button - More compact on xs -->
                        <v-app-bar-nav-icon class="d-flex d-md-none" @click="drawer = !drawer" density="compact"
                            :size="$vuetify.display.xs ? 'small' : 'default'" aria-label="Menu"></v-app-bar-nav-icon>

                        <!-- Logo - More responsive sizing -->
                        <Link :href="route('index')" class="header-logo ml-1 ml-sm-2">
                        <v-img src="/images/logo.png"
                            :width="$vuetify.display.xs ? 120 : $vuetify.display.sm ? 150 : 180"
                            :height="$vuetify.display.xs ? 40 : $vuetify.display.sm ? 50 : 60" contain class="my-1"
                            alt="Tech Store Logo"></v-img>
                        </Link>
                    </div>

                    <!-- Center: Search Bar (visible on md and up) -->
                    <v-text-field v-model="search" density="compact" placeholder="Search products..."
                        append-inner-icon="mdi-magnify" single-line hide-details variant="outlined"
                        class="search-field mx-2 mx-md-4 d-none d-md-block" @keyup.enter="searchCallback"
                        @click:append-inner="searchCallback"></v-text-field>

                    <!-- Right Side: Action buttons - Better spacing & touch targets -->
                    <div class="d-flex align-center header-actions">
                        <!-- Mobile search button -->
                        <v-btn icon class="d-md-none action-btn-touch" @click="showMobileSearch = true"
                            :size="$vuetify.display.xs ? 'small' : 'default'" aria-label="Search">
                            <v-icon>mdi-magnify</v-icon>
                        </v-btn>

                        <!-- Wishlist Button with Badge - Icon only on xs -->
                        <v-btn :icon="$vuetify.display.xs" variant="text" class="action-btn"
                            :href="route('wishlist.index')" :size="$vuetify.display.xs ? 'small' : 'default'"
                            aria-label="Wishlist">
                            <v-icon :size="$vuetify.display.xs ? 'default' : 'small'"
                                class="mr-1">mdi-heart-outline</v-icon>
                            <span class="d-none d-sm-block">Wishlist</span>
                            <template v-slot:append>
                                <v-badge color="error" :content="wishlistCount" :model-value="wishlistCount > 0"
                                    floating location="top end" :offset-x="$vuetify.display.xs ? 8 : 2"
                                    :offset-y="$vuetify.display.xs ? 8 : 2"
                                    :size="$vuetify.display.xs ? 'x-small' : 'small'"></v-badge>
                            </template>
                        </v-btn>

                        <!-- Cart Button with Badge - Icon only on xs -->
                        <v-btn :icon="$vuetify.display.xs" variant="text" class="action-btn" :href="route('cart.index')"
                            :size="$vuetify.display.xs ? 'small' : 'default'" aria-label="Cart">
                            <v-icon :size="$vuetify.display.xs ? 'default' : 'small'"
                                class="mr-1">mdi-cart-outline</v-icon>
                            <span class="d-none d-sm-block">Cart</span>
                            <template v-slot:append>
                                <v-badge color="error" :content="cartCount" :model-value="cartCount > 0" floating
                                    location="top end" :offset-x="$vuetify.display.xs ? 8 : 2"
                                    :offset-y="$vuetify.display.xs ? 8 : 2"
                                    :size="$vuetify.display.xs ? 'x-small' : 'small'"></v-badge>
                            </template>
                        </v-btn>

                        <!-- User Account Menu with enhanced touch target -->
                        <template v-if="$page.props.auth.user">
                            <v-menu location="bottom end" :close-on-content-click="true" offset="5">
                                <template v-slot:activator="{ props }">
                                    <v-btn :icon="$vuetify.display.xs" variant="text" v-bind="props" class="action-btn"
                                        :size="$vuetify.display.xs ? 'small' : 'default'" aria-label="Account">
                                        <v-icon :size="$vuetify.display.xs ? 'default' : 'small'"
                                            class="mr-1">mdi-account-circle</v-icon>
                                        <span class="d-none d-sm-block text-truncate account-name">
                                            {{ $page.props.auth.user.name }}
                                        </span>
                                    </v-btn>
                                </template>
                                <v-card min-width="200" class="mt-1">
                                    <v-list density="compact">
                                        <v-list-item :href="route('profile.edit')" link prepend-icon="mdi-account-edit">
                                            <v-list-item-title>Profile</v-list-item-title>
                                        </v-list-item>
                                        <v-list-item @click="logout" link prepend-icon="mdi-logout">
                                            <v-list-item-title>Logout</v-list-item-title>
                                        </v-list-item>
                                    </v-list>
                                </v-card>
                            </v-menu>
                        </template>

                        <!-- Auth buttons with better mobile design -->
                        <template v-else>
                            <v-btn v-if="!$vuetify.display.xs" variant="text" :href="route('login')" class="action-btn">
                                <span>Login</span>
                            </v-btn>
                            <v-btn v-if="!$vuetify.display.xs" color="primary" :href="route('register')"
                                class="action-btn">
                                <span>Register</span>
                            </v-btn>
                            <v-btn v-else icon @click="showAuthOptions = true"
                                :size="$vuetify.display.xs ? 'small' : 'default'" aria-label="Account options">
                                <v-icon>mdi-account</v-icon>
                            </v-btn>
                        </template>
                    </div>
                </div>
            </v-container>
        </v-app-bar>

        <!-- Navigation Bar with Categories - Hide on xs and improved scrolling -->
        <v-app-bar class="bg-grey-lighten-4 d-none d-sm-flex" height="48" :elevation="0">
            <v-container class="d-flex align-center px-2 px-sm-4">
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

                <!-- Main Navigation Links - Scrollable on sm -->
                <div class="d-none d-sm-flex nav-links-container">
                    <v-btn variant="text" class="text-none nav-link">
                        <Link :href="route('index')">
                        Home
                        </Link>
                    </v-btn>
                    <template v-for="(category, index) in categories.slice(0, 6)" :key="index">
                        <v-btn variant="text" class="text-none nav-link">
                            <Link :href="route('categories.show', category.slug)">
                            {{ category.name }}
                            </Link>
                        </v-btn>
                    </template>
                </div>
            </v-container>
        </v-app-bar>

        <!-- Navigation Drawer - Enhanced for mobile -->
        <v-navigation-drawer v-model="drawer" temporary :width="280">
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
                <v-list-item :href="route('wishlist.index')" link>
                    <template v-slot:prepend>
                        <v-icon>mdi-heart-outline</v-icon>
                    </template>
                    <v-list-item-title>
                        Wishlist
                        <v-badge v-if="wishlistCount > 0" color="error" :content="wishlistCount" inline></v-badge>
                    </v-list-item-title>
                </v-list-item>
            </v-list>

            <v-divider class="my-2"></v-divider>

            <!-- All Categories Menu -->
            <v-list nav>
                <v-list-subheader>Shop By Category</v-list-subheader>
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
        </v-navigation-drawer>

        <!-- Main Content -->
        <v-main>
            <slot />
        </v-main>

        <!-- Mobile search modal - Improved & optimized -->
        <v-dialog v-model="showMobileSearch" fullscreen transition="dialog-bottom-transition"
            class="mobile-search-dialog">
            <v-card>
                <v-toolbar color="primary" dark>
                    <v-text-field v-model="searchTerm" placeholder="Search products..." variant="outlined"
                        density="compact" hide-details bg-color="white" class="mobile-search-field" autofocus
                        @keyup.enter="searchMobile"></v-text-field>

                    <v-btn icon @click="showMobileSearch = false" aria-label="Close search">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-toolbar>
            </v-card>
        </v-dialog>

        <!-- Mobile auth options dialog with better UX -->
        <v-dialog v-model="showAuthOptions" max-width="300">
            <v-card>
                <v-card-title class="text-center pb-0">Account</v-card-title>
                <v-card-text>
                    <v-row>
                        <v-col cols="6">
                            <v-btn block color="primary" variant="elevated" :href="route('login')"
                                class="mb-2">Login</v-btn>
                        </v-col>
                        <v-col cols="6">
                            <v-btn block color="secondary" variant="elevated" :href="route('register')">Register</v-btn>
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>
        </v-dialog>

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
    const showAuthOptions = ref(false)

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

    const wishlistCount = computed(() => {
        return usePage().props.wishlistCount || 0;
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

    const searchTerm = ref('');
    const showMobileSearch = ref(false);

    // Function to handle searching
    const searchProducts = () => {
        if (searchTerm.value.trim()) {
            router.visit(`/search?searchTerm=${encodeURIComponent(searchTerm.value.trim())}`);
        }
    };

    // Function to handle mobile search
    const searchMobile = () => {
        if (searchTerm.value.trim()) {
            showMobileSearch.value = false;
            router.visit(`/search?searchTerm=${encodeURIComponent(searchTerm.value.trim())}`);
        }
    };
</script>

<style scoped>

    /* Core Layout - Professional responsive optimization */
    .header-logo {
        display: flex;
        align-items: center;
    }

    /* Top bar styles - Optimized for touch */
    .icon-btn {
        min-width: 32px;
        height: 32px;
        padding: 0;
    }

    /* Modern search field with consistent styling */
    .search-field {
        flex-grow: 1;
        max-width: 600px;
        border-radius: 8px;
        margin: 0 8px;
    }

    .search-field :deep(.v-field__outline__start) {
        border-radius: 20px 0 0 20px;
    }

    .search-field :deep(.v-field__outline__end) {
        border-radius: 0 20px 20px 0;
    }

    /* Header action buttons - Optimized spacing */
    .header-actions {
        gap: 4px;
    }

    .action-btn {
        margin: 0;
        min-width: unset;
    }

    /* Action button touch areas */
    .action-btn-touch {
        margin: 0;
        width: 40px;
        height: 40px;
    }

    /* Account name truncation */
    .account-name {
        max-width: 100px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Navigation styles - Improved scrolling */
    .nav-links-container {
        display: flex;
        overflow-x: auto;
        scrollbar-width: none;
        -ms-overflow-style: none;
        -webkit-overflow-scrolling: touch;
    }

    .nav-links-container::-webkit-scrollbar {
        display: none;
    }

    .nav-link {
        white-space: nowrap;
        color: #333;
        text-transform: none;
        font-weight: 400;
        margin-right: 4px;
        height: 38px;
    }

    /* Mobile search styles - Improved UI */
    .mobile-search-field {
        flex: 1;
        margin-right: 8px;
    }

    .mobile-search-field :deep(.v-field__outline__start),
    .mobile-search-field :deep(.v-field__outline__end) {
        border-radius: 8px;
    }

    /* Global button styles */
    :deep(.v-btn) {
        text-transform: none;
        letter-spacing: 0;
    }

    /* Responsive breakpoints - Professional cascade */
    @media (max-width: 599px) {
        .v-container {
            padding-left: 8px !important;
            padding-right: 8px !important;
        }

        .header-actions {
            gap: 2px;
        }

        .action-btn.v-btn--icon {
            margin: 0;
            height: 36px;
            width: 36px;
            min-width: 36px;
        }
    }

    @media (min-width: 600px) {
        .header-actions {
            gap: 8px;
        }

        .action-btn {
            margin: 0 2px;
        }

        .account-name {
            max-width: 120px;
        }
    }

    @media (min-width: 960px) {
        .search-field {
            margin: 0 16px;
        }

        .account-name {
            max-width: 150px;
        }
    }

    @media (min-width: 1264px) {
        .search-field {
            margin: 0 24px;
            max-width: 600px;
        }

        .account-name {
            max-width: 200px;
        }

        .header-actions {
            gap: 12px;
        }
    }

    /* Footer Styles */
    .v-footer {
        background: #f8f9fa;
        border-top: 1px solid #eee;
    }

    /* Category Menu Styles */
    :deep(.v-list-group__items) {
        margin-left: 12px;
    }

    :deep(.v-list-item--density-compact) {
        min-height: 32px;
    }
</style>
