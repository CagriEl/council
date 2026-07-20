import { Platform } from 'react-native';
import { APP_CONFIG } from '../config';
import { fetchWithFallback, postMultipartWithFallback } from './apiClient';

export type ContactPayload = {
  name: string;
  phone: string;
  message: string;
  source?: string;
  photoUri?: string | null;
};

function guessMimeType(uri: string): string {
  const lower = uri.toLowerCase();
  if (lower.endsWith('.png')) return 'image/png';
  if (lower.endsWith('.webp')) return 'image/webp';
  if (lower.endsWith('.heic')) return 'image/heic';
  return 'image/jpeg';
}

function guessFileName(uri: string): string {
  const parts = uri.split('/');
  const last = parts[parts.length - 1];
  return last && last.includes('.') ? last : 'talep-foto.jpg';
}

/**
 * Talep formunu API'ye gönderir; fotoğraf varsa multipart olarak iletilir.
 */
export async function submitContactForm(payload: ContactPayload): Promise<void> {
  const platform = Platform.OS;

  if (payload.photoUri) {
    const formData = new FormData();
    formData.append('name', payload.name);
    formData.append('phone', payload.phone);
    formData.append('message', payload.message);
    formData.append('source', payload.source ?? 'mobile-talep');
    formData.append('subject', 'Mobil Talep Formu');
    formData.append('photo', {
      uri: payload.photoUri,
      name: guessFileName(payload.photoUri),
      type: guessMimeType(payload.photoUri),
    } as unknown as Blob);

    await postMultipartWithFallback('/contact/submit', formData, APP_CONFIG.apiTimeoutMs, {
      'X-Platform': platform,
    });
    return;
  }

  await fetchWithFallback('/contact/submit', APP_CONFIG.apiTimeoutMs, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-Platform': platform },
    body: JSON.stringify({
      name: payload.name,
      phone: payload.phone,
      message: payload.message,
      source: payload.source ?? 'mobile-talep',
      subject: 'Mobil Talep Formu',
    }),
  });
}
