import { Platform } from 'react-native';
import { APP_CONFIG } from '../config';
import { postJsonWithFallback } from './apiClient';

export type ContactPayload = {
  name: string;
  phone: string;
  message: string;
  source?: string;
};

/**
 * Talep formunu JSON olarak API'ye gönderir.
 * Kaynak: mobil-app → panelde "Mobil" / Gelen Mesajlar.
 * X-Platform: Turnstile muafiyeti (android|ios).
 */
export async function submitContactForm(payload: ContactPayload): Promise<void> {
  const platform = Platform.OS === 'ios' ? 'ios' : 'android';

  await postJsonWithFallback(
    '/contact/submit',
    {
      name: payload.name,
      phone: payload.phone,
      message: payload.message,
      source: payload.source ?? 'mobil-app',
      subject: 'Mobil Uygulama Talebi',
    },
    APP_CONFIG.apiTimeoutMs,
    { 'X-Platform': platform },
  );
}
