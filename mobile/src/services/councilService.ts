import { formatDateTR } from '../utils/format';
import { fetchWithFallback, unwrapList, unwrapMeta } from './apiClient';

export type CouncilDecision = {
  id: number;
  year: number;
  month?: string | null;
  title: string;
  meetingDate: string;
  formattedDate: string;
  agendaFileUrl: string | null;
  decisionFileUrl: string | null;
  commissionFileUrl: string | null;
};

export type CouncilPaginatedResult = {
  items: CouncilDecision[];
  currentPage: number;
  lastPage: number;
};

function mapDecision(raw: Record<string, unknown>): CouncilDecision | null {
  const id = Number(raw.id);
  if (!id) return null;

  const meetingDate = String(raw.meeting_date ?? '');
  return {
    id,
    year: Number(raw.year ?? new Date().getFullYear()),
    month: raw.month ? String(raw.month) : null,
    title: String(raw.title ?? 'Meclis Kararı'),
    meetingDate,
    formattedDate: formatDateTR(meetingDate),
    agendaFileUrl: raw.agenda_file_url ? String(raw.agenda_file_url) : null,
    decisionFileUrl: raw.decision_file_url ? String(raw.decision_file_url) : null,
    commissionFileUrl: raw.commission_file_url ? String(raw.commission_file_url) : null,
  };
}

export async function fetchCouncilDecisions(page = 1): Promise<CouncilPaginatedResult> {
  try {
    const { payload } = await fetchWithFallback(
      `/council/decisions?page=${page}&per_page=15`,
    );

    const items = unwrapList(payload)
      .map((item) => mapDecision(item as Record<string, unknown>))
      .filter((item): item is CouncilDecision => item !== null);

    const meta = unwrapMeta(payload);

    return {
      items,
      currentPage: meta?.current_page ?? page,
      lastPage: meta?.last_page ?? 1,
    };
  } catch {
    return { items: [], currentPage: 1, lastPage: 1 };
  }
}
