import { mockDirectory } from '../mock/data';
import { APP_CONFIG } from '../config';
import { fetchWithFallback, unwrapList } from './apiClient';

export type DirectoryEntry = {
  id: number;
  name: string;
  phone: string;
  address: string;
  email?: string;
  slug?: string;
};

export async function fetchDirectory(): Promise<DirectoryEntry[]> {
  try {
    const { payload } = await fetchWithFallback('/directorates', 15000);
    const items = unwrapList(payload);

    if (!items.length) return mockDirectory;

    const mapped = items.map((raw, index) => {
      const item = raw as Record<string, unknown>;
      return {
        id: Number(item.id ?? index + 1),
        name: String(item.name ?? 'Müdürlük'),
        phone: APP_CONFIG.callCenterPhone,
        address: 'Kırklareli Belediyesi',
        slug: item.slug ? String(item.slug) : undefined,
      } satisfies DirectoryEntry;
    });

    return [
      { id: 0, name: 'Alo Belediye', phone: APP_CONFIG.aloBelediyePhone, address: '7/24 Hizmet Hattı' },
      { id: -1, name: 'Çağrı Merkezi', phone: APP_CONFIG.callCenterPhone, address: 'Merkez' },
      ...mapped,
    ];
  } catch {
    return mockDirectory;
  }
}
