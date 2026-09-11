import Constants from 'expo-constants';
import * as Device from 'expo-device';
import * as Notifications from 'expo-notifications';
import { AppState, Platform } from 'react-native';
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

const ANDROID_CHANNEL_ID = 'default';

let lastRegisteredToken: string | null = null;
let registering = false;

function resolveExpoProjectId(): string | null {
  return (
    Constants.expoConfig?.extra?.eas?.projectId ??
    Constants.easConfig?.projectId ??
    null
  );
}

async function ensureAndroidChannel(): Promise<void> {
  if (Platform.OS !== 'android') return;
  await Notifications.setNotificationChannelAsync(ANDROID_CHANNEL_ID, {
    name: 'Kırklareli Belediyesi',
    importance: Notifications.AndroidImportance.MAX,
    vibrationPattern: [0, 250, 250, 250],
    enableVibrate: true,
  });
}

async function persistToken(token: string): Promise<void> {
  await fetchWithFallback('/push/register', 15000, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-Platform': Platform.OS },
    body: JSON.stringify({
      token,
      platform: Platform.OS === 'ios' ? 'ios' : 'android',
    }),
  });
  lastRegisteredToken = token;
  console.log('[push] Token kaydedildi');
}

/**
 * Expo push token alır ve API'ye kaydeder.
 * Android'de google-services.json (FCM) zorunludur; yoksa token alınamaz.
 */
export async function registerForPushNotifications(): Promise<string | null> {
  if (registering) return lastRegisteredToken;
  registering = true;

  try {
    if (!Device.isDevice) {
      console.warn('[push] Fiziksel cihaz gerekli (emülatörde FCM token alınamaz)');
      return null;
    }

    await ensureAndroidChannel();

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
        '[push] EAS projectId eksik. app.config.ts → extra.eas.projectId ekleyin.',
      );
      return null;
    }

    let token: string | null = null;
    let lastError: unknown = null;

    for (let attempt = 0; attempt < 3; attempt++) {
      try {
        const tokenData = await Notifications.getExpoPushTokenAsync({ projectId });
        token = tokenData.data;
        break;
      } catch (error) {
        lastError = error;
        console.warn(`[push] Token alma denemesi ${attempt + 1} başarısız:`, error);
        await new Promise((resolve) => setTimeout(resolve, 800 * (attempt + 1)));
      }
    }

    if (!token) {
      console.error(
        '[push] Expo push token alınamadı. Android için google-services.json + FCM V1 credentials gerekir.',
        lastError,
      );
      return null;
    }

    if (token === lastRegisteredToken) {
      return token;
    }

    await persistToken(token);
    return token;
  } catch (error) {
    console.error('[push] Kayıt başarısız:', error);
    return null;
  } finally {
    registering = false;
  }
}

/** Uygulama öne gelince token'ı yenile / yeniden kaydet. */
export function startPushRegistrationLifecycle(): () => void {
  const run = () => {
    registerForPushNotifications().catch(() => undefined);
  };

  run();
  const sub = AppState.addEventListener('change', (state) => {
    if (state === 'active') run();
  });

  return () => sub.remove();
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
