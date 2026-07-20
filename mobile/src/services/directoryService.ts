import { mockDirectory } from '../mock/data';
import { fetchWithFallback, unwrapList } from './apiClient';

export type DirectoryEntry = {
  id: number;
  name: string;
  phone: string;
  address: string;
};

export async function fetchDirectory(): Promise<DirectoryEntry[]> {
  try {
    const { payload } = await fetchWithFallback('/directorates');
    const items = unwrapList(payload);

    if (!items.length) return mockDirectory;

    return items.map((raw, index) => {
      const item = raw as Record<string, unknown>;
      return {
        id: Number(item.id ?? index + 1),
        name: String(item.name ?? 'Müdürlük'),
        phone: '444 01 39',
        address: 'Kırklareli Belediyesi',
      };
    });
  } catch {
    return mockDirectory;
  }
}
