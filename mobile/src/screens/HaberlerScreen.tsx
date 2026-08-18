import { useCallback, useEffect, useRef, useState } from 'react';
import {
  Animated,
  FlatList,
  Pressable,
  RefreshControl,
  StyleSheet,
  Text,
  View,
} from 'react-native';
import { useLocalSearchParams, useRouter } from 'expo-router';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { EmptyState } from '../components/EmptyState';
import { ScreenHeader } from '../components/ScreenHeader';
import {
  fetchAnnouncementsByType,
  type AnnouncementType,
  type NewsItem,
} from '../services/newsService';
import { getTypeIcon } from '../utils/format';
import { ambientShadow, colors, radius, spacing, typography } from '../theme';

const TABS: { key: AnnouncementType | 'all'; label: string }[] = [
  { key: 'all', label: 'Tümü' },
  { key: 'duyuru', label: 'Genel' },
  { key: 'resmi', label: 'Resmî' },
  { key: 'ihale', label: 'İhale' },
];

function NewsCard({ item, onPress }: { item: NewsItem; onPress: () => void }) {
  const opacity = useRef(new Animated.Value(0)).current;

  useEffect(() => {
    Animated.timing(opacity, { toValue: 1, duration: 280, useNativeDriver: true }).start();
  }, [opacity]);

  return (
    <Animated.View style={{ opacity }}>
      <Pressable style={styles.card} onPress={onPress}>
        <View style={styles.iconCircle}>
          <Text style={styles.iconEmoji}>{getTypeIcon(item.announcementType, item.categoryLabel)}</Text>
        </View>
        <View style={styles.content}>
          <View style={styles.metaRow}>
            <Text style={styles.badge}>{item.categoryLabel}</Text>
            <Text style={styles.date}>{item.formattedDate}</Text>
          </View>
          <Text style={styles.title} numberOfLines={2}>{item.title}</Text>
          <Text style={styles.excerpt} numberOfLines={2}>{item.excerpt}</Text>
          {item.hasAttachment ? (
            <Text style={styles.attach}>📎 Ek dosya var</Text>
          ) : null}
        </View>
      </Pressable>
    </Animated.View>
  );
}

type Props = {
  initialTip?: AnnouncementType | 'all';
  title?: string;
};

