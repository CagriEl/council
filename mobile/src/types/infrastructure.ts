/** Altyapı çalışması durumu. */
export type InfrastructureStatus = 'planned' | 'ongoing' | 'completed';

/** Site / API'den gelen altyapı çalışması kaydı. */
export type InfrastructureWork = {
  id: number;
  title: string;
  summary: string;
  location: string;
  status: InfrastructureStatus;
  statusLabel: string;
  progress: number;
  startedAt: string;
  estimatedEndAt: string | null;
  formattedStartDate: string;
  formattedEndDate: string;
};
