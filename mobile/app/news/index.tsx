import { useCallback, useEffect, useState } from 'react';
import {
  FlatList,
  Image,
  Pressable,
  RefreshControl,
  StyleSheet,
  Text,
  View,
} from 'react-native';
import { useRouter } from 'expo-router';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { EmptyState } from '../../src/components/EmptyState';
import { ScreenHeader } from '../../src/components/ScreenHeader';
import {
  fetchNewsArticles,
  type NewsArticleItem,
} from '../../src/services/homeService';
import { ambientShadow, colors, radius, spacing, typography } from '../../src/theme';

export default function NewsListRoute() {
  const router = useRouter();
  const insets = useSafeAreaInsets();
  const [items, setItems] = useState<NewsArticleItem[]>([]);
  const [page, setPage] = useState(1);
  const [lastPage, setLastPage] = useState(1);
  const [loading, setLoading] = useState(true);
  const [loadingMore, setLoadingMore] = useState(false);
  const [refreshing, setRefreshing] = useState(false);

  const load = useCallback(async (targetPage: number, reset = false) => {
    const result = await fetchNewsArticles(targetPage);
    setItems((prev) => (reset ? result.items : [...prev, ...result.items]));
    setPage(result.currentPage);
    setLastPage(result.lastPage);
    setLoading(false);
    setLoadingMore(false);
  }, []);

  useEffect(() => {
    load(1, true);
  }, [load]);

  const openDetail = (item: NewsArticleItem) => {
    router.push({
      pathname: '/news/[slug]',
      params: { slug: item.slug },
    } as never);
  };

  return (
    <View style={styles.container}>
      <ScreenHeader title="Haberler" onBack={() => router.back()} />
      {loading ? (
        <EmptyState title="Yükleniyor..." />
      ) : items.length === 0 ? (
        <EmptyState title="Haber yok" message="Şu an gösterilecek haber bulunamadı." />
      ) : (
        <FlatList
          data={items}
          keyExtractor={(item) => String(item.id)}
          contentContainerStyle={{
            padding: spacing.xl,
            paddingBottom: insets.bottom + spacing['4xl'],
            gap: spacing.md,
          }}
          refreshControl={
            <RefreshControl
              refreshing={refreshing}
              onRefresh={async () => {
                setRefreshing(true);
                await load(1, true);
                setRefreshing(false);
              }}
              tintColor={colors.primary}
            />
          }
          onEndReached={async () => {
            if (loadingMore || page >= lastPage) return;
            setLoadingMore(true);
            await load(page + 1);
          }}
          onEndReachedThreshold={0.3}
          renderItem={({ item }) => (
            <Pressable style={styles.card} onPress={() => openDetail(item)}>
              {item.imageUrl ? (
                <Image source={{ uri: item.imageUrl }} style={styles.image} />
              ) : (
                <View style={[styles.image, styles.imagePlaceholder]}>
                  <Text style={styles.emoji}>📰</Text>
                </View>
              )}
              <View style={styles.body}>
                <Text style={styles.badge}>{item.categoryLabel}</Text>
                <Text style={styles.title} numberOfLines={2}>{item.title}</Text>
                <Text style={styles.date}>{item.formattedDate}</Text>
              </View>
            </Pressable>
          )}
        />
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: colors.background },
  card: {
    backgroundColor: colors.surfaceContainerLowest,
    borderRadius: radius.lg,
    overflow: 'hidden',
    ...ambientShadow,
  },
  image: { width: '100%', height: 160, backgroundColor: colors.surfaceContainerLow },
  imagePlaceholder: { alignItems: 'center', justifyContent: 'center' },
  emoji: { fontSize: 40 },
  body: { padding: spacing.lg, gap: spacing.xs },
  badge: {
    ...typography.caption,
    color: colors.primary,
    alignSelf: 'flex-start',
  },
  title: { ...typography.bodyMedium, fontFamily: typography.h2.fontFamily },
  date: { ...typography.caption },
});
