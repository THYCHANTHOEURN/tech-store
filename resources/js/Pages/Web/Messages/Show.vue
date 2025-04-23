<template>

    <Head :title="`Message: ${thread.subject}`" />

    <WebLayout>
        <v-container class="py-10">
            <div class="d-flex align-center mb-6">
                <Link :href="route('messages.index')">
                <v-btn variant="text" prepend-icon="mdi-arrow-left">Back to Messages</v-btn>
                </Link>
                <v-spacer></v-spacer>
                <v-chip :color="thread.status === 'active' ? 'success' : 'grey'" size="small" text-color="white">
                    {{ thread.status === 'active' ? 'Active' : 'Closed' }}
                </v-chip>
            </div>

            <v-card class="mb-6">
                <v-card-title class="pb-0 text-h5">{{ thread.subject }}</v-card-title>
                <v-card-subtitle class="pt-2">
                    <span>Started on {{ formatDate(thread.created_at) }}</span>
                </v-card-subtitle>
                <v-divider></v-divider>
                <v-card-text class="pa-0">
                    <!-- Message thread -->
                    <div class="message-container">
                        <div v-for="(message, index) in thread.messages" :key="message.id" :class="[
                            'message',
                            message.user_id === $page.props.auth.user?.id ? 'sender' : 'receiver'
                        ]">
                            <div class="message-header">
                                <strong>
                                    {{
                                        message.user_id === $page.props.auth.user?.id
                                            ? 'You'
                                            : (message.admin?.name || 'Support Staff')
                                    }}
                                </strong>
                                <span class="message-time">{{ formatDate(message.created_at) }}</span>
                            </div>
                            <div class="message-content">
                                <p>{{ message.content }}</p>

                                <!-- Attachment if any -->
                                <div v-if="message.attachment" class="attachment">
                                    <v-btn variant="outlined" size="small" :href="`/storage/${message.attachment}`"
                                        target="_blank" prepend-icon="mdi-paperclip">
                                        View Attachment
                                    </v-btn>
                                </div>
                            </div>
                        </div>
                    </div>
                </v-card-text>
            </v-card>

            <!-- Reply form -->
            <v-card v-if="thread.status === 'active'">
                <v-card-title>Reply</v-card-title>
                <v-card-text>
                    <v-form @submit.prevent="sendReply">
                        <v-textarea v-model="form.message" label="Your message" rows="4" auto-grow
                            :error-messages="errors.message" variant="outlined" required></v-textarea>

                        <v-file-input v-model="form.attachment" label="Attachment (optional)"
                            prepend-icon="mdi-paperclip" :error-messages="errors.attachment"
                            accept="image/*, application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                            variant="outlined" density="comfortable"></v-file-input>

                        <div class="d-flex justify-end mt-4">
                            <v-btn type="submit" color="primary" :loading="processing" :disabled="processing">
                                Send Reply
                            </v-btn>
                        </div>
                    </v-form>
                </v-card-text>
            </v-card>

            <div v-else class="text-center pa-4">
                <p class="text-body-1 text-medium-emphasis mb-4">
                    This message thread has been closed.
                </p>
                <Link :href="route('contact')">
                <v-btn color="primary" prepend-icon="mdi-message-plus">
                    Start New Conversation
                </v-btn>
                </Link>
            </div>
        </v-container>
    </WebLayout>
</template>

<script setup>
    import WebLayout from '@/Layouts/WebLayout.vue';
    import { Head, Link, router, useForm } from '@inertiajs/vue3';
    import { ref } from 'vue';
    import { format } from 'date-fns';
    import { useCustomerMessages } from '@/Composables/useCustomerMessages';

    const props = defineProps({
        thread: Object,
        errors: Object,
    });

    // Reply form
    const form = useForm({
        message: '',
        attachment: null,
    });

    const processing = ref(false);

    // Format date
    const formatDate = (dateString) => {
        if (!dateString) return '';
        try {
            return format(new Date(dateString), 'MMM d, yyyy h:mm a');
        } catch (error) {
            return dateString;
        }
    };

    const { decrementUnreadCount } = useCustomerMessages();

    // Similar to admin side, update the badge count when viewing messages
    if (props.thread && props.thread.messages) {
        const unreadMessages = props.thread.messages.filter(
            message => message.admin_id && !message.is_read
        );
        
        // Decrease the unread count to match the UI immediately
        unreadMessages.forEach(() => decrementUnreadCount());
    }

    // Send reply
    const sendReply = () => {
        processing.value = true;
        form.post(route('messages.reply', props.thread.uuid), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
            },
            onFinish: () => {
                processing.value = false;
            },
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

    .sender {
        margin-left: auto;
    }

    .sender .message-content {
        background-color: #e3f2fd;
        color: #0d47a1;
    }

    .receiver {
        margin-right: auto;
    }

    .receiver .message-content {
        background-color: #f5f5f5;
        color: #333;
    }

    .attachment {
        margin-top: 10px;
    }
</style>
