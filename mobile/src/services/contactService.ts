import { Platform } from 'react-native';
import { APP_CONFIG } from '../config';
import { postMultipartWithFallback, postJsonWithFallback } from './apiClient';

export type ContactPayload = {
  name: string;
  phone: string;
  message: string;
  source?: string;
  photoUri?: string | null;
};

function guessMimeType(uri: string): string {
  const lower = uri.toLowerCase().split('?')[0];
  if (lower.endsWith('.png')) return 'image/png';
  if (lower.endsWith('.webp')) return 'image/webp';
  if (lower.endsWith('.heic')) return 'image/heic';
  return 'image/jpeg';
}

function guessFileName(uri: string): string {
  const clean = uri.split('?')[0];
  const parts = clean.split('/');
  const last = parts[parts.length - 1];
  if (last && /\.(jpe?g|png|webp|heic)$/i.test(last)) {
    return last;
  }
  return `talep-foto-${Date.now()}.jpg`;
}

/**
 * Talep formunu API'ye gönderir; fotoğraf varsa multipart olarak iletilir.
 * Mobil isteklerde X-Platform zorunlu (Turnstile muafiyeti).
 */
export async function submitContactForm(payload: ContactPayload): Promise<void> {
  const platform = Platform.OS === 'ios' ? 'ios' : 'android';
  const body = {
    name: payload.name,
    phone: payload.phone,
    message: payload.message,
    source: payload.source ?? 'mobile-talep',
    subject: 'Mobil Talep Formu',
  };

  if (payload.photoUri) {
    const formData = new FormData();
    formData.append('name', body.name);
    formData.append('phone', body.phone);
    formData.append('message', body.message);
    formData.append('source', body.source);
    formData.append('subject', body.subject);
    formData.append('photo', {
      uri: payload.photoUri,
      name: guessFileName(payload.photoUri),
      type: guessMimeType(payload.photoUri),
    } as unknown as Blob);

    await postMultipartWithFallback('/contact/submit', formData, Math.max(APP_CONFIG.apiTimeoutMs, 30000), {
      'X-Platform': platform,
    });
    return;
  }

  await postJsonWithFallback('/contact/submit', body, APP_CONFIG.apiTimeoutMs, {
    'X-Platform': platform,
  });
}