export function HaberlerScreen({ initialTip = 'all', title = 'Duyurular' }: Props) {
  const router = useRouter();
  const insets = useSafeAreaInsets();
  const params = useLocalSearchParams<{ tip?: string }>();
  const paramTip = params.tip as AnnouncementType | 'all' | undefined;
  const [tip, setTip] = useState<AnnouncementType | 'all'>(paramTip ?? initialTip);
  const [items, setItems] = useState<NewsItem[]>([]);
  const [page, setPage] = useState(1);
  const [lastPage, setLastPage] = useState(1);
  const [loading, setLoading] = useState(true);
  const [loadingMore, setLoadingMore] = useState(false);
  const [refreshing, setRefreshing] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [scrollY, setScrollY] = useState(0);
  const seenIds = useRef(new Set<number>());

  const loadPage = useCallback(async (targetPage: number, reset = false, activeTip = tip) => {
    try {
      setError(null);
      const filter = activeTip === 'all' ? null : activeTip;
      const result = await fetchAnnouncementsByType(targetPage, filter);
      setItems((prev) => {
        const next = reset ? [] : [...prev];
        if (reset) seenIds.current = new Set();
        for (const item of result.items) {
          if (!seenIds.current.has(item.id)) {
            seenIds.current.add(item.id);
            next.push(item);
          }
        }
        return next;
      });
      setPage(result.currentPage);
      setLastPage(result.lastPage);
    } catch {
      setError('Duyurular yüklenemedi.');
    } finally {
      setLoading(false);
      setLoadingMore(false);
    }
  }, [tip]);

  useEffect(() => {
    setLoading(true);
    setItems([]);
    loadPage(1, true, tip);
  }, [tip, loadPage]);

  const onRefresh = async () => {
    setRefreshing(true);
    await loadPage(1, true);
    setRefreshing(false);
  };

  const onEndReached = async () => {
    if (loadingMore || page >= lastPage) return;
    setLoadingMore(true);
    await loadPage(page + 1);
  };

  const openDetail = (item: NewsItem) => {
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
        contentHtml: item.contentHtml ?? item.excerpt,
        fileUrl: item.fileUrl ?? '',
      },
    } as never);
  };

  return (
    <View style={styles.container}>
      <ScreenHeader
        title={title}
        scrollY={scrollY}
        onBack={() => router.back()}
      />

      <View style={styles.tabs}>
        {TABS.map((tab) => {
          const active = tip === tab.key;
          return (
            <Pressable
              key={tab.key}
              style={[styles.tab, active && styles.tabActive]}
              onPress={() => {
                if (tab.key === tip) return;
                setTip(tab.key);
              }}
            >
              <Text
                style={[styles.tabText, active && styles.tabTextActive]}
                numberOfLines={1}
              >
                {tab.label}
              </Text>
            </Pressable>
          );
        })}
      </View>

      <View style={styles.listWrap}>
        {loading ? (
          <EmptyState title="Yükleniyor..." />
        ) : error ? (
          <EmptyState title="Hata" message={error} actionLabel="Tekrar Dene" onAction={() => loadPage(1, true)} />
        ) : items.length === 0 ? (
          <EmptyState title="Kayıt bulunamadı" message="Bu kategoride gösterilecek içerik yok." />
        ) : (
          <FlatList
            style={styles.list}
            data={items}
            keyExtractor={(item) => String(item.id)}
            contentContainerStyle={{
              paddingTop: spacing.md,
              paddingBottom: insets.bottom + spacing['4xl'],
              paddingHorizontal: spacing.xl,
              gap: spacing.md,
            }}
            onScroll={(e) => setScrollY(e.nativeEvent.contentOffset.y)}
            scrollEventThrottle={16}
            refreshControl={
              <RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor={colors.primary} />
            }
            onEndReached={onEndReached}
            onEndReachedThreshold={0.3}
            renderItem={({ item }) => (
              <NewsCard item={item} onPress={() => openDetail(item)} />
            )}
          />
        )}
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: colors.background,
  },
  tabs: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: spacing.lg,
    paddingVertical: spacing.sm,
    gap: spacing.sm,
  },
  tab: {
    flex: 1,
    minHeight: 40,
    paddingHorizontal: spacing.sm,
    paddingVertical: spacing.sm,
    borderRadius: radius.full,
    backgroundColor: colors.surfaceContainerLow,
    alignItems: 'center',
    justifyContent: 'center',
  },
  tabActive: {
    backgroundColor: colors.primary,
  },
  tabText: {
    ...typography.caption,
    color: colors.onSurfaceVariant,
    fontWeight: '700',
    fontSize: 13,
  },
  tabTextActive: {
    color: colors.white,
  },
  listWrap: {
    flex: 1,
    minHeight: 0,
  },
  list: {
    flex: 1,
  },
  card: {
    flexDirection: 'row',
    backgroundColor: colors.surfaceContainerLowest,
    borderRadius: radius.lg,
    padding: spacing.lg,
    gap: spacing.md,
    ...ambientShadow,
  },
  iconCircle: {
    width: 48,
    height: 48,
    borderRadius: radius.full,
    backgroundColor: colors.surfaceContainerLow,
    alignItems: 'center',
    justifyContent: 'center',
  },
  iconEmoji: {
    fontSize: 22,
  },
  content: {
    flex: 1,
  },
  metaRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: spacing.xs,
  },
  badge: {
    ...typography.caption,
    color: colors.primary,
    backgroundColor: 'rgba(11,110,153,0.1)',
    paddingHorizontal: spacing.sm,
    paddingVertical: 2,
    borderRadius: radius.sm,
    overflow: 'hidden',
  },
  date: {
    ...typography.caption,
  },
  title: {
    ...typography.bodyMedium,
    fontFamily: typography.h2.fontFamily,
    color: colors.onSurface,
    marginBottom: 4,
  },
  excerpt: {
    ...typography.bodySmall,
  },
  attach: {
    ...typography.caption,
    color: colors.tertiary,
    marginTop: spacing.xs,
  },
});
