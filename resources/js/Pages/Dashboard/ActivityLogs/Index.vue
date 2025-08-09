<template>

    <Head title="Activity Logs" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Activity Logs
                </h2>
            </div>
        </template>

        <v-container fluid class="py-8">
            <!-- Enhanced Filters -->
            <FilterBar :loading="loading" :total-items="activities.total" items-label="activities"
                :active-filters="activeFilters" @reset-filters="resetFilters" @clear-filter="clearFilter">
                <template #filters>
                    <v-col cols="12" md="3">
                        <SearchField v-model="causer" label="Causer Name" :loading="loading" @search="applyFilters"
                            @clear="applyFilters" />
                    </v-col>
                    <v-col cols="12" md="3">
                        <SearchField v-model="description" label="Description" :loading="loading" @search="applyFilters"
                            @clear="applyFilters" />
                    </v-col>
                    <v-col cols="12" md="3">
                        <v-text-field v-model="dateFrom" label="Date From" type="date" variant="outlined"
                            density="comfortable" @change="applyFilters" />
                    </v-col>
                    <v-col cols="12" md="3">
                        <v-text-field v-model="dateTo" label="Date To" type="date" variant="outlined"
                            density="comfortable" @change="applyFilters" />
                    </v-col>
                </template>
            </FilterBar>

            <v-row>
                <v-col cols="12">
                    <v-card>
                        <v-data-table :headers="headers" :items="activities.data" :loading="loading" class="elevation-0"
                            hide-default-footer>
                            <template v-slot:item.causer="{ item }">
                                {{ item.causer ? item.causer.name : 'System' }}
                            </template>
                            <template v-slot:item.subject_type="{ item }">
                                {{ item.subject_type.split('\\').pop() }}
                            </template>
                            <template v-slot:item.created_at="{ item }">
                                {{ new Date(item.created_at).toLocaleString() }}
                            </template>
                            <template v-slot:item.description="{ item }">
                                <v-chip :color="descriptionMeta[item.description]?.color || 'default'" variant="tonal"
                                    class="font-weight-medium" size="small">
                                    <v-icon start
                                        :icon="descriptionMeta[item.description]?.icon || 'mdi-information-outline'" />
                                    {{ item.description }}
                                </v-chip>
                            </template>
                            <template v-slot:item.actions="{ item }">
                                <div class="d-flex flex-nowrap justify-center justify-sm-end">
                                    <Link :href="route('dashboard.activity-logs.show', item.id)"
                                        class="text-decoration-none">
                                    <v-btn icon size="x-small" color="info" class="mr-1" title="View" rounded="lg">
                                        <v-icon>mdi-eye</v-icon>
                                    </v-btn>
                                    </Link>
                                </div>
                            </template>
                        </v-data-table>
                        <!-- Custom Pagination -->
                        <div class="d-flex justify-center py-4">
                            <v-pagination v-if="activities.last_page" v-model="currentPage"
                                :length="activities.last_page" total-visible="7" @update:model-value="changePage"
                                rounded></v-pagination>
                            <v-select v-model="perPage" :items="perPageOptions" label="Rows per page" class="ml-4"
                                style="max-width: 120px;" @update:model-value="changePerPage" hide-details></v-select>
                        </div>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>

    </DashboardLayout>

</template>

<script setup>

    import DashboardLayout from '@/Layouts/DashboardLayout.vue';
    import { Head, router } from '@inertiajs/vue3';
    import { ref, computed } from 'vue';
    import FilterBar from '@/Components/Dashboard/FilterBar.vue';
    import SearchField from '@/Components/Dashboard/SearchField.vue';

    const props = defineProps({
        activities: Object,
        filters: Object
    });

    // State variables
    const causer = ref(props.filters.causer || '');
    const description = ref(props.filters.description || '');
    const dateFrom = ref(props.filters.date_from || '');
    const dateTo = ref(props.filters.date_to || '');
    const currentPage = ref(props.activities.current_page || 1);
    const perPage = ref(Number(props.filters.per_page) || 10);
    const loading = ref(false);

    // Table headers
    const headers = [
        { title: 'ID', key: 'id' },
        { title: 'Description', key: 'description' },
        { title: 'Causer', key: 'causer' },
        { title: 'Subject', key: 'subject_type' },
        { title: 'Created At', key: 'created_at', sortable: true },
        { title: 'Actions', key: 'actions', sortable: false, align: 'center' }, // Add Actions column
    ];

    const perPageOptions = [10, 25, 50, 100];

    // Description to icon/color mapping
    const descriptionMeta = {
        'created': { icon: 'mdi-plus', color: 'primary' },
        'updated': { icon: 'mdi-pencil', color: 'warning' },
        'deleted': { icon: 'mdi-delete', color: 'error' },
        'logged in': { icon: 'mdi-login', color: 'success' },
        'logged out': { icon: 'mdi-logout', color: 'secondary' },
    };

    // Computed property for active filters
    const activeFilters = computed(() => ({
        causer: {
            label: 'Causer',
            value: causer.value,
            displayValue: causer.value,
            active: !!causer.value
        },
        description: {
            label: 'Description',
            value: description.value,
            displayValue: description.value,
            active: !!description.value
        },
        date_from: {
            label: 'Date From',
            value: dateFrom.value,
            displayValue: dateFrom.value,
            active: !!dateFrom.value
        },
        date_to: {
            label: 'Date To',
            value: dateTo.value,
            displayValue: dateTo.value,
            active: !!dateTo.value
        },
    }));

    // Apply filters
    const applyFilters = () => {
        loading.value = true;
        router.get(route('dashboard.activity-logs.index'), {
            causer: causer.value,
            description: description.value,
            date_from: dateFrom.value,
            date_to: dateTo.value,
            page: 1,
            per_page: perPage.value,
        }, {
            preserveState: true,
            replace: true,
            onFinish: () => {
                loading.value = false;
            }
        });
    };

    // Clear specific filter
    const clearFilter = (filterKey) => {
        switch (filterKey) {
            case 'causer':
                causer.value = '';
                break;
            case 'description':
                description.value = '';
                break;
            case 'date_from':
                dateFrom.value = '';
                break;
            case 'date_to':
                dateTo.value = '';
                break;
        }
        applyFilters();
    };

    // Reset all filters
    const resetFilters = () => {
        causer.value = '';
        description.value = '';
        dateFrom.value = '';
        dateTo.value = '';
        loading.value = true;
        router.get(route('dashboard.activity-logs.index'), {
            per_page: perPage.value,
        }, {
            onFinish: () => {
                loading.value = false;
            }
        });
    };

    // Pagination
    const changePage = (newPage) => {
        loading.value = true;
        router.get(route('dashboard.activity-logs.index'), {
            causer: causer.value,
            description: description.value,
            date_from: dateFrom.value,
            date_to: dateTo.value,
            page: newPage,
            per_page: perPage.value,
        }, {
            preserveState: true,
            replace: true,
            onFinish: () => {
                loading.value = false;
            }
        });
    };

    // Change per page
    const changePerPage = (newPerPage) => {
        perPage.value = newPerPage;
        applyFilters();
    };

</script>
