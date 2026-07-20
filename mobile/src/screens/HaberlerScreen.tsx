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
import { useRouter } from 'expo-router';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { EmptyState } from '../components/EmptyState';
import { ScreenHeader } from '../components/ScreenHeader';
import { fetchNews, type NewsItem } from '../services/newsService';
import { getTypeIcon } from '../utils/format';
import { ambientShadow, colors, radius, spacing, typography } from '../theme';

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
        </View>
      </Pressable>
    </Animated.View>
  );
}

export function HaberlerScreen() {
  const router = useRouter();
  const insets = useSafeAreaInsets();
  const [items, setItems] = useState<NewsItem[]>([]);
  const [page, setPage] = useState(1);
  const [lastPage, setLastPage] = useState(1);
  const [loading, setLoading] = useState(true);
  const [loadingMore, setLoadingMore] = useState(false);
  const [refreshing, setRefreshing] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [scrollY, setScrollY] = useState(0);
  const seenIds = useRef(new Set<number>());

  const loadPage = useCallback(async (targetPage: number, reset = false) => {
    try {
      setError(null);
      const result = await fetchNews(targetPage);
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
  }, []);

  useEffect(() => {
    loadPage(1, true);
  }, []);

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
      },
    } as never);
  };

  return (
    <View style={styles.container}>
      <ScreenHeader
        title="Duyurular"
        scrollY={scrollY}
        onBack={() => router.back()}
      />
      {loading ? (
        <EmptyState title="Yükleniyor..." />
      ) : error ? (
        <EmptyState title="Hata" message={error} actionLabel="Tekrar Dene" onAction={() => loadPage(1, true)} />
      ) : items.length === 0 ? (
        <EmptyState title="Duyuru bulunamadı" message="Şu an gösterilecek duyuru yok." />
      ) : (
        <FlatList
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
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: colors.background,
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
    backgroundColor: 'rgba(0,102,138,0.1)',
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
});
