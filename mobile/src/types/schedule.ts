/** Otobüs / servis güzergahı sefer saatleri. */
export type RouteSchedule = {
  id: string;
  label: string;
  color: string;
  weekday: string[];
  weekend: string[];
  notes?: Record<string, string>;
};
