import { mockMayor } from '../mock/data';
import { resolveImageUrl, stripHtml } from '../utils/format';
import { fetchWithFallback, unwrapSingle } from './apiClient';

export type MayorProfile = {
  name: string;
  title: string;
  imageUrl: string | null;
  biography: string;
  message: string;
};

export async function fetchMayorProfile(): Promise<MayorProfile> {
  try {
    const { payload } = await fetchWithFallback('/mayor');
    const raw = unwrapSingle(payload);
    if (!raw) return mockMayor;

    const imagePath = raw.image_url ?? raw.imageUrl ?? null;

    return {
      name: String(raw.name ?? mockMayor.name),
      title: String(raw.title ?? mockMayor.title),
      imageUrl: resolveImageUrl(
        typeof imagePath === 'string' ? imagePath : null,
      ),
      biography: stripHtml(String(raw.description_html ?? mockMayor.biography)),
      message: stripHtml(String(raw.message_html ?? mockMayor.message)),
    };
  } catch {
    return mockMayor;
  }
}
