const TR_MONTHS = [
  'Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran',
  'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık',
];

/** Hermes-safe date formatting — avoids new Date(isoString) */
export function formatDateTR(raw?: string | null): string {
  if (!raw) return '';
  const datePart = raw.split('T')[0];
  const [yearStr, monthStr, dayStr] = datePart.split('-');
  const year = Number(yearStr);
  const month = Number(monthStr);
  const day = Number(dayStr);
  if (!year || !month || !day) return raw;
  return `${day} ${TR_MONTHS[month - 1]} ${year}`;
}

export function stripHtml(html?: string | null): string {
  if (!html) return '';
  return html
    .replace(/<br\s*\/?>/gi, '\n')
    .replace(/<\/p>/gi, '\n\n')
    .replace(/<\/li>/gi, '\n')
    .replace(/<[^>]+>/g, '')
    .replace(/&nbsp;/g, ' ')
    .replace(/&amp;/g, '&')
    .replace(/&lt;/g, '<')
    .replace(/&gt;/g, '>')
    .replace(/\n{3,}/g, '\n\n')
    .trim();
}

const LOCAL_HOST_PATTERN = /localhost|127\.0\.0\.1|192\.168\.|\.test\b|\.local\b/i;

export function resolveImageUrl(path?: string | null, baseUrl?: string): string | null {
  if (!path) return null;
  if (path.startsWith('http://') || path.startsWith('https://')) {
    if (LOCAL_HOST_PATTERN.test(path)) return path;
    return path.replace('http://', 'https://');
  }
  if (!baseUrl) return path;
  const normalized = path.startsWith('/') ? path : `/${path}`;
  return `${baseUrl.replace(/\/$/, '')}${normalized}`;
}

export function buildNewsFallbackUrl(url: string): string {
  if (url.includes('/public/storage/')) {
    return url.replace('/public/storage/', '/storage/');
  }
  if (url.includes('/storage/')) {
    return url.replace('/storage/', '/public/storage/');
  }
  return url;
}

export function pickFullText(item: Record<string, unknown>): string {
  const keys = [
    'description', 'content_html', 'content', 'body',
    'full_text', 'text', 'detail', 'excerpt', 'summary',
  ];
  for (const key of keys) {
    const value = item[key];
    if (typeof value === 'string' && value.trim()) {
      return stripHtml(value);
    }
  }
  return '';
}

export function getTypeIcon(type?: string | null, typeLabel?: string | null): string {
  const source = `${type ?? ''} ${typeLabel ?? ''}`.toLowerCase();
  if (source.includes('ihale')) return '🏛️';
  if (source.includes('resm') || source.includes('ilan')) return '📜';
  if (source.includes('meclis')) return '⚖️';
  if (source.includes('imar') || source.includes('ruhsat')) return '🏗️';
  if (source.includes('zabıta') || source.includes('zabita')) return '🛡️';
  if (source.includes('çevre') || source.includes('cevre')) return '🌿';
  return '📢';
}
