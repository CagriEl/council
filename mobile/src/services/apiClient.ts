import { APP_CONFIG } from '../config';

type ApiEnvelope<T> = {
  data?: T | { data?: T; meta?: PaginationMeta };
  meta?: PaginationMeta;
};

export type PaginationMeta = {
  current_page?: number;
  last_page?: number;
  per_page?: number;
  total?: number;
};

export async function fetchWithFallback(
  path: string,
  timeoutMs: number = APP_CONFIG.apiTimeoutMs,
  init?: RequestInit,
): Promise<{ payload: unknown; baseUrl: string }> {
  const errors: string[] = [];

  for (const baseUrl of APP_CONFIG.apiBaseUrls) {
    const controller = new AbortController();
    const timer = setTimeout(() => controller.abort(), timeoutMs);

    try {
      const response = await fetch(`${baseUrl}${path}`, {
        ...init,
        signal: controller.signal,
        headers: {
          Accept: 'application/json',
          'Cache-Control': 'no-cache',
          ...(init?.headers ?? {}),
        },
      });
      clearTimeout(timer);

      const payload = await response.json().catch(() => null);
      if (response.ok && payload !== null) {
        return { payload, baseUrl };
      }
      errors.push(`${baseUrl}${path} -> ${response.status}`);
    } catch (error) {
      clearTimeout(timer);
      const message = error instanceof Error ? error.message : 'unknown';
      errors.push(`${baseUrl}${path} -> ${message}`);
    }
  }

  throw new Error(errors.join(' | '));
}

/**
 * Multipart gövde (FormData) ile POST; Content-Type otomatik boundary alır.
 */
export async function postMultipartWithFallback(
  path: string,
  formData: FormData,
  timeoutMs: number = APP_CONFIG.apiTimeoutMs,
  extraHeaders?: Record<string, string>,
): Promise<{ payload: unknown; baseUrl: string }> {
  const errors: string[] = [];

  for (const baseUrl of APP_CONFIG.apiBaseUrls) {
    const controller = new AbortController();
    const timer = setTimeout(() => controller.abort(), timeoutMs);

    try {
      const response = await fetch(`${baseUrl}${path}`, {
        method: 'POST',
        body: formData,
        signal: controller.signal,
        headers: {
          Accept: 'application/json',
          ...(extraHeaders ?? {}),
        },
      });
      clearTimeout(timer);

      const payload = await response.json().catch(() => null);
      if (response.ok && payload !== null) {
        return { payload, baseUrl };
      }
      errors.push(`${baseUrl}${path} -> ${response.status}`);
    } catch (error) {
      clearTimeout(timer);
      const message = error instanceof Error ? error.message : 'unknown';
      errors.push(`${baseUrl}${path} -> ${message}`);
    }
  }

  throw new Error(errors.join(' | '));
}

export function unwrapList(payload: unknown): unknown[] {
  if (Array.isArray(payload)) return payload;
  if (!payload || typeof payload !== 'object') return [];

  const root = payload as ApiEnvelope<unknown[]>;
  if (Array.isArray(root.data)) return root.data;

  if (root.data && typeof root.data === 'object') {
    const nested = root.data as { data?: unknown[] };
    if (Array.isArray(nested.data)) return nested.data;
  }

  return [];
}

export function unwrapMeta(payload: unknown): PaginationMeta | null {
  if (!payload || typeof payload !== 'object') return null;
  const root = payload as ApiEnvelope<unknown[]>;
  if (root.meta) return root.meta;
  if (root.data && typeof root.data === 'object') {
    const nested = root.data as { meta?: PaginationMeta };
    return nested.meta ?? null;
  }
  return null;
}

export function unwrapSingle(payload: unknown): Record<string, unknown> | null {
  if (!payload || typeof payload !== 'object') return null;
  const root = payload as ApiEnvelope<Record<string, unknown>>;
  if (root.data && !Array.isArray(root.data)) {
    if (typeof root.data === 'object' && 'data' in (root.data as object)) {
      return null;
    }
    return root.data as Record<string, unknown>;
  }
  if (Array.isArray(payload)) {
    return (payload[0] as Record<string, unknown>) ?? null;
  }
  return payload as Record<string, unknown>;
}

export function getStorageBase(baseUrl: string): string {
  return baseUrl.replace(/\/api$/, '');
}
