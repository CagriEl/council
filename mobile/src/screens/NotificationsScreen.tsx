import { useCallback, useState } from 'react';
import {
  ActivityIndicator,
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
import { useNotifications } from '../hooks/useNotifications';
import type { NotificationListItem } from '../hooks/useNotifications';
import { ambientShadow, colors, radius, spacing, typography } from '../theme';

function NotificationRow({
  item,
  onPress,
}: {
  item: NotificationListItem;
  onPress: () => void;
}) {
  return (
    <Pressable style={[styles.row, item.unread && styles.rowUnread]} onPress={onPress}>
      <View style={styles.rowContent}>
        <View style={styles.metaRow}>
          <Text style={styles.badge}>{item.categoryLabel}</Text>
          <Text style={styles.date}>{item.formattedDate}</Text>
        </View>
        <Text style={styles.title} numberOfLines={2}>{item.title}</Text>
        {item.excerpt ? (
          <Text style={styles.excerpt} numberOfLines={2}>{item.excerpt}</Text>
        ) : null}
      </View>
      {item.unread ? <View style={styles.unreadDot} /> : null}
    </Pressable>
  );
}

export function NotificationsScreen() {
  const router = useRouter();
  const insets = useSafeAreaInsets();
  const { items, loading, refresh, markRead, markAllRead } = useNotifications();
  const [refreshing, setRefreshing] = useState(false);

  const onRefresh = useCallback(async () => {
    setRefreshing(true);
    await refresh();
    setRefreshing(false);
  }, [refresh]);

  const openNotification = async (item: NotificationListItem) => {
    await markRead(item.id);
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
      },
    } as never);
  };

  return (
    <View style={styles.container}>
      <ScreenHeader title="Bildirimler" onBack={() => router.back()} />

      {!loading && items.length > 0 ? (
        <View style={styles.actionsRow}>
          <Pressable onPress={() => markAllRead()}>
            <Text style={styles.markAll}>Tümünü okundu say</Text>
          </Pressable>
        </View>
      ) : null}

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
            paddingBottom: insets.bottom + spacing['4xl'],
            flexGrow: items.length === 0 ? 1 : undefined,
          }}
          refreshControl={
            <RefreshControl refreshing={refreshing} onRefresh={onRefresh} tintColor={colors.primary} />
          }
          ListEmptyComponent={
            <EmptyState
              title="Bildirim yok"
              message="Yeni duyurular burada görünecek."
            />
          }
          renderItem={({ item }) => (
            <NotificationRow
              item={item}
              onPress={() => openNotification(item)}
            />
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
  centered: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
  },
  actionsRow: {
    alignItems: 'flex-end',
    paddingHorizontal: spacing.xl,
    paddingBottom: spacing.sm,
  },
  markAll: {
    ...typography.caption,
    color: colors.primary,
    fontWeight: '600',
  },
  row: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: colors.surfaceContainerLowest,
    borderRadius: radius.lg,
    padding: spacing.lg,
    marginBottom: spacing.md,
    gap: spacing.md,
    ...ambientShadow,
  },
  rowUnread: {
    backgroundColor: 'rgba(0,102,138,0.06)',
  },
  rowContent: {
    flex: 1,
    gap: spacing.xs,
  },
  metaRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    gap: spacing.sm,
  },
  badge: {
    ...typography.caption,
    color: colors.primary,
  },
  date: {
    ...typography.caption,
    color: colors.onSurfaceVariant,
  },
  title: {
    ...typography.bodyMedium,
  },
  excerpt: {
    ...typography.bodySmall,
    color: colors.onSurfaceVariant,
  },
  unreadDot: {
    width: 10,
    height: 10,
    borderRadius: radius.full,
    backgroundColor: colors.tertiary,
  },
});
