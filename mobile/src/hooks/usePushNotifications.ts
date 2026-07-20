import { useEffect, useRef } from 'react';
import * as Notifications from 'expo-notifications';
import { useRouter } from 'expo-router';
import { emitNotificationChanges } from '../services/notificationEvents';
import { registerPushNotification } from '../services/notificationStorage';
import { parsePushPayload, registerForPushNotifications } from '../services/pushService';

export function usePushNotifications() {
  const router = useRouter();
  const handledInitial = useRef(false);

  useEffect(() => {
    registerForPushNotifications().catch(() => null);

    const navigateFromPayload = (data: unknown) => {
      const payload = parsePushPayload(data);

      if (payload.id) {
        registerPushNotification(payload.id).then(() => emitNotificationChanges());
      }

      if (payload.type === 'news' && payload.slug) {
        router.push({
          pathname: '/news/[slug]',
          params: { slug: payload.slug },
        } as never);
        return;
      }

      if (payload.slug || payload.id) {
        router.push({
          pathname: '/haberler/[id]',
          params: {
            id: String(payload.id ?? '0'),
            slug: payload.slug ?? '',
          },
        } as never);
      }
    };

    const receivedSubscription = Notifications.addNotificationReceivedListener((notification) => {
      const payload = parsePushPayload(notification.request.content.data);
      if (payload.id) {
        registerPushNotification(payload.id).then(() => emitNotificationChanges());
      }
    });

    const responseSubscription = Notifications.addNotificationResponseReceivedListener((response) => {
      navigateFromPayload(response.notification.request.content.data);
    });

    if (!handledInitial.current) {
      handledInitial.current = true;
      Notifications.getLastNotificationResponseAsync().then((response) => {
        if (response) {
          navigateFromPayload(response.notification.request.content.data);
        }
      });
    }

    return () => {
      receivedSubscription.remove();
      responseSubscription.remove();
    };
  }, [router]);
}
