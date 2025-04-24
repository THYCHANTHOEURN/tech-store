<template>
    <Head title="Admin Dashboard" />

    <DashboardLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template>

        <v-container fluid class="py-8">
            <!-- Welcome Section with User Name -->
            <v-row>
                <v-col cols="12">
                    <v-card class="mb-4" elevation="1">
                        <v-card-text>
                            <div class="d-flex align-center">
                                <v-avatar color="primary" class="mr-4">
                                    <span class="text-h5 white--text">{{ userInitials }}</span>
                                </v-avatar>
                                <div>
                                    <h3 class="text-h5">Welcome back, {{ userName }}!</h3>
                                    <p class="text-body-1 text-medium-emphasis">
                                        Here's what's happening in your store today.
                                    </p>
                                </div>
                                <v-spacer></v-spacer>
                                <v-chip color="primary" outlined>{{ currentDate }}</v-chip>
                            </div>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Statistics Cards -->
            <v-row>
                <v-col cols="12" sm="6" md="3">
                    <v-hover v-slot="{ isHovering, props }">
                        <v-card v-bind="props" :elevation="isHovering ? 4 : 1" color="primary" class="white--text stats-card">
                            <v-card-text>
                                <div class="d-flex justify-space-between align-center">
                                    <div>
                                        <div class="text-h6 white--text">Products</div>
                                        <div class="text-h4 white--text">{{ stats.products || 0 }}</div>
                                    </div>
                                    <v-icon x-large color="white" class="stats-icon">mdi-package-variant-closed</v-icon>
                                </div>
                                <v-progress-linear v-if="loading" color="white" indeterminate class="mt-2"></v-progress-linear>
                            </v-card-text>
                        </v-card>
                    </v-hover>
                </v-col>

                <v-col cols="12" sm="6" md="3">
                    <v-hover v-slot="{ isHovering, props }">
                        <v-card v-bind="props" :elevation="isHovering ? 4 : 1" color="success" class="white--text stats-card">
                            <v-card-text>
                                <div class="d-flex justify-space-between align-center">
                                    <div>
                                        <div class="text-h6 white--text">Orders</div>
                                        <div class="text-h4 white--text">{{ stats.orders || 0 }}</div>
                                    </div>
                                    <v-icon x-large color="white" class="stats-icon">mdi-cart</v-icon>
                                </div>
                                <v-progress-linear v-if="loading" color="white" indeterminate class="mt-2"></v-progress-linear>
                            </v-card-text>
                        </v-card>
                    </v-hover>
                </v-col>

                <v-col cols="12" sm="6" md="3">
                    <v-hover v-slot="{ isHovering, props }">
                        <v-card v-bind="props" :elevation="isHovering ? 4 : 1" color="warning" class="white--text stats-card">
                            <v-card-text>
                                <div class="d-flex justify-space-between align-center">
                                    <div>
                                        <div class="text-h6 white--text">Customers</div>
                                        <div class="text-h4 white--text">{{ stats.customers || 0 }}</div>
                                    </div>
                                    <v-icon x-large color="white" class="stats-icon">mdi-account-group</v-icon>
                                </div>
                                <v-progress-linear v-if="loading" color="white" indeterminate class="mt-2"></v-progress-linear>
                            </v-card-text>
                        </v-card>
                    </v-hover>
                </v-col>

                <v-col cols="12" sm="6" md="3">
                    <v-hover v-slot="{ isHovering, props }">
                        <v-card v-bind="props" :elevation="isHovering ? 4 : 1" color="info" class="white--text stats-card">
                            <v-card-text>
                                <div class="d-flex justify-space-between align-center">
                                    <div>
                                        <div class="text-h6 white--text">Revenue</div>
                                        <div class="text-h4 white--text">${{ formatCurrency(stats.revenue || 0) }}</div>
                                    </div>
                                    <v-icon x-large color="white" class="stats-icon">mdi-currency-usd</v-icon>
                                </div>
                                <v-progress-linear v-if="loading" color="white" indeterminate class="mt-2"></v-progress-linear>
                            </v-card-text>
                        </v-card>
                    </v-hover>
                </v-col>
            </v-row>

            <!-- Quick Actions -->
            <v-row class="mt-6">
                <v-col cols="12">
                    <v-card elevation="1">
                        <v-card-title class="d-flex align-center">
                            <v-icon left color="primary" class="mr-2">mdi-lightning-bolt</v-icon>
                            Quick Actions
                        </v-card-title>
                        <v-card-text>
                            <v-row>
                                <v-col cols="6" sm="4" md="2">
                                    <Link :href="route('dashboard.products.create')" class="text-decoration-none">
                                        <v-hover v-slot="{ isHovering, props }">
                                            <v-card v-bind="props" :elevation="isHovering ? 3 : 1" class="text-center pa-3">
                                                <v-icon size="32" color="primary">mdi-plus-box</v-icon>
                                                <div class="text-body-2 mt-2">Add Product</div>
                                            </v-card>
                                        </v-hover>
                                    </Link>
                                </v-col>
                                <v-col cols="6" sm="4" md="2">
                                    <Link :href="route('dashboard.orders.index')" class="text-decoration-none">
                                        <v-hover v-slot="{ isHovering, props }">
                                            <v-card v-bind="props" :elevation="isHovering ? 3 : 1" class="text-center pa-3">
                                                <v-icon size="32" color="success">mdi-receipt</v-icon>
                                                <div class="text-body-2 mt-2">View Orders</div>
                                            </v-card>
                                        </v-hover>
                                    </Link>
                                </v-col>
                                <v-col cols="6" sm="4" md="2">
                                    <Link :href="route('dashboard.customers.index')" class="text-decoration-none">
                                        <v-hover v-slot="{ isHovering, props }">
                                            <v-card v-bind="props" :elevation="isHovering ? 3 : 1" class="text-center pa-3">
                                                <v-icon size="32" color="warning">mdi-account-multiple</v-icon>
                                                <div class="text-body-2 mt-2">Customers</div>
                                            </v-card>
                                        </v-hover>
                                    </Link>
                                </v-col>
                                <v-col cols="6" sm="4" md="2">
                                    <Link :href="route('dashboard.categories.index')" class="text-decoration-none">
                                        <v-hover v-slot="{ isHovering, props }">
                                            <v-card v-bind="props" :elevation="isHovering ? 3 : 1" class="text-center pa-3">
                                                <v-icon size="32" color="deep-purple">mdi-format-list-bulleted</v-icon>
                                                <div class="text-body-2 mt-2">Categories</div>
                                            </v-card>
                                        </v-hover>
                                    </Link>
                                </v-col>
                                <v-col cols="6" sm="4" md="2">
                                    <Link :href="route('dashboard.banners.index')" class="text-decoration-none">
                                        <v-hover v-slot="{ isHovering, props }">
                                            <v-card v-bind="props" :elevation="isHovering ? 3 : 1" class="text-center pa-3">
                                                <v-icon size="32" color="pink">mdi-image-multiple</v-icon>
                                                <div class="text-body-2 mt-2">Banners</div>
                                            </v-card>
                                        </v-hover>
                                    </Link>
                                </v-col>
                                <v-col cols="6" sm="4" md="2">
                                    <Link :href="route('index')" target="_blank" class="text-decoration-none">
                                        <v-hover v-slot="{ isHovering, props }">
                                            <v-card v-bind="props" :elevation="isHovering ? 3 : 1" class="text-center pa-3">
                                                <v-icon size="32" color="blue">mdi-web</v-icon>
                                                <div class="text-body-2 mt-2">View Store</div>
                                            </v-card>
                                        </v-hover>
                                    </Link>
                                </v-col>
                            </v-row>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>

            <!-- Data Visualization and Tables Row -->
            <v-row class="mt-6">
                <v-col cols="12" md="7">
                    <!-- Sales Chart -->
                    <v-card elevation="1" class="mb-6">
                        <v-card-title class="d-flex align-center">
                            <v-icon left color="primary" class="mr-2">mdi-chart-line</v-icon>
                            Sales Overview
                            <v-spacer></v-spacer>
                            <v-chip small>Last 7 days</v-chip>
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text class="chart-container">
                            <div v-if="loading" class="d-flex justify-center align-center" style="height: 250px">
                                <v-progress-circular indeterminate color="primary"></v-progress-circular>
                            </div>
                            <div v-else style="height: 250px; position: relative;">
                                <apexchart
                                    type="area"
                                    height="250"
                                    :options="chartOptions"
                                    :series="chartSeries"
                                ></apexchart>
                            </div>
                        </v-card-text>
                    </v-card>

                    <!-- Recent Orders -->
                    <v-card elevation="1">
                        <v-card-title class="d-flex align-center">
                            <v-icon left color="primary" class="mr-2">mdi-cart-outline</v-icon>
                            Recent Orders
                            <v-spacer></v-spacer>
                            <Link :href="route('dashboard.orders.index')">
                                <v-btn color="primary" text variant="plain" size="small" class="text-none">
                                    View All
                                    <v-icon small class="ml-1">mdi-arrow-right</v-icon>
                                </v-btn>
                            </Link>
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text class="pa-0">
                            <v-data-table :headers="orderHeaders" :items="recentOrders" :items-per-page="5"
                                hide-default-footer class="elevation-0" :loading="loading">
                                <template v-slot:item.uuid="{ item }">
                                    <Link :href="route('dashboard.orders.show', item.uuid)" class="font-weight-medium text-decoration-none">
                                        #{{ item.uuid.slice(-8).toUpperCase() }}
                                    </Link>
                                </template>
                                <template v-slot:item.customer="{ item }">
                                    <div class="text-truncate" style="max-width: 150px;">{{ item.customer }}</div>
                                </template>
                                <template v-slot:item.status="{ item }">
                                    <v-chip :color="getStatusColor(item.status)" size="small" class="text-white">
                                        {{ item.status.charAt(0).toUpperCase() + item.status.slice(1) }}
                                    </v-chip>
                                </template>
                                <template v-slot:item.total_amount="{ item }">
                                    <span class="font-weight-bold">${{ formatCurrency(item.total_amount) }}</span>
                                </template>
                                <template v-slot:item.created_at="{ item }">
                                    {{ formatDateFull(item.created_at) }}
                                </template>
                            </v-data-table>
                        </v-card-text>
                    </v-card>
                </v-col>

                <v-col cols="12" md="5">
                    <!-- Popular Products with Enhanced Layout -->
                    <v-card elevation="1" height="100%">
                        <v-card-title class="d-flex align-center">
                            <v-icon left color="primary" class="mr-2">mdi-star-outline</v-icon>
                            Popular Products
                            <v-spacer></v-spacer>
                            <Link :href="route('dashboard.products.index')">
                                <v-btn color="primary" text variant="plain" size="small" class="text-none">
                                    View All
                                    <v-icon small class="ml-1">mdi-arrow-right</v-icon>
                                </v-btn>
                            </Link>
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text class="pa-0">
                            <v-list three-line>
                                <template v-if="loading">
                                    <div class="d-flex justify-center py-8">
                                        <v-progress-circular indeterminate color="primary"></v-progress-circular>
                                    </div>
                                </template>
                                <template v-else>
                                    <Link v-for="(product, index) in popularProducts" :key="index"
                                        :href="route('dashboard.products.show', product.uuid)" class="text-decoration-none">
                                        <v-list-item>
                                            <template v-slot:prepend>
                                                <v-avatar rounded="lg" size="60">
                                                    <v-img :src="product.primary_image_url" cover alt="Product Image"></v-img>
                                                </v-avatar>
                                            </template>
                                            <v-list-item-title class="font-weight-medium">{{ product.name }}</v-list-item-title>
                                            <v-list-item-subtitle>
                                                <v-icon small color="success">mdi-shopping</v-icon> Sold: {{ product.sold }}
                                                <v-icon small color="info" class="ml-2">mdi-package-variant</v-icon> Stock: {{ product.stock }}
                                            </v-list-item-subtitle>
                                            <template v-slot:append>
                                                <v-chip color="primary" size="small">${{ formatCurrency(product.price) }}</v-chip>
                                            </template>
                                        </v-list-item>
                                        <v-divider v-if="index < popularProducts.length - 1"></v-divider>
                                    </Link>
                                </template>
                            </v-list>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </DashboardLayout>
