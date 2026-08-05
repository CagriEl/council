import { formatDateTR, resolveImageUrl } from '../utils/format';
import { fetchWithFallback, getStorageBase, unwrapList } from './apiClient';

export type CouncilMember = {
  id: number;
  name: string;
  title: string;
  party: string;
  imageUrl: string | null;
  sortOrder: number;
  partyColor: string | null;
  partyLogoUrl: string | null;
};

export async function fetchCouncilMembers(): Promise<CouncilMember[]> {
  try {
    const { payload, baseUrl } = await fetchWithFallback('/council/members', 15000);
    return unwrapList(payload)
      .map((item) => {
        const raw = item as Record<string, unknown>;
        const id = Number(raw.id);
        if (!id) return null;
        const party = (raw.political_party && typeof raw.political_party === 'object')
          ? (raw.political_party as Record<string, unknown>)
          : null;
        return {
          id,
          name: String(raw.name ?? ''),
          title: String(raw.title ?? 'Meclis Üyesi'),
          party: String(raw.party ?? party?.name ?? ''),
          imageUrl: resolveImageUrl(
            typeof raw.image_url === 'string' ? raw.image_url : null,
            getStorageBase(baseUrl),
          ),
          sortOrder: Number(raw.sort_order ?? 0),
          partyColor: party?.color ? String(party.color) : null,
          partyLogoUrl: resolveImageUrl(
            typeof party?.logo_url === 'string' ? party.logo_url : null,
            getStorageBase(baseUrl),
          ),
        } satisfies CouncilMember;
      })
      .filter((item): item is CouncilMember => item !== null)
      .sort((a, b) => a.sortOrder - b.sortOrder || a.name.localeCompare(b.name, 'tr'));
  } catch {
    return [];
  }
}
