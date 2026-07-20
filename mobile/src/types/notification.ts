/** Bildirim merkezinde gösterilen duyuru kaydı. */
export type AppNotification = {
  id: number;
  title: string;
  excerpt: string;
  formattedDate: string;
  categoryLabel: string;
  slug?: string;
  imageUrl?: string | null;
};
