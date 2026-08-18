import { resolveImageUrl } from '../utils/format';
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

const INDEPENDENT_RE = /bağımsız|bagimsiz|diğer|diger/i;

const PARTY_FALLBACK_COLORS: Record<string, string> = {
  CHP: '#E30A17',
  MHP: '#C8102E',
  'AK Parti': '#F5A623',
  AKP: '#F5A623',
};

/**
 * Web sitesiyle aynı kaynak: political_party ilişkisi öncelikli.
 * (Üyeler sitede Bağımsız iken party alanında eski CHP metni kalabiliyor.)
 */
function resolvePartyName(raw: Record<string, unknown>, relation: Record<string, unknown> | null): string {
  const partyField = String(raw.party ?? '').trim();
  const relationName = relation?.name ? String(relation.name).trim() : '';
  const resolved = relationName || partyField;

  if (INDEPENDENT_RE.test(resolved)) {
    return 'Bağımsız';
  }

  return resolved;
}

function resolvePartyColor(partyName: string, relation: Record<string, unknown> | null): string | null {
  if (INDEPENDENT_RE.test(partyName)) {
    return '#64748B';
  }

  if (relation?.color) {
    return String(relation.color);
  }

  for (const [key, color] of Object.entries(PARTY_FALLBACK_COLORS)) {
    if (partyName.toUpperCase().includes(key.toUpperCase())) {
      return color;
    }
  }

  return null;
}

/**
 * Meclis üyelerini API'den çeker; parti adını tutarlı şekilde çözümler.
 */
export async function fetchCouncilMembers(): Promise<CouncilMember[]> {
  try {
    const { payload, baseUrl } = await fetchWithFallback('/council/members', 15000);
    return unwrapList(payload)
      .map((item) => {
        const raw = item as Record<string, unknown>;
        const id = Number(raw.id);
        if (!id) return null;

        const relation =
          raw.political_party && typeof raw.political_party === 'object'
            ? (raw.political_party as Record<string, unknown>)
            : null;

        const partyName = resolvePartyName(raw, relation);

        return {
          id,
          name: String(raw.name ?? ''),
          title: String(raw.title ?? 'Meclis Üyesi'),
          party: partyName,
          imageUrl: resolveImageUrl(
            typeof raw.image_url === 'string' ? raw.image_url : null,
            getStorageBase(baseUrl),
          ),
          sortOrder: Number(raw.sort_order ?? 0),
          partyColor: resolvePartyColor(partyName, relation),
          partyLogoUrl: resolveImageUrl(
            typeof relation?.logo_url === 'string' ? relation.logo_url : null,
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
