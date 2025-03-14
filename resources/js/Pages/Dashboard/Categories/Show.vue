<template>

    <Head :title="category.name" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Category Details: {{ category.name }}
                </h2>
                <v-spacer></v-spacer>
                <Link :href="route('dashboard.categories.edit', category.uuid)">
                <v-btn color="warning" prepend-icon="mdi-pencil" class="mr-2">
                    Edit
                </v-btn>
                </Link>
                <Link :href="route('dashboard.categories.index')">
                <v-btn color="secondary" prepend-icon="mdi-arrow-left" variant="outlined">
                    Back to Categories
                </v-btn>
                </Link>
            </div>
        </template>

        <v-container fluid class="py-8">
            <v-row>
                <!-- Category Details -->
                <v-col cols="12" md="6">
                    <v-card>
                        <v-card-title>
                            <v-icon class="mr-2">mdi-folder-information</v-icon>
                            Category Information
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <v-list lines="two">
                                <v-list-item>
                                    <v-list-item-title>Name</v-list-item-title>
                                    <v-list-item-subtitle>{{ category.name }}</v-list-item-subtitle>
                                </v-list-item>

                                <v-list-item v-if="category.parent">
                                    <v-list-item-title>Parent Category</v-list-item-title>
                                    <v-list-item-subtitle>{{ category.parent.name }}</v-list-item-subtitle>
                                </v-list-item>
                                <v-list-item v-else>
                                    <v-list-item-title>Parent Category</v-list-item-title>
                                    <v-list-item-subtitle class="text-grey">Root Category</v-list-item-subtitle>
                                </v-list-item>

                                <v-list-item>
                                    <v-list-item-title>Status</v-list-item-title>
                                    <v-list-item-subtitle>
                                        <v-chip :color="category.status ? 'success' : 'error'" size="small">
                                            {{ category.status ? 'Active' : 'Inactive' }}
                                        </v-chip>
                                    </v-list-item-subtitle>
                                </v-list-item>

                                <v-list-item>
                                    <v-list-item-title>Slug</v-list-item-title>
                                    <v-list-item-subtitle>{{ category.slug }}</v-list-item-subtitle>
                                </v-list-item>

                                <v-list-item>
                                    <v-list-item-title>Created At</v-list-item-title>
                                    <v-list-item-subtitle>
                                        {{ new Date(category.created_at).toLocaleString() }}
                                    </v-list-item-subtitle>
                                </v-list-item>

                                <v-list-item>
                                    <v-list-item-title>Updated At</v-list-item-title>
                                    <v-list-item-subtitle>
                                        {{ new Date(category.updated_at).toLocaleString() }}
                                    </v-list-item-subtitle>
                                </v-list-item>
                            </v-list>
                        </v-card-text>
                    </v-card>
                </v-col>

                <!-- Category Image -->
                <v-col cols="12" md="6">
                    <v-card>
                        <v-card-title>
                            <v-icon class="mr-2">mdi-image</v-icon>
                            Category Image
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text class="text-center py-6">
                            <v-img :src="category.image_url" max-height="300" contain class="rounded"></v-img>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Category Description -->
            <v-row class="mt-4">
                <v-col cols="12">
                    <v-card>
                        <v-card-title>
                            <v-icon class="mr-2">mdi-text-box</v-icon>
                            Description
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text class="py-4">
                            <p v-if="category.description">{{ category.description }}</p>
                            <p v-else class="text-grey">No description provided</p>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Child Categories -->
            <v-row v-if="category.children && category.children.length > 0" class="mt-4">
                <v-col cols="12">
                    <v-card>
                        <v-card-title>
                            <v-icon class="mr-2">mdi-folder-multiple</v-icon>
                            Sub-Categories ({{ category.children.length }})
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <v-row>
                                <v-col v-for="child in category.children" :key="child.id" cols="12" sm="6" md="4"
                                    lg="3">
                                    <v-card variant="outlined" class="mb-2">
                                        <v-card-item>
                                            <v-img v-if="child.image_url" :src="child.image_url" height="100" cover
                                                class="mb-2 rounded"></v-img>
                                            <v-card-title class="text-subtitle-1">{{ child.name }}</v-card-title>
                                            <v-chip :color="child.status ? 'success' : 'error'" size="small"
                                                class="mb-2">
                                                {{ child.status ? 'Active' : 'Inactive' }}
                                            </v-chip>
                                        </v-card-item>
                                        <v-card-actions>
                                            <Link :href="route('dashboard.categories.show', child.uuid)"
                                                class="text-decoration-none">
                                            <v-btn variant="text" color="primary" block>View Details</v-btn>
                                            </Link>
                                        </v-card-actions>
                                    </v-card>
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Products Table -->
            <v-row class="mt-4">
                <v-col cols="12">
                    <v-card>
                        <v-card-title>
                            <v-icon class="mr-2">mdi-package-variant-closed</v-icon>
                            Products in this Category
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <div v-if="products.data.length > 0">
                                <v-data-table :headers="productHeaders" :items="products.data" class="elevation-1">
                                    <template v-slot:item.primary_image_url="{ item }">
                                        <div class="d-flex align-center py-2">
                                            <v-img :src="item.primary_image_url" :alt="item.name" width="50" height="50"
                                                class="rounded" cover></v-img>
                                        </div>
                                    </template>

                                    <template v-slot:item.price="{ item }">
                                        ${{ item.price }}
                                    </template>

                                    <template v-slot:item.status="{ item }">
                                        <v-chip :color="item.status ? 'success' : 'error'" size="small">
                                            {{ item.status ? 'Active' : 'Inactive' }}
                                        </v-chip>
                                    </template>

                                    <template v-slot:item.actions="{ item }">
                                        <div class="d-flex flex-nowrap justify-center justify-sm-end">
                                            <Link :href="route('dashboard.products.show', item.uuid)"
                                                class="text-decoration-none">
                                            <v-btn icon size="x-small" color="info" class="mr-1" title="View"
                                                rounded="lg">
                                                <v-icon>mdi-eye</v-icon>
                                            </v-btn>
                                            </Link>
                                            <Link :href="route('dashboard.products.edit', item.uuid)"
                                                class="text-decoration-none">
                                            <v-btn icon size="x-small" color="warning" class="mr-1" title="Edit"
                                                rounded="lg">
                                                <v-icon>mdi-pencil</v-icon>
                                            </v-btn>
                                            </Link>
                                        </div>
                                    </template>
                                </v-data-table>

                                <!-- Pagination -->
                                <div class="d-flex justify-center py-4">
                                    <v-pagination v-if="products.last_page > 1" v-model="page"
                                        :length="products.last_page" total-visible="7"
                                        @update:model-value="changePage"></v-pagination>
                                </div>
                            </div>
                            <div v-else class="text-center py-6">
                                <v-icon size="large" color="grey-lighten-1"
                                    class="mb-4">mdi-package-variant-closed</v-icon>
                                <p class="text-h6 text-grey">No products in this category</p>
                                <p class="text-subtitle-1 text-grey-darken-1">You can add products to this category from
                                    the
                                    Products section</p>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </DashboardLayout>
</template>

<script setup>
    import DashboardLayout from '@/Layouts/DashboardLayout.vue';
    import { Head, Link, router } from '@inertiajs/vue3';
    import { ref } from 'vue';

    const props = defineProps({
        category: Object,
        products: Object
    });

    // Pagination
    const page = ref(props.products.current_page || 1);

    const productHeaders = [
        { title: 'Image', key: 'primary_image_url', sortable: false },
        { title: 'Name', key: 'name', sortable: true },
        { title: 'Brand', key: 'brand.name', sortable: true },
        { title: 'Price', key: 'price', sortable: true },
        { title: 'Stock', key: 'stock', sortable: true },
        { title: 'Status', key: 'status', sortable: true },
        { title: 'Actions', key: 'actions', sortable: false, align: 'end' },
    ];

    // Pagination function
    const changePage = (newPage) => {
        router.get(route('dashboard.categories.show', props.category.uuid), {
            page: newPage,
        }, {
            preserveState: true,
            replace: true
        });
    };
</script>

<style scoped>
    .border-primary {
        border: 2px solid var(--v-primary-base);
    }
</style>
