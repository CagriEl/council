import * as Device from 'expo-device';
import * as Notifications from 'expo-notifications';
import { Platform } from 'react-native';
import { fetchWithFallback } from './apiClient';

Notifications.setNotificationHandler({
  handleNotification: async () => ({
    shouldShowAlert: true,
    shouldPlaySound: true,
    shouldSetBadge: true,
    shouldShowBanner: true,
    shouldShowList: true,
  }),
});

export type PushPayload = {
  type?: 'announcement' | 'news';
  id?: number;
  slug?: string;
};

export async function registerForPushNotifications(): Promise<string | null> {
  if (!Device.isDevice) {
    return null;
  }

  if (Platform.OS === 'android') {
    await Notifications.setNotificationChannelAsync('default', {
      name: 'Kırklareli Belediyesi',
      importance: Notifications.AndroidImportance.MAX,
      vibrationPattern: [0, 250, 250, 250],
    });
  }

  const { status: existingStatus } = await Notifications.getPermissionsAsync();
  let finalStatus = existingStatus;

  if (existingStatus !== 'granted') {
    const { status } = await Notifications.requestPermissionsAsync();
    finalStatus = status;
  }

  if (finalStatus !== 'granted') {
    return null;
  }

  const tokenData = await Notifications.getExpoPushTokenAsync();
  const token = tokenData.data;

  await fetchWithFallback('/push/register', 8000, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-Platform': Platform.OS },
    body: JSON.stringify({ token, platform: Platform.OS }),
  }).catch(() => null);

  return token;
}

export function parsePushPayload(data: unknown): PushPayload {
  if (!data || typeof data !== 'object') return {};
  const payload = data as Record<string, unknown>;
  return {
    type: payload.type as PushPayload['type'],
    id: payload.id ? Number(payload.id) : undefined,
    slug: payload.slug ? String(payload.slug) : undefined,
  };
}
