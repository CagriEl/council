import { mockRoutes } from '../mock/data';
import type { RouteSchedule } from '../types/schedule';
import { fetchWithFallback, unwrapList } from './apiClient';

function mapRoute(raw: Record<string, unknown>): RouteSchedule | null {
  const id = String(raw.id ?? '');
  if (!id) return null;

  const weekday = Array.isArray(raw.weekday)
    ? raw.weekday.map(String)
    : [];
  const weekend = Array.isArray(raw.weekend)
    ? raw.weekend.map(String)
    : [];

  const notesRaw = raw.notes;
  const notes =
    notesRaw && typeof notesRaw === 'object' && !Array.isArray(notesRaw)
      ? Object.fromEntries(
          Object.entries(notesRaw as Record<string, unknown>).map(([key, value]) => [
            key,
            String(value),
          ]),
        )
      : undefined;

  return {
    id,
    label: String(raw.label ?? id),
    color: String(raw.color ?? '#00668a'),
    weekday,
    weekend,
    notes,
  };
}

/**
 * Web API'den sefer saatlerini çeker; hata durumunda mock veriye düşer.
 */
export async function fetchTransportSchedules(): Promise<RouteSchedule[]> {
  try {
    const { payload } = await fetchWithFallback('/transport/schedules');
    const routes = unwrapList(payload)
      .map((item) => mapRoute(item as Record<string, unknown>))
      .filter((item): item is RouteSchedule => item !== null);

    return routes.length > 0 ? routes : mockRoutes;
  } catch {
    return mockRoutes;
  }
}
