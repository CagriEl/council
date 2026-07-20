import { fetchWithFallback, getStorageBase, unwrapSingle } from './apiClient';
import { formatDateTR, resolveImageUrl, stripHtml } from '../utils/format';

export type NewsArticle = {
  id: number;
  title: string;
  slug: string;
  summary: string;
  contentHtml: string;
  imageUrl: string | null;
  formattedDate: string;
  categoryLabel: string;
};

export async function fetchNewsDetail(slug: string): Promise<NewsArticle | null> {
  try {
    const { payload, baseUrl } = await fetchWithFallback(
      `/news/${encodeURIComponent(slug)}`,
      12000,
    );
    const raw = unwrapSingle(payload);
    if (!raw) return null;

    const date = String(raw.published_at ?? '');
    const imagePath = raw.image_url ?? null;

    return {
      id: Number(raw.id),
      title: String(raw.title ?? ''),
      slug: String(raw.slug ?? slug),
      summary: String(raw.summary ?? ''),
      contentHtml: String(raw.content_html ?? ''),
      imageUrl: resolveImageUrl(
        typeof imagePath === 'string' ? imagePath : null,
        getStorageBase(baseUrl),
      ),
      formattedDate: formatDateTR(date),
      categoryLabel: String(raw.category_label ?? 'Haber'),
    };
  } catch {
    return null;
  }
}

export function newsContent(article: NewsArticle): string {
  return stripHtml(article.contentHtml) || article.summary;
}
