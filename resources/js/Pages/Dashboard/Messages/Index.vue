<template>

    <Head title="Customer Messages" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ $t('Customer Messages') }}
                </h2>
            </div>
        </template>

        <v-container fluid class="py-8">
            <!-- Filters -->
            <FilterBar :loading="loading" :total-items="threads.total" items-label="messages"
                :active-filters="activeFilters" @reset-filters="resetFilters" @clear-filter="clearFilter">
                <template #filters>
                    <v-col cols="12" md="4">
                        <SearchField v-model="search" :label="$t('Search Messages')" :loading="loading"
                            @search="filterMessages" @clear="filterMessages" />
                    </v-col>

                    <v-col cols="12" sm="6" md="4">
                        <v-select v-model="selectedStatus" :items="statusOptions" :label="$t('Status')" hide-details clearable
                            @update:model-value="filterMessages" variant="outlined" density="comfortable">
                            <template v-slot:prepend-inner>
                                <v-icon color="primary" size="small">mdi-filter-variant</v-icon>
                            </template>
                        </v-select>
                    </v-col>
                </template>
            </FilterBar>

            <!-- Messages Table -->
            <v-card elevation="2">
                <v-data-table :headers="headers" :items="threads.data" :loading="loading" item-value="id"
                    :sort-by="[{ key: sortBy, order: sortOrder }]" @update:sort-by="updateSort" class="elevation-0"
                    hide-default-footer>
                    <template v-slot:item.subject="{ item }">
                        <div class="d-flex align-center">
                            <v-badge v-if="hasUnreadMessages(item)" color="error" content="•" inline></v-badge>
                            <Link :href="route('dashboard.messages.show', item.uuid)" class="text-decoration-none">
                            {{ item.subject }}
                            </Link>
                        </div>
                    </template>

                    <template v-slot:item.user="{ item }">
                        <div class="d-flex flex-column">
                            <span>{{ item.user?.name }}</span>
                            <span class="text-caption text-grey">{{ item.user?.email }}</span>
                        </div>
                    </template>

                    <template v-slot:item.status="{ item }">
                        <v-chip :color="item.status === 'active' ? 'success' : 'grey'" size="small">
                            {{ item.status === 'active' ? 'Active' : 'Closed' }}
                        </v-chip>
                    </template>

                    <template v-slot:item.last_message_at="{ item }">
                        {{ formatDate(item.last_message_at) }}
                    </template>

                    <template v-slot:item.actions="{ item }">
                        <div class="d-flex justify-end">
                            <Link :href="route('dashboard.messages.show', item.uuid)">
                            <v-btn icon size="small" color="primary" class="mr-2">
                                <v-icon>mdi-eye</v-icon>
                            </v-btn>
                            </Link>

                            <v-btn icon size="small" :color="item.status === 'active' ? 'warning' : 'success'"
                                @click="toggleStatus(item)">
                                <v-icon>{{ item.status === 'active' ? 'mdi-close-circle' : 'mdi-refresh' }}</v-icon>
                            </v-btn>
                        </div>
                    </template>
                </v-data-table>

                <!-- Pagination -->
                <div class="d-flex justify-center pa-4">
                    <v-pagination v-if="threads.last_page > 1" v-model="page" :length="threads.last_page"
                        total-visible="7" @update:model-value="changePage" rounded></v-pagination>
                </div>
            </v-card>
        </v-container>
    </DashboardLayout>
</template>

<script setup>
    import DashboardLayout from '@/Layouts/DashboardLayout.vue';
    import FilterBar from '@/Components/Dashboard/FilterBar.vue';
    import SearchField from '@/Components/Dashboard/SearchField.vue';
    import { Head, Link, router } from '@inertiajs/vue3';
    import { ref, computed } from 'vue';
    import { format } from 'date-fns';

    const props = defineProps({
        threads: Object,
        filters: Object,
    });

    // State
    const search = ref(props.filters?.search || '');
    const selectedStatus = ref(props.filters?.status || '');
    const sortBy = ref('last_message_at');
    const sortOrder = ref('desc');
    const page = ref(props.threads.current_page || 1);
    const loading = ref(false);

    // Table headers
    const headers = [
        { title: 'Subject', key: 'subject', sortable: false },
        { title: 'Customer', key: 'user', sortable: false },
        { title: 'Status', key: 'status', sortable: false },
        { title: 'Last Updated', key: 'last_message_at', sortable: true },
        { title: 'Actions', key: 'actions', sortable: false, align: 'right' },
    ];

    // Status options for filter
    const statusOptions = [
        { title: 'All', value: '' },
        { title: 'Active', value: 'active' },
        { title: 'Closed', value: 'closed' },
        { title: 'Unread', value: 'unread' },
    ];

    // Format date
    const formatDate = (dateString) => {
        if (!dateString) return '';
        try {
            return format(new Date(dateString), 'MMM d, yyyy h:mm a');
        } catch (error) {
            return dateString;
        }
    };

    // Active filters for filter bar
    const activeFilters = computed(() => {
        const filters = {};
        if (search.value) filters.search = search.value;
        if (selectedStatus.value) {
            const status = statusOptions.find(s => s.value === selectedStatus.value);
            filters.status = status ? status.title : selectedStatus.value;
        }
        return filters;
    });

    // Check if a thread has unread messages
    const hasUnreadMessages = (thread) => {
        return thread.last_message?.user_id && !thread.last_message?.is_read;
    };

    // Filter messages
    const filterMessages = () => {
        loading.value = true;
        router.get(route('dashboard.messages.index'), {
            search: search.value,
            status: selectedStatus.value,
            sort_field: sortBy.value,
            sort_order: sortOrder.value,
            page: 1, // Reset to first page when filtering
        }, {
            preserveState: true,
            replace: true,
            onFinish: () => {
                loading.value = false;
            },
        });
    };

    // Reset all filters
    const resetFilters = () => {
        search.value = '';
        selectedStatus.value = '';
        filterMessages();
    };

    // Clear individual filter
    const clearFilter = (filter) => {
        if (filter === 'search') search.value = '';
        if (filter === 'status') selectedStatus.value = '';
        filterMessages();
    };

    // Update sorting
    const updateSort = (sortItems) => {
        if (sortItems.length > 0) {
            sortBy.value = sortItems[0].key;
            sortOrder.value = sortItems[0].order;
        } else {
            sortBy.value = 'last_message_at';
            sortOrder.value = 'desc';
        }
        filterMessages();
    };

    // Change page
    const changePage = (newPage) => {
        loading.value = true;
        router.get(route('dashboard.messages.index'), {
            search: search.value,
            status: selectedStatus.value,
            sort_field: sortBy.value,
            sort_order: sortOrder.value,
            page: newPage,
        }, {
            preserveState: true,
            replace: true,
            onFinish: () => {
                loading.value = false;
            },
        });
    };

    // Toggle thread status (open/close)
    const toggleStatus = (thread) => {
        loading.value = true;
        if (thread.status === 'active') {
            router.post(route('dashboard.messages.close', thread.uuid), {}, {
                preserveState: true,
                onFinish: () => {
                    loading.value = false;
                },
            });
        } else {
            router.post(route('dashboard.messages.reopen', thread.uuid), {}, {
                preserveState: true,
                onFinish: () => {
                    loading.value = false;
                },
            });
        }
    };
</script>