</template>

<script setup>
    import DashboardLayout from '@/Layouts/DashboardLayout.vue';
    import { Head, Link, usePage } from '@inertiajs/vue3';
    import { ref, computed, onMounted, watch } from 'vue';

    const loading = ref(true);
    const chartOptions = ref({});
    const chartSeries = ref([]);

    // Get data from controller via Inertia props
    const props = defineProps({
        stats: Object,
        recentOrders: Array,
        popularProducts: Array,
        salesChart: Object
    });

    // Get user information
    const page = usePage();
    const userName = computed(() => {
        return page.props.auth.user?.name || 'Admin';
    });

    const userInitials = computed(() => {
        const name = page.props.auth.user?.name || 'Admin User';
        return name
            .split(' ')
            .map(part => part[0])
            .join('')
            .toUpperCase()
            .substring(0, 2);
    });

    // Current date in nice format
    const currentDate = computed(() => {
        return new Date().toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    });

    // Table headers for recent orders
    const orderHeaders = [
        { title: 'Order ID', key: 'uuid', align: 'start', sortable: false },
        { title: 'Customer', key: 'customer' },
        { title: 'Status', key: 'status' },
        { title: 'Amount', key: 'total_amount' },
        { title: 'Date', key: 'created_at' }
    ];

    /**
     * Get status color based on order status
     */
    const getStatusColor = (status) => {
        switch (status) {
            case 'completed': return 'success';
            case 'processing': return 'info';
            case 'pending': return 'warning';
            case 'cancelled': return 'error';
            default: return 'grey';
        }
    };

    /**
     * Format currency with 2 decimal places
     */
    const formatCurrency = (value) => {
        return Number(value).toFixed(2);
    };

    /**
     * Format date string to more detailed human-readable format
     */
    const formatDateFull = (dateString) => {
        const options = { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
        return new Date(dateString).toLocaleDateString('en-US', options);
    };

    /**
     * Initialize sales chart with ApexCharts
     */
    const initSalesChart = () => {
        if (!props.salesChart) {
            console.error("No sales chart data available");
            return;
        }

        console.log("Sales Chart Data:", props.salesChart);

        // Update chart options
        chartOptions.value = {
            chart: {
                id: 'sales-overview',
                toolbar: {
                    show: false,
                },
                zoom: {
                    enabled: false,
                },
                fontFamily: 'inherit',
                sparkline: {
                    enabled: false,
                },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800,
                    animateGradually: {
                        enabled: true,
                        delay: 150
                    },
                    dynamicAnimation: {
                        enabled: true,
                        speed: 350
                    }
                }
            },
            colors: ['#42A5F5'],
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'light',
                    type: 'vertical',
                    shadeIntensity: 0.5,
                    gradientToColors: ['#1976D2'],
                    inverseColors: false,
                    opacityFrom: 0.6,
                    opacityTo: 0.1,
                    stops: [0, 100],
                }
            },
            stroke: {
                curve: 'smooth',
                width: 3,
            },
            dataLabels: {
                enabled: false
            },
            grid: {
                borderColor: 'rgba(0,0,0,0.1)',
                strokeDashArray: 3,
                xaxis: {
                    lines: {
                        show: false
                    }
                },
            },
            tooltip: {
                theme: 'dark',
                x: {
                    format: 'dd MMM'
                },
                y: {
                    formatter: function(value) {
                        return '$ ' + value.toFixed(2);
                    }
                }
            },
            xaxis: {
                categories: props.salesChart.labels,
                labels: {
                    style: {
                        colors: '#999',
                    }
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#999',
                    },
                    formatter: function(value) {
                        return '$ ' + value;
                    }
                }
            },
            markers: {
                size: 4,
                colors: ['#1976D2'],
                strokeColors: '#fff',
                strokeWidth: 2,
                hover: {
                    size: 6,
                }
            }
        };

        // Update chart series data
        chartSeries.value = [
            {
                name: 'Sales',
                data: props.salesChart.data
            }
        ];
    };

    // Watch for changes in salesChart data
    watch(() => props.salesChart, (newData) => {
        if (newData) {
            initSalesChart();
        }
    }, { immediate: false, deep: true });

    // Simulate loading state
    onMounted(() => {
        setTimeout(() => {
            loading.value = false;
            // Initialize chart after loading completes
            initSalesChart();
        }, 700);
    });
</script>

<style scoped>
    .stats-card {
        transition: transform 0.3s;
    }

    .stats-card:hover {
        transform: translateY(-5px);
    }

    .stats-icon {
        opacity: 0.8;
    }

    .chart-container {
        position: relative;
        min-height: 250px;
    }

    /* Ensure link hover styles don't conflict with v-hover */
    :deep(.v-list-item:hover) {
        background-color: rgba(var(--v-theme-primary), 0.05);
    }
</style>
