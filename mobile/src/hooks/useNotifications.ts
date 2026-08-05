import { useCallback, useEffect, useState } from 'react';
import { emitNotificationChanges, subscribeNotificationChanges } from '../services/notificationEvents';
import {
  getPushUnreadIds,
  getReadNotificationIds,
  markAllNotificationsRead,
  markNotificationRead,
} from '../services/notificationStorage';
import { fetchAnnouncementsByType, type NewsItem } from '../services/newsService';
import type { AppNotification } from '../types/notification';

export type NotificationListItem = AppNotification & {
  unread: boolean;
};

function toAppNotification(item: NewsItem): AppNotification {
  return {
    id: item.id,
    title: item.title,
    excerpt: item.excerpt,
    formattedDate: item.formattedDate,
    categoryLabel: item.categoryLabel,
    slug: item.slug,
    imageUrl: item.imageUrl,
  };
}

/**
 * Bildirim merkezi: duyurular + okunmamış sayacı.
 */
export function useNotifications() {
  const [items, setItems] = useState<NotificationListItem[]>([]);
  const [unreadCount, setUnreadCount] = useState(0);
  const [loading, setLoading] = useState(true);

  const refresh = useCallback(async () => {
    const [newsResult, readIds, pushUnreadIds] = await Promise.all([
      fetchAnnouncementsByType(1, null, 30),
      getReadNotificationIds(),
      getPushUnreadIds(),
    ]);

    const notifications: NotificationListItem[] = newsResult.items.slice(0, 30).map((item) => {
      const notification = toAppNotification(item);
      return {
        ...notification,
        unread: !readIds.has(notification.id) || pushUnreadIds.has(notification.id),
      };
    });
    setItems(notifications);

    const unread = notifications.filter((item) => item.unread).length;
    setUnreadCount(unread);
    setLoading(false);
  }, []);

  useEffect(() => {
    refresh();
    return subscribeNotificationChanges(() => {
      refresh();
    });
  }, [refresh]);

  const markRead = useCallback(async (id: number) => {
    await markNotificationRead(id);
    emitNotificationChanges();
  }, []);

  const markAllRead = useCallback(async () => {
    await markAllNotificationsRead(items.map((item) => item.id));
    emitNotificationChanges();
  }, [items]);

  return {
    items,
    unreadCount,
    loading,
    refresh,
    markRead,
    markAllRead,
  };
}
