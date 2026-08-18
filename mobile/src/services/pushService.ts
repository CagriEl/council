import Constants from 'expo-constants';
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

function resolveExpoProjectId(): string | null {
  return (
    Constants.expoConfig?.extra?.eas?.projectId ??
    Constants.easConfig?.projectId ??
    null
  );
}

export async function registerForPushNotifications(): Promise<string | null> {
  if (!Device.isDevice) {
    console.warn('[push] Fiziksel cihaz gerekli');
    return null;
  }

  try {
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
      console.warn('[push] Bildirim izni verilmedi');
      return null;
    }

    const projectId = resolveExpoProjectId();
    if (!projectId) {
      console.error(
        '[push] EAS projectId eksik. app.config.ts → extra.eas.projectId ekleyin (npx eas init).',
      );
      return null;
    }

    const tokenData = await Notifications.getExpoPushTokenAsync({ projectId });
    const token = tokenData.data;

    await fetchWithFallback('/push/register', 10000, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-Platform': Platform.OS },
      body: JSON.stringify({ token, platform: Platform.OS }),
    });

    console.log('[push] Token kaydedildi');
    return token;
  } catch (error) {
    console.error('[push] Kayıt başarısız:', error);
    return null;
  }
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
