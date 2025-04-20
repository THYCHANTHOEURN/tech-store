import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

export function useNotifications(initialNotifications = [], initialUnreadCount = 0) {
  const notifications = ref(initialNotifications);
  const unreadCount = ref(initialUnreadCount);

  const fetchNotifications = async () => {
    try {
      const response = await axios.get(route('dashboard.notifications.list'));
      notifications.value = response.data.notifications;
      unreadCount.value = response.data.unreadCount;
    } catch (error) {
      console.error('Error fetching notifications', error);
    }
  };

  const markAsRead = (id) => {
    router.post(route('dashboard.notifications.read', id), {}, {
      preserveScroll: true,
      onSuccess: () => {
        const notificationIndex = notifications.value.findIndex(n => n.id === id);
        if (notificationIndex !== -1) {
          notifications.value[notificationIndex].read_at = new Date().toISOString();
          if (unreadCount.value > 0) {
            unreadCount.value--;
          }
        }
      }
    });
  };

  const markAllAsRead = () => {
    router.post(route('dashboard.notifications.readAll'), {}, {
      preserveScroll: true,
      onSuccess: () => {
        notifications.value = notifications.value.map(notification => ({
          ...notification,
          read_at: notification.read_at || new Date().toISOString()
        }));
        unreadCount.value = 0;
      }
    });
  };

  return {
    notifications,
    unreadCount,
    fetchNotifications,
    markAsRead,
    markAllAsRead
  };
}
