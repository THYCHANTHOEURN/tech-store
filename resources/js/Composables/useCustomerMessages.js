import { ref } from 'vue';
import axios from 'axios';

export function useCustomerMessages(initialUnreadCount = 0) {
    const unreadCount = ref(initialUnreadCount);

    const fetchUnreadCount = async () => {
        try {
            const response = await axios.get(route('messages.unread.count'));
            unreadCount.value = response.data.count;
        } catch (error) {
            console.error('Error fetching unread message count:', error);
        }
    };

    const decrementUnreadCount = () => {
        if (unreadCount.value > 0) {
            unreadCount.value--;
        }
    };

    return {
        unreadCount,
        fetchUnreadCount,
        decrementUnreadCount
    };
}
