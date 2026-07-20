import { mockInfrastructureWorks } from '../mock/data';
import type { InfrastructureStatus, InfrastructureWork } from '../types/infrastructure';
import { formatDateTR } from '../utils/format';
import { fetchWithFallback, unwrapList } from './apiClient';

const STATUS_LABELS: Record<InfrastructureStatus, string> = {
  planned: 'Planlandı',
  ongoing: 'Devam Ediyor',
  completed: 'Tamamlandı',
};

function mapStatus(raw: unknown): InfrastructureStatus {
  const value = String(raw ?? 'planned');
  if (value === 'ongoing' || value === 'completed' || value === 'planned') {
    return value;
  }
  return 'planned';
}

/**
 * API kaydını mobil tipine dönüştürür.
 */
function mapWork(raw: Record<string, unknown>): InfrastructureWork | null {
  const id = Number(raw.id);
  if (!id) return null;

  const status = mapStatus(raw.status);
  const startedAt = String(raw.started_at ?? raw.startedAt ?? '');
  const estimatedEndAt = raw.estimated_end_at ?? raw.estimatedEndAt;
  const endRaw = estimatedEndAt ? String(estimatedEndAt) : null;

  return {
    id,
    title: String(raw.title ?? ''),
    summary: String(raw.summary ?? raw.description ?? ''),
    location: String(raw.location ?? ''),
    status,
    statusLabel: String(raw.status_label ?? raw.statusLabel ?? STATUS_LABELS[status]),
    progress: Math.min(100, Math.max(0, Number(raw.progress ?? 0))),
    startedAt,
    estimatedEndAt: endRaw,
    formattedStartDate: formatDateTR(startedAt),
    formattedEndDate: formatDateTR(endRaw),
  };
}

/**
 * Web API'den altyapı çalışmalarını çeker; hata durumunda demo veriye düşer.
 */
export async function fetchInfrastructureWorks(): Promise<InfrastructureWork[]> {
  try {
    const { payload } = await fetchWithFallback('/infrastructure-works');
    const items = unwrapList(payload)
      .map((item) => mapWork(item as Record<string, unknown>))
      .filter((item): item is InfrastructureWork => item !== null);

    return items.length > 0 ? items : mockInfrastructureWorks;
  } catch {
    return mockInfrastructureWorks;
  }
}
