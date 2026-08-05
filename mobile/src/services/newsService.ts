import { mockAnnouncements } from '../mock/data';
import { formatDateTR, resolveImageUrl } from '../utils/format';
import { fetchWithFallback, getStorageBase, unwrapList, unwrapMeta, unwrapSingle } from './apiClient';

export type AnnouncementType = 'duyuru' | 'resmi' | 'ihale';

export type NewsItem = {
  id: number;
  title: string;
  excerpt: string;
  imageUrl: string | null;
  publishedAt: string;
  formattedDate: string;
  isHeadline: boolean;
  categoryLabel: string;
  announcementType: string;
  slug?: string;
  contentHtml?: string;
  fileUrl?: string | null;
  hasAttachment?: boolean;
};

export type NewsPaginatedResult = {
  items: NewsItem[];
  currentPage: number;
  lastPage: number;
  total: number;
};

function mapRawItem(raw: Record<string, unknown>, baseUrl: string): NewsItem | null {
  const id = Number(raw.id);
  if (!id) return null;

  const type = String(raw.type ?? raw.announcementType ?? 'duyuru');
  const date = String(raw.date ?? raw.published_at ?? raw.publishedAt ?? '');
  const imagePath = raw.image_url ?? raw.imageUrl ?? null;
  const filePath = raw.file_url ?? raw.fileUrl ?? null;

  return {
    id,
    slug: raw.slug ? String(raw.slug) : undefined,
    title: String(raw.title ?? ''),
    excerpt: String(raw.excerpt ?? raw.summary ?? ''),
    imageUrl: resolveImageUrl(
      typeof imagePath === 'string' ? imagePath : null,
      getStorageBase(baseUrl),
    ),
    publishedAt: date,
    formattedDate: formatDateTR(date),
    isHeadline: Boolean(raw.is_headline ?? raw.isHeadline ?? false),
    categoryLabel: String(raw.type_label ?? raw.categoryLabel ?? 'Duyuru'),
    announcementType: type,
    contentHtml: raw.content_html ? String(raw.content_html) : undefined,
    fileUrl: typeof filePath === 'string' && filePath !== '' ? filePath : null,
    hasAttachment: Boolean(raw.has_attachment ?? (typeof filePath === 'string' && filePath !== '')),
  };
}

export async function fetchAnnouncementsByType(
  page = 1,
  tip?: AnnouncementType | null,
  perPage = 20,
): Promise<NewsPaginatedResult> {
  try {
    const tipQuery = tip ? `&tip=${encodeURIComponent(tip)}` : '';
    const { payload, baseUrl } = await fetchWithFallback(
      `/announcements?page=${page}&per_page=${perPage}${tipQuery}`,
      15000,
    );

    const rawItems = unwrapList(payload)
      .map((item) => mapRawItem(item as Record<string, unknown>, baseUrl))
      .filter((item): item is NewsItem => item !== null);

    const meta = unwrapMeta(payload);

    return {
      items: rawItems,
      currentPage: meta?.current_page ?? page,
      lastPage: meta?.last_page ?? 1,
      total: meta?.total ?? rawItems.length,
    };
  } catch {
    const filtered = tip
      ? mockAnnouncements.filter((item) => item.announcementType === tip)
      : mockAnnouncements;
    return {
      items: filtered,
      currentPage: 1,
      lastPage: 1,
      total: filtered.length,
    };
  }
}

/** @deprecated Use fetchAnnouncementsByType — kept for existing imports */
export async function fetchNews(page = 1): Promise<NewsPaginatedResult> {
  return fetchAnnouncementsByType(page, 'duyuru');
}

export async function fetchAnnouncementDetail(slug: string): Promise<NewsItem | null> {
  try {
    const { payload, baseUrl } = await fetchWithFallback(
      `/announcements/${encodeURIComponent(slug)}`,
      15000,
    );
    const raw = unwrapSingle(payload);
    if (!raw) return null;
    return mapRawItem(raw, baseUrl);
  } catch {
    return null;
  }
}

export async function fetchAnnouncementById(id: number): Promise<NewsItem | null> {
  try {
    const { payload, baseUrl } = await fetchWithFallback(
      `/announcements?page=1&per_page=50`,
    );
    const items = unwrapList(payload)
      .map((item) => mapRawItem(item as Record<string, unknown>, baseUrl))
      .filter((item): item is NewsItem => item !== null);

    const found = items.find((item) => item.id === id);
    if (found?.slug) {
      const detail = await fetchAnnouncementDetail(found.slug);
      if (detail) return detail;
    }
    return found ?? null;
  } catch {
    return mockAnnouncements.find((item) => item.id === id) ?? null;
  }
}

export async function fetchHeroAnnouncements(): Promise<NewsItem[]> {
  const result = await fetchAnnouncementsByType(1, 'duyuru', 5);
  return result.items.slice(0, 5);
}

export async function fetchAnnouncementCount(tip?: AnnouncementType): Promise<number> {
  try {
    const tipQuery = tip ? `&tip=${encodeURIComponent(tip)}` : '';
    const { payload } = await fetchWithFallback(`/announcements?page=1&per_page=1${tipQuery}`);
    const meta = unwrapMeta(payload);
    return meta?.total ?? 0;
  } catch {
    return 0;
  }
}
