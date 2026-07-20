import { useCallback, useEffect, useState } from 'react';
import {
  ActivityIndicator,
  FlatList,
  Platform,
  RefreshControl,
  StyleSheet,
  Text,
  View,
} from 'react-native';
import { MaterialIcons } from '@expo/vector-icons';
import { BlurView } from 'expo-blur';
import { useRouter } from 'expo-router';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { BottomNavBar, TabKey } from '../components/BottomNavBar';
import { EmptyState } from '../components/EmptyState';
import { ScreenHeader } from '../components/ScreenHeader';
import { fetchInfrastructureWorks } from '../services/infrastructureService';
import type { InfrastructureStatus, InfrastructureWork } from '../types/infrastructure';
import { ambientShadow, colors, radius, spacing, typography } from '../theme';

const STATUS_COLORS: Record<InfrastructureStatus, string> = {
  planned: colors.tertiary,
  ongoing: colors.primary,
  completed: '#2e7d4f',
};

function ProgressBar({ progress, color }: { progress: number; color: string }) {
  return (
    <View style={styles.progressTrack}>
      <View style={[styles.progressFill, { width: `${progress}%`, backgroundColor: color }]} />
    </View>
  );
}

function WorkCard({ item }: { item: InfrastructureWork }) {
  const statusColor = STATUS_COLORS[item.status];

  const content = (
    <>
      <View style={styles.cardHeader}>
        <View style={[styles.statusBadge, { backgroundColor: `${statusColor}18` }]}>
          <Text style={[styles.statusText, { color: statusColor }]}>{item.statusLabel}</Text>
        </View>
        <Text style={styles.progressLabel}>{item.progress}%</Text>
      </View>

      <Text style={styles.cardTitle}>{item.title}</Text>
      <Text style={styles.summary} numberOfLines={3}>
        {item.summary}
      </Text>

      <ProgressBar progress={item.progress} color={statusColor} />

      <View style={styles.metaRow}>
        <MaterialIcons name="place" size={16} color={colors.onSurfaceVariant} />
        <Text style={styles.metaText}>{item.location}</Text>
      </View>

      {item.formattedStartDate || item.formattedEndDate ? (
        <View style={styles.metaRow}>
          <MaterialIcons name="event" size={16} color={colors.onSurfaceVariant} />
          <Text style={styles.metaText}>
            {item.formattedStartDate}
            {item.formattedEndDate ? ` — ${item.formattedEndDate}` : ''}
          </Text>
        </View>
      ) : null}
    </>
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

export function AltyapiCalismalariScreen() {
  const router = useRouter();
  const insets = useSafeAreaInsets();
  const [items, setItems] = useState<InfrastructureWork[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  const load = useCallback(async () => {
    const data = await fetchInfrastructureWorks();
    setItems(data);
    setLoading(false);
  }, []);

  useEffect(() => {
    load();
  }, [load]);

  const onRefresh = async () => {
    setRefreshing(true);
    await load();
    setRefreshing(false);
  };

  const handleTabPress = (tab: TabKey, route?: string) => {
    if (route) router.push(route as never);
  };

  return (
    <View style={styles.container}>
      <ScreenHeader title="Altyapı Çalışmaları" />

      {loading ? (
        <View style={styles.centered}>
          <ActivityIndicator size="large" color={colors.primary} />
        </View>
      ) : (
        <FlatList
          data={items}
          keyExtractor={(item) => String(item.id)}
          contentContainerStyle={{
            padding: spacing.xl,
            paddingBottom: insets.bottom + 100,
            flexGrow: items.length === 0 ? 1 : undefined,
            gap: spacing.md,
          }}
          refreshControl={
            <RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor={colors.primary} />
          }
          ListEmptyComponent={
            <EmptyState
              title="Kayıt yok"
              message="Aktif altyapı çalışması bulunmuyor."
            />
          }
          renderItem={({ item }) => <WorkCard item={item} />}
        />
      )}

      <BottomNavBar activeTab="infrastructure" onTabPress={handleTabPress} />
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: colors.background,
  },
  centered: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
  },
  card: {
    backgroundColor: colors.surfaceContainerLowest,
    borderRadius: radius.lg,
    padding: spacing.lg,
    gap: spacing.sm,
    ...ambientShadow,
  },
  androidCard: {
    backgroundColor: 'rgba(255,255,255,0.92)',
  },
  cardHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
  },
  statusBadge: {
    paddingHorizontal: spacing.sm,
    paddingVertical: 4,
    borderRadius: radius.sm,
  },
  statusText: {
    ...typography.caption,
    fontWeight: '600',
  },
  progressLabel: {
    ...typography.caption,
    color: colors.onSurfaceVariant,
    fontWeight: '700',
  },
  cardTitle: {
    ...typography.bodyMedium,
    fontSize: 16,
  },
  summary: {
    ...typography.bodySmall,
    color: colors.onSurfaceVariant,
  },
  progressTrack: {
    height: 6,
    borderRadius: radius.full,
    backgroundColor: colors.surfaceContainerLow,
    overflow: 'hidden',
    marginTop: spacing.xs,
  },
  progressFill: {
    height: '100%',
    borderRadius: radius.full,
  },
  metaRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: spacing.xs,
    marginTop: 2,
  },
  metaText: {
    ...typography.caption,
    color: colors.onSurfaceVariant,
    flex: 1,
  },
});
