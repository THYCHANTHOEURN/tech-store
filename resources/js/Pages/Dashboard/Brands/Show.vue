<template>
    <Head :title="brand.name" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Brand Details: {{ brand.name }}
                </h2>
                <v-spacer></v-spacer>
                <Link :href="route('dashboard.brands.edit', brand.uuid)" class="text-decoration-none">
                <v-btn color="warning" prepend-icon="mdi-pencil" class="mx-2">
                    Edit
                </v-btn>
                </Link>
                <Link :href="route('dashboard.brands.index')">
                <v-btn color="secondary" prepend-icon="mdi-arrow-left" variant="outlined">
                    Back to Brands
                </v-btn>
                </Link>
            </div>
        </template>

        <v-container fluid class="py-8">
            <!-- Brand Info Card -->
            <v-row>
                <v-col cols="12" md="4">
                    <v-card class="mb-4">
                        <v-card-title>Logo</v-card-title>
                        <v-card-text>
                            <v-img :src="brand.logo_url" height="200" contain class="bg-grey-lighten-2 rounded"></v-img>
                        </v-card-text>
                    </v-card>
                </v-col>

                <v-col cols="12" md="8">
                    <v-card class="mb-4">
                        <v-card-title>Brand Information</v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <v-row>
                                <v-col cols="12" md="6">
                                    <v-list>
                                        <v-list-item>
                                            <v-list-item-title>Name</v-list-item-title>
                                            <v-list-item-subtitle>{{ brand.name }}</v-list-item-subtitle>
                                        </v-list-item>

                                        <v-list-item>
                                            <v-list-item-title>Status</v-list-item-title>
                                            <v-list-item-subtitle>
                                                <v-chip :color="brand.status ? 'success' : 'error'" size="small">
                                                    {{ brand.status ? 'Active' : 'Inactive' }}
                                                </v-chip>
                                            </v-list-item-subtitle>
                                        </v-list-item>
                                    </v-list>
                                </v-col>
                                <v-col cols="12" md="6">
                                    <v-list>
                                        <v-list-item>
                                            <v-list-item-title>Created</v-list-item-title>
                                            <v-list-item-subtitle>{{ brand.created_at }}</v-list-item-subtitle>
                                        </v-list-item>

                                        <v-list-item>
                                            <v-list-item-title>Last Updated</v-list-item-title>
                                            <v-list-item-subtitle>{{ brand.updated_at }}</v-list-item-subtitle>
                                        </v-list-item>
                                    </v-list>
                                </v-col>
                            </v-row>

                            <div v-if="brand.description" class="mt-4">
                                <h3 class="text-subtitle-1 mb-2">Description:</h3>
                                <p>{{ brand.description }}</p>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Brand Products Table -->
            <v-row>
                <v-col cols="12">
                    <v-card>
                        <v-card-title>
                            <div class="d-flex align-center">
                                <span>Products ({{ products.total }})</span>
                                <v-spacer></v-spacer>
                                <Link :href="route('dashboard.products.create')" class="text-decoration-none">
                                    <v-btn color="primary" size="small" prepend-icon="mdi-plus">Add Product</v-btn>
                                </Link>
                            </div>
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text class="pa-0">
                            <v-data-table :headers="productHeaders" :items="products.data" class="elevation-0">
                                <template v-slot:item.image="{ item }">
                                    <div class="d-flex align-center py-2">
                                        <v-img :src="item.primary_image_url" width="60" height="60" cover class="rounded"></v-img>
                                    </div>
                                </template>

                                <template v-slot:item.price="{ item }">
                                    <div>
                                        <div v-if="item.sale_price" class="text-decoration-line-through text-grey text-caption">
                                            ${{ item.price }}
                                        </div>
                                        <div :class="{ 'text-error': item.sale_price }">
                                            ${{ item.sale_price || item.price }}
                                        </div>
                                    </div>
                                </template>

                                <template v-slot:item.stock="{ item }">
                                    <v-chip :color="item.stock > 0 ? 'success' : 'error'" size="small">
                                        {{ item.stock > 0 ? 'In Stock' : 'Out of Stock' }}
                                    </v-chip>
                                </template>

                                <template v-slot:item.status="{ item }">
                                    <v-chip :color="item.status ? 'success' : 'error'" size="small">
                                        {{ item.status ? 'Active' : 'Inactive' }}
                                    </v-chip>
                                </template>

                                <template v-slot:item.actions="{ item }">
                                    <div class="d-flex flex-nowrap justify-center justify-sm-end">
                                        <Link :href="route('dashboard.products.show', item.uuid)" class="text-decoration-none">
                                        <v-btn icon size="x-small" color="info" class="mr-1" title="View" rounded="lg">
                                            <v-icon>mdi-eye</v-icon>
                                        </v-btn>
                                        </Link>

                                        <Link :href="route('dashboard.products.edit', item.uuid)" class="text-decoration-none">
                                        <v-btn icon size="x-small" color="warning" class="mr-1" title="Edit" rounded="lg">
                                            <v-icon>mdi-pencil</v-icon>
                                        </v-btn>
                                        </Link>
                                    </div>
                                </template>
                            </v-data-table>

                            <div class="d-flex justify-center py-4">
                                <v-pagination v-if="products.last_page > 1" v-model="currentPage" :length="products.last_page"
                                    @update:model-value="changePage" rounded></v-pagination>
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
        brand: Object,
        products: Object
    });

    const currentPage = ref(props.products?.current_page || 1);

    const productHeaders = [
        { title: 'Image', key: 'image', sortable: false },
        { title: 'Name', key: 'name' },
        { title: 'Category', key: 'category.name' },
        { title: 'Price', key: 'price' },
        { title: 'Stock', key: 'stock' },
        { title: 'Status', key: 'status' },
        { title: 'Actions', key: 'actions', sortable: false, align: 'end' },
    ];

    const changePage = (newPage) => {
        router.get(route('dashboard.brands.show', props.brand.uuid), {
            page: newPage
        }, {
            preserveState: true,
            replace: true
        });
    };
</script>
