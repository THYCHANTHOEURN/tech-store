<template>

    <Head :title="`Message: ${thread.subject}`" />

    <DashboardLayout>
        <template #header>
            <div class="d-flex align-center">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Message Thread: {{ thread.subject }}
                </h2>
                <v-spacer></v-spacer>
                <Link :href="route('dashboard.messages.index')">
                <v-btn color="secondary" prepend-icon="mdi-arrow-left" variant="outlined">
                    Back to Messages
                </v-btn>
                </Link>
            </div>
        </template>

        <v-container fluid class="py-8">
            <v-row>
                <!-- Message Thread -->
                <v-col cols="12" md="9">
                    <v-card class="mb-4">
                        <v-card-title class="d-flex align-center">
                            <div>
                                <div class="text-h6">{{ thread.subject }}</div>
                                <div class="text-subtitle-2 text-medium-emphasis">
                                    Thread started on {{ formatDate(thread.created_at) }}
                                </div>
                            </div>
                            <v-spacer></v-spacer>
                            <v-chip :color="thread.status === 'active' ? 'success' : 'grey'" size="small">
                                {{ thread.status === 'active' ? 'Active' : 'Closed' }}
                            </v-chip>
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text class="pa-0">
                            <!-- Message thread -->
                            <div class="message-container">
                                <div v-for="(message, index) in thread.messages" :key="message.id" :class="[
                                    'message',
                                    message.admin_id ? 'admin' : 'customer'
                                ]">
                                    <div class="message-header">
                                        <strong>
                                            {{ message.admin_id ? (message.admin?.name || 'Staff') : (message.user?.name
                                                ||
                                                'Customer') }}
                                        </strong>
                                        <span class="message-time">{{ formatDate(message.created_at) }}</span>
                                    </div>
                                    <div class="message-content">
                                        <p>{{ message.content }}</p>

                                        <!-- Attachment if any -->
                                        <div v-if="message.attachment" class="attachment">
                                            <v-btn variant="outlined" size="small"
                                                :href="`/storage/${message.attachment}`" target="_blank"
                                                prepend-icon="mdi-paperclip">
                                                View Attachment
                                            </v-btn>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </v-card-text>
                    </v-card>

                    <!-- Reply Form -->
                    <v-card v-if="thread.status === 'active'">
                        <v-card-title>Reply to Customer</v-card-title>
                        <v-card-text>
                            <v-form @submit.prevent="submitReply">
                                <v-textarea v-model="form.message" label="Your reply" :error-messages="errors?.message"
                                    auto-grow rows="4" variant="outlined" required></v-textarea>

                                <v-file-input v-model="form.attachment" label="Attachment (optional)"
                                    :error-messages="errors?.attachment" prepend-icon="mdi-paperclip"
                                    accept="image/*, application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                                    variant="outlined" density="comfortable"></v-file-input>

                                <div class="d-flex justify-end mt-4">
                                    <v-btn color="primary" type="submit" :loading="submitting" :disabled="submitting">
                                        Send Reply
                                    </v-btn>
                                </div>
                            </v-form>
                        </v-card-text>
                    </v-card>

                    <div v-else class="text-center pa-4">
                        <p class="mb-4">This message thread has been closed.</p>
                        <v-btn color="primary" @click="reopenThread">Reopen Thread</v-btn>
                    </div>
                </v-col>

                <!-- Customer Information -->
                <v-col cols="12" md="3">
                    <v-card class="mb-4">
                        <v-card-title class="text-h6">
                            <v-icon class="mr-2">mdi-account</v-icon>
                            Customer Info
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <v-list>
                                <v-list-item>
                                    <template v-slot:prepend>
                                        <v-icon color="primary" class="mr-2">mdi-account</v-icon>
                                    </template>
                                    <v-list-item-title>Name</v-list-item-title>
                                    <v-list-item-subtitle>{{ thread.user?.name }}</v-list-item-subtitle>
                                </v-list-item>

                                <v-list-item>
                                    <template v-slot:prepend>
                                        <v-icon color="primary" class="mr-2">mdi-email</v-icon>
                                    </template>
                                    <v-list-item-title>Email</v-list-item-title>
                                    <v-list-item-subtitle>{{ thread.user?.email }}</v-list-item-subtitle>
                                </v-list-item>
                            </v-list>

                            <div class="d-flex justify-center mt-4">
                                <Link v-if="thread.user" :href="route('dashboard.customers.show', thread.user.uuid)">
                                <v-btn color="primary" variant="outlined">View Customer Profile</v-btn>
                                </Link>
                            </div>
                        </v-card-text>
                    </v-card>

                    <v-card>
                        <v-card-title class="text-h6">
                            <v-icon class="mr-2">mdi-cog</v-icon>
                            Actions
                        </v-card-title>
                        <v-divider></v-divider>
                        <v-card-text>
                            <v-btn block :color="thread.status === 'active' ? 'warning' : 'success'"
                                :prepend-icon="thread.status === 'active' ? 'mdi-close-circle' : 'mdi-refresh'"
                                class="mb-3" @click="toggleStatus">
                                {{ thread.status === 'active' ? 'Close Thread' : 'Reopen Thread' }}
                            </v-btn>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>
    </DashboardLayout>
</template>

<script setup>
    import DashboardLayout from '@/Layouts/DashboardLayout.vue';
    import { Head, Link, router, useForm } from '@inertiajs/vue3';
    import { ref } from 'vue';
    import { format } from 'date-fns';
    import { useMessages } from '@/Composables/useMessages';

    const props = defineProps({
        thread: Object,
        errors: Object,
    });

    const submitting = ref(false);

    // Reply form
    const form = useForm({
        message: '',
        attachment: null,
    });

    // Format date
    const formatDate = (dateString) => {
        if (!dateString) return '';
        try {
            return format(new Date(dateString), 'MMM d, yyyy h:mm a');
        } catch (error) {
            return dateString;
        }
    };

    const { decrementUnreadCount } = useMessages();

    // Decrement unread count for viewed messages
    if (props.thread && props.thread.messages) {
        const unreadMessages = props.thread.messages.filter(
            message => message.user_id && !message.is_read
        );

        unreadMessages.forEach(() => decrementUnreadCount());
    }

    // Submit reply
    const submitReply = () => {
        submitting.value = true;
        form.post(route('dashboard.messages.reply', props.thread.uuid), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
            },
            onFinish: () => {
                submitting.value = false;
            },
        });
    };

    // Toggle thread status (open/close)
    const toggleStatus = () => {
        if (props.thread.status === 'active') {
            router.post(route('dashboard.messages.close', props.thread.uuid), {}, {
                preserveScroll: true,
            });
        } else {
            reopenThread();
        }
    };

    // Reopen thread
    const reopenThread = () => {
        router.post(route('dashboard.messages.reopen', props.thread.uuid), {}, {
            preserveScroll: true,
        });
    };
</script>

<style scoped>
    .message-container {
        padding: 20px;
    }

    .message {
        margin-bottom: 20px;
        max-width: 80%;
    }

    .message-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 5px;
        font-size: 0.85rem;
    }

    .message-time {
        color: rgba(0, 0, 0, 0.6);
    }

    .message-content {
        padding: 15px;
        border-radius: 8px;
    }

    .admin {
        margin-left: auto;
    }

    .admin .message-content {
        background-color: #e8f5e9;
        color: #1b5e20;
    }

    .customer {
        margin-right: auto;
    }

    .customer .message-content {
        background-color: #f5f5f5;
        color: #333;
    }

    .attachment {
        margin-top: 10px;
    }
</style>
