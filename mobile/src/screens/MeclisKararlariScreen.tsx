import { useCallback, useEffect, useRef, useState } from 'react';
import {
  FlatList,
  Linking,
  Platform,
  Pressable,
  RefreshControl,
  StyleSheet,
  Text,
  View,
} from 'react-native';
import { BlurView } from 'expo-blur';
import { useRouter } from 'expo-router';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { EmptyState } from '../components/EmptyState';
import { ScreenHeader } from '../components/ScreenHeader';
import {
  fetchCouncilDecisions,
  type CouncilDecision,
} from '../services/councilService';
import { ambientShadow, colors, radius, spacing, typography } from '../theme';

function DocButton({
  label,
  emoji,
  url,
}: {
  label: string;
  emoji: string;
  url: string | null;
}) {
  const disabled = !url;
  return (
    <Pressable
      style={[styles.docBtn, disabled && styles.docBtnDisabled]}
      disabled={disabled}
      onPress={() => url && Linking.openURL(url)}
    >
      <Text style={styles.docEmoji}>{emoji}</Text>
      <Text style={[styles.docLabel, disabled && styles.docLabelDisabled]}>{label}</Text>
    </Pressable>
  );
}

function DecisionCard({ item }: { item: CouncilDecision }) {
  const content = (
    <View style={styles.cardInner}>
      <View style={styles.cardHeader}>
        <Text style={styles.yearBadge}>{item.year}</Text>
        <Text style={styles.date}>{item.formattedDate}</Text>
      </View>
      <Text style={styles.cardTitle}>{item.title}</Text>
      <View style={styles.docRow}>
        <DocButton label="Gündem" emoji="📄" url={item.agendaFileUrl} />
        <DocButton label="Kararlar" emoji="🔨" url={item.decisionFileUrl} />
        <DocButton label="Komisyon" emoji="👥" url={item.commissionFileUrl} />
      </View>
    </View>
  );

  if (Platform.OS === 'ios') {
    return (
      <BlurView intensity={30} tint="light" style={styles.card}>
        {content}
      </BlurView>
    );
  }

  return <View style={[styles.card, styles.androidCard]}>{content}</View>;
}

export function MeclisKararlariScreen() {
  const router = useRouter();
  const insets = useSafeAreaInsets();
  const [items, setItems] = useState<CouncilDecision[]>([]);
  const [page, setPage] = useState(1);
  const [lastPage, setLastPage] = useState(1);
  const [loading, setLoading] = useState(true);
  const [loadingMore, setLoadingMore] = useState(false);
  const [refreshing, setRefreshing] = useState(false);
  const seenIds = useRef(new Set<number>());

  const load = useCallback(async (targetPage: number, reset = false) => {
    const result = await fetchCouncilDecisions(targetPage);
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
    setLoading(false);
    setLoadingMore(false);
  }, []);

  useEffect(() => {
    load(1, true);
  }, []);

  const onRefresh = async () => {
    setRefreshing(true);
    await load(1, true);
    setRefreshing(false);
  };

  const onEndReached = async () => {
    if (loadingMore || page >= lastPage) return;
    setLoadingMore(true);
    await load(page + 1);
  };

  return (
    <View style={styles.container}>
      <ScreenHeader title="Meclis Kararları" onBack={() => router.back()} />
      {loading ? (
        <EmptyState title="Yükleniyor..." />
      ) : items.length === 0 ? (
        <EmptyState title="Kayıt bulunamadı" message="Meclis kararları henüz eklenmemiş." />
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
            <RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor={colors.primary} />
          }
          onEndReached={onEndReached}
          onEndReachedThreshold={0.3}
          renderItem={({ item }) => <DecisionCard item={item} />}
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
    borderRadius: radius.lg,
    overflow: 'hidden',
    ...ambientShadow,
  },
  androidCard: {
    backgroundColor: 'rgba(255,255,255,0.9)',
  },
  cardInner: {
    padding: spacing.lg,
  },
  cardHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: spacing.sm,
  },
  yearBadge: {
    ...typography.bodyMedium,
    color: colors.white,
    backgroundColor: colors.primary,
    paddingHorizontal: spacing.md,
    paddingVertical: 4,
    borderRadius: radius.sm,
    overflow: 'hidden',
  },
  date: {
    ...typography.caption,
  },
  cardTitle: {
    ...typography.bodyMedium,
    fontFamily: typography.h2.fontFamily,
    marginBottom: spacing.md,
  },
  docRow: {
    flexDirection: 'row',
    gap: spacing.sm,
  },
  docBtn: {
    flex: 1,
    backgroundColor: colors.surfaceContainerLow,
    borderRadius: radius.md,
    padding: spacing.sm,
    alignItems: 'center',
    gap: 4,
  },
  docBtnDisabled: {
    opacity: 0.4,
  },
  docEmoji: {
    fontSize: 18,
  },
  docLabel: {
    ...typography.caption,
    textAlign: 'center',
    color: colors.onSurface,
  },
  docLabelDisabled: {
    color: colors.onSurfaceVariant,
  },
});
