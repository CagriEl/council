import { useCallback, useEffect, useState } from 'react';
import {
  Pressable,
  RefreshControl,
  ScrollView,
  StyleSheet,
  Text,
  View,
} from 'react-native';
import { useRouter } from 'expo-router';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { AnnouncementFooter } from '../components/AnnouncementFooter';
import { BottomNavBar, TabKey } from '../components/BottomNavBar';
import { HeroBanner } from '../components/HeroBanner';
import { QuickAccessGrid } from '../components/QuickAccessGrid';
import { TopAppBar } from '../components/TopAppBar';
import { useNotifications } from '../hooks/useNotifications';
import {
  fetchAnnouncements,
  fetchHomeFeed,
  fetchQuickAccessItems,
  fetchSocialLinks,
  type QuickAccessItem,
} from '../services/dashboardService';
import type { NewsItem } from '../services/newsService';
import { colors, spacing, typography } from '../theme';

export function DashboardScreen() {
  const router = useRouter();
  const insets = useSafeAreaInsets();
  const [announcements, setAnnouncements] = useState<NewsItem[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);
  const { unreadCount, refresh: refreshNotifications } = useNotifications();
  const quickItems = fetchQuickAccessItems();
  const socialLinks = fetchSocialLinks();

  const load = useCallback(async () => {
    const [home, hero] = await Promise.all([fetchHomeFeed(), fetchAnnouncements()]);
    setAnnouncements(home.announcements.length > 0 ? home.announcements.slice(0, 5) : hero);
    setLoading(false);
    await refreshNotifications();
  }, [refreshNotifications]);

  useEffect(() => {
    load();
  }, [load]);

  const onRefresh = async () => {
    setRefreshing(true);
    await load();
    setRefreshing(false);
  };

  const handleQuickPress = (item: QuickAccessItem) => {
    router.push(item.route as never);
  };

  const handleAnnouncementPress = (item: NewsItem) => {
    router.push({
      pathname: '/haberler/[id]',
      params: {
        id: String(item.id),
        slug: item.slug ?? '',
        title: item.title,
        imageUrl: item.imageUrl ?? '',
        excerpt: item.excerpt,
        formattedDate: item.formattedDate,
        categoryLabel: item.categoryLabel,
        contentHtml: item.contentHtml ?? '',
        fileUrl: item.fileUrl ?? '',
      },
    } as never);
  };

  const handleTabPress = (tab: TabKey, route?: string) => {
    if (route) router.push(route as never);
  };

  return (
    <View style={styles.container}>
      <TopAppBar
        notificationCount={unreadCount > 0 ? Math.min(unreadCount, 99) : 0}
        onNotificationPress={() => router.push('/notifications' as never)}
      />
      <ScrollView
        contentContainerStyle={{
          paddingTop: insets.top + 80,
          paddingBottom: insets.bottom + 100,
        }}
        refreshControl={
          <RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor={colors.primary} />
        }
      >
        <View style={styles.section}>
          <View style={styles.sectionHeader}>
            <Text style={styles.sectionTitle}>Güncel Duyurular</Text>
            <Pressable onPress={() => router.push('/haberler' as never)}>
              <Text style={styles.link}>Tümü</Text>
            </Pressable>
          </View>
          <HeroBanner
            items={announcements}
            loading={loading}
            onPressItem={handleAnnouncementPress}
          />
        </View>

        <View style={styles.section}>
          <Text style={styles.sectionTitlePadded}>Hızlı Erişim</Text>
          <QuickAccessGrid items={quickItems} onPress={handleQuickPress} />
        </View>

        <AnnouncementFooter socialLinks={socialLinks} />
      </ScrollView>
      <BottomNavBar activeTab="home" onTabPress={handleTabPress} />
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: colors.background,
  },
  section: {
    marginTop: spacing['2xl'],
  },
  sectionHeader: {
    paddingHorizontal: spacing.xl,
    marginBottom: spacing.lg,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
  },
  sectionTitle: {
    ...typography.h2,
    color: colors.primaryDark,
  },
  link: {
    ...typography.caption,
    color: colors.secondary,
    fontWeight: '700',
  },
  sectionTitlePadded: {
    ...typography.h2,
    paddingHorizontal: spacing.xl,
    marginBottom: spacing.lg,
    color: colors.primaryDark,
  },
});
