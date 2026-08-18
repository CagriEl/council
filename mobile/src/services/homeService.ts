import { formatDateTR, resolveImageUrl, stripHtml } from '../utils/format';
import type { NewsItem } from './newsService';
import { fetchWithFallback, getStorageBase, unwrapList, unwrapSingle } from './apiClient';

export type HomeSlider = {
  id: number;
  title: string;
  imageUrl: string | null;
  link: string | null;
  order: number;
};

export type HomePayload = {
  sliders: HomeSlider[];
  announcements: NewsItem[];
  mayorName?: string | null;
};

function mapAnnouncement(raw: Record<string, unknown>, baseUrl: string): NewsItem | null {
  const id = Number(raw.id);
  if (!id) return null;
  const date = String(raw.date ?? raw.published_at ?? '');
  const imagePath = raw.image_url ?? null;
  const filePath = raw.file_url ?? null;

  return {
    id,
    slug: raw.slug ? String(raw.slug) : undefined,
    title: String(raw.title ?? ''),
    excerpt: String(raw.excerpt ?? ''),
    imageUrl: resolveImageUrl(
      typeof imagePath === 'string' ? imagePath : null,
      getStorageBase(baseUrl),
    ),
    publishedAt: date,
    formattedDate: formatDateTR(date),
    isHeadline: false,
    categoryLabel: String(raw.type_label ?? 'Duyuru'),
    announcementType: String(raw.type ?? 'duyuru'),
    contentHtml: raw.content_html ? String(raw.content_html) : undefined,
    fileUrl: typeof filePath === 'string' && filePath !== '' ? filePath : null,
    hasAttachment: Boolean(raw.has_attachment),
  };
}

export async function fetchHomeFeed(): Promise<HomePayload> {
  try {
    const { payload, baseUrl } = await fetchWithFallback(
      '/home?include=announcements_by_type,mayor',
      15000,
    );

    const root = (payload && typeof payload === 'object' ? payload : {}) as Record<string, unknown>;

    const byType = (root.announcements_by_type ?? {}) as Record<string, unknown>;
    const merged: NewsItem[] = [];
    for (const key of ['duyuru', 'resmi', 'ihale'] as const) {
      const list = Array.isArray(byType[key]) ? byType[key] : [];
      for (const item of list) {
        const mapped = mapAnnouncement(item as Record<string, unknown>, baseUrl);
        if (mapped) merged.push(mapped);
      }
    }

    // Prefer duyuru first, then by date
    merged.sort((a, b) => {
      const rank = (t: string) => (t === 'duyuru' ? 0 : t === 'resmi' ? 1 : 2);
      const r = rank(a.announcementType) - rank(b.announcementType);
      if (r !== 0) return r;
      return String(b.publishedAt).localeCompare(String(a.publishedAt));
    });

    const mayorRaw = root.mayor && typeof root.mayor === 'object'
      ? (root.mayor as Record<string, unknown>)
      : null;

    return {
      sliders: [],
      announcements: merged.slice(0, 8),
      mayorName: mayorRaw?.name ? String(mayorRaw.name) : null,
    };
  } catch {
    return { sliders: [], announcements: [] };
  }
}

export type NewsArticleItem = {
  id: number;
  title: string;
  slug: string;
  summary: string;
  contentHtml: string;
  imageUrl: string | null;
  formattedDate: string;
  categoryLabel: string;
  category: string;
};

export type NewsArticlePaginated = {
  items: NewsArticleItem[];
  currentPage: number;
  lastPage: number;
};

export async function fetchNewsArticles(
  page = 1,
  kategori?: string | null,
): Promise<NewsArticlePaginated> {
  try {
    const kat = kategori ? `&kategori=${encodeURIComponent(kategori)}` : '';
    const { payload, baseUrl } = await fetchWithFallback(
      `/news?page=${page}&per_page=15${kat}`,
      15000,
    );

    const items = unwrapList(payload)
      .map((item) => {
        const raw = item as Record<string, unknown>;
        const id = Number(raw.id);
        if (!id) return null;
        const date = String(raw.published_at ?? '');
        return {
          id,
          title: String(raw.title ?? ''),
          slug: String(raw.slug ?? ''),
          summary: String(raw.summary ?? ''),
          contentHtml: String(raw.content_html ?? ''),
          imageUrl: resolveImageUrl(
            typeof raw.image_url === 'string' ? raw.image_url : null,
            getStorageBase(baseUrl),
          ),
          formattedDate: formatDateTR(date),
          categoryLabel: String(raw.category_label ?? 'Haber'),
          category: String(raw.category ?? ''),
        } satisfies NewsArticleItem;
      })
      .filter((item): item is NewsArticleItem => item !== null);

    const meta = (payload as { meta?: { current_page?: number; last_page?: number } })?.meta;

    return {
      items,
      currentPage: meta?.current_page ?? page,
      lastPage: meta?.last_page ?? 1,
    };
  } catch {
    return { items: [], currentPage: 1, lastPage: 1 };
  }
}

export async function fetchNewsArticleDetail(slug: string): Promise<NewsArticleItem | null> {
  try {
    const { payload, baseUrl } = await fetchWithFallback(`/news/${encodeURIComponent(slug)}`, 15000);
    const raw = unwrapSingle(payload);
    if (!raw) return null;
    const date = String(raw.published_at ?? '');
    return {
      id: Number(raw.id),
      title: String(raw.title ?? ''),
      slug: String(raw.slug ?? slug),
      summary: String(raw.summary ?? ''),
      contentHtml: String(raw.content_html ?? ''),
      imageUrl: resolveImageUrl(
        typeof raw.image_url === 'string' ? raw.image_url : null,
        getStorageBase(baseUrl),
      ),
      formattedDate: formatDateTR(date),
      categoryLabel: String(raw.category_label ?? 'Haber'),
      category: String(raw.category ?? ''),
    };
  } catch {
    return null;
  }
}

export function articlePlainText(article: NewsArticleItem): string {
  return stripHtml(article.contentHtml) || article.summary;
}
