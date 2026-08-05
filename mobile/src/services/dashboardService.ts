import {
  mockAnnouncements,
  mockQuickAccessItems,
  mockSocialLinks,
} from '../mock/data';
import { fetchHomeFeed } from './homeService';
import type { NewsItem } from './newsService';
import { fetchAnnouncementCount, fetchHeroAnnouncements } from './newsService';

export type QuickAccessItem = {
  id: string;
  label: string;
  icon:
    | 'badge'
    | 'newspaper'
    | 'pending_actions'
    | 'schedule'
    | 'contact_page'
    | 'gavel'
    | 'construction'
    | 'campaign'
    | 'account_balance'
    | 'groups';
  route: string;
  badge?: number;
};

export type DashboardStats = {
  announcementCount: number;
};

export async function fetchAnnouncements(): Promise<NewsItem[]> {
  try {
    const home = await fetchHomeFeed();
    if (home.announcements.length > 0) {
      return home.announcements.slice(0, 5);
    }
    return await fetchHeroAnnouncements();
  } catch {
    return mockAnnouncements.slice(0, 3);
  }
}

export function fetchQuickAccessItems(): QuickAccessItem[] {
  return mockQuickAccessItems;
}

export async function fetchDashboardStats(): Promise<DashboardStats> {
  const count = await fetchAnnouncementCount();
  return { announcementCount: count || 0 };
}

export function fetchSocialLinks() {
  return mockSocialLinks;
}

export { fetchHomeFeed };
