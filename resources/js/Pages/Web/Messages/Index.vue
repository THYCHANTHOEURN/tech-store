<template>

    <Head title="My Messages" />

    <WebLayout>
        <v-container class="py-10">
            <h1 class="text-h4 mb-6">My Messages</h1>

            <!-- Filters -->
            <v-card class="mb-6">
                <v-card-text>
                    <v-row align="center">
                        <v-col cols="12" sm="6" md="4">
                            <v-select v-model="filters.status" :items="statusOptions" label="Status"
                                density="comfortable" variant="outlined" @update:model-value="applyFilters"></v-select>
                        </v-col>

                        <v-spacer></v-spacer>

                        <v-col cols="12" sm="auto">
                            <Link :href="route('contact')">
                            <v-btn color="primary" prepend-icon="mdi-message-plus">
                                New Message
                            </v-btn>
                            </Link>
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>

            <!-- Message Threads -->
            <v-card>
                <v-data-table :headers="headers" :items="threads.data" class="elevation-1" :loading="loading"
                    :items-per-page="10">
                    <template v-slot:item.subject="{ item }">
                        <div class="d-flex align-center">
                            <v-badge v-if="hasUnreadMessages(item)" color="error" content="•" inline></v-badge>
                            <Link :href="route('messages.show', item.uuid)" class="text-decoration-none">
                            <span class="font-weight-medium">{{ item.subject }}</span>
                            </Link>
                        </div>
                    </template>

                    <template v-slot:item.status="{ item }">
                        <v-chip :color="item.status === 'active' ? 'success' : 'grey'" size="small" text-color="white">
                            {{ item.status === 'active' ? 'Active' : 'Closed' }}
                        </v-chip>
                    </template>

                    <template v-slot:item.last_message_at="{ item }">
                        {{ formatDate(item.last_message_at) }}
                    </template>

                    <template v-slot:item.actions="{ item }">
                        <Link :href="route('messages.show', item.uuid)">
                        <v-btn icon size="small" color="primary">
                            <v-icon>mdi-eye</v-icon>
                        </v-btn>
                        </Link>
                    </template>

                    <!-- Empty state -->
                    <template v-slot:no-data>
                        <div class="text-center pa-5">
                            <v-icon size="large" color="grey" class="mb-3">mdi-message-text-outline</v-icon>
                            <p class="text-body-1 text-medium-emphasis">You don't have any messages yet.</p>
                            <Link :href="route('contact')" class="mt-3">
                            <v-btn color="primary" prepend-icon="mdi-message-plus">
                                Send a New Message
                            </v-btn>
                            </Link>
                        </div>
                    </template>
                </v-data-table>

                <!-- Pagination -->
                <div class="d-flex justify-center pa-4" v-if="threads.last_page > 1">
                    <v-pagination v-model="page" :length="threads.last_page" @update:model-value="changePage"
                        total-visible="7"></v-pagination>
                </div>
            </v-card>
        </v-container>
    </WebLayout>
</template>

<script setup>
    import WebLayout from '@/Layouts/WebLayout.vue';
    import { Head, Link, router } from '@inertiajs/vue3';
    import { ref, computed } from 'vue';
    import { format } from 'date-fns';

    const props = defineProps({
        threads: Object,
        filters: Object,
    });

    // Pagination state
    const page = ref(props.threads.current_page || 1);
    const loading = ref(false);

    // Status filter options
    const statusOptions = [
        { title: 'All Messages', value: '' },
        { title: 'Active', value: 'active' },
        { title: 'Closed', value: 'closed' },
    ];

    // Table headers
    const headers = [
        { title: 'Subject', key: 'subject', sortable: false },
        { title: 'Status', key: 'status', sortable: false },
        { title: 'Last Updated', key: 'last_message_at', sortable: false },
        { title: 'Actions', key: 'actions', sortable: false, align: 'right' },
    ];

    // Check if a thread has unread messages
    const hasUnreadMessages = (thread) => {
        return thread.last_message?.admin_id && !thread.last_message?.is_read;
    };

    // Format date
    const formatDate = (dateString) => {
        if (!dateString) return '';
        try {
            return format(new Date(dateString), 'MMM d, yyyy h:mm a');
        } catch (error) {
            return dateString;
        }
    };

    // Apply filters and fetch messages
    const applyFilters = () => {
        loading.value = true;
        router.get(route('messages.index'), {
            status: filters.status || '',
            page: 1, // Reset to first page when filtering
        }, {
            preserveState: true,
            replace: true,
            onFinish: () => {
                loading.value = false;
            },
        });
    };

    // Change page
    const changePage = (newPage) => {
        loading.value = true;
        router.get(route('messages.index'), {
            status: filters.status || '',
            page: newPage,
        }, {
            preserveState: true,
            replace: true,
            onFinish: () => {
                loading.value = false;
            },
        });
    };
</script>
