import { useCallback, useEffect, useState } from 'react';
import {
  FlatList,
  Image,
  RefreshControl,
  StyleSheet,
  Text,
  View,
} from 'react-native';
import { useRouter } from 'expo-router';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { EmptyState } from '../src/components/EmptyState';
import { ScreenHeader } from '../src/components/ScreenHeader';
import {
  fetchCouncilMembers,
  type CouncilMember,
} from '../src/services/councilMembersService';
import { ambientShadow, colors, radius, spacing, typography } from '../src/theme';

export default function MeclisUyeleriRoute() {
  const router = useRouter();
  const insets = useSafeAreaInsets();
  const [items, setItems] = useState<CouncilMember[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  const load = useCallback(async () => {
    const data = await fetchCouncilMembers();
    setItems(data);
    setLoading(false);
  }, []);

  useEffect(() => {
    load();
  }, [load]);

  return (
    <View style={styles.container}>
      <ScreenHeader title="Meclis Üyeleri" onBack={() => router.back()} />
      {loading ? (
        <EmptyState title="Yükleniyor..." />
      ) : items.length === 0 ? (
        <EmptyState title="Üye bulunamadı" />
      ) : (
        <FlatList
          data={items}
          keyExtractor={(item) => String(item.id)}
          numColumns={2}
          columnWrapperStyle={styles.row}
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
                await load();
                setRefreshing(false);
              }}
              tintColor={colors.primary}
            />
          }
          renderItem={({ item }) => (
            <View style={styles.card}>
              {item.imageUrl ? (
                <Image source={{ uri: item.imageUrl }} style={styles.photo} />
              ) : (
                <View style={[styles.photo, styles.photoPlaceholder]}>
                  <Text style={styles.initial}>{item.name.slice(0, 1)}</Text>
                </View>
              )}
              <Text style={styles.name} numberOfLines={2}>{item.name}</Text>
              <Text style={styles.title} numberOfLines={1}>{item.title}</Text>
              {item.party ? (
                <View style={[styles.party, item.partyColor ? { backgroundColor: item.partyColor } : null]}>
                  <Text style={styles.partyText}>{item.party}</Text>
                </View>
              ) : null}
            </View>
          )}
        />
      )}
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: colors.background },
  row: { gap: spacing.md },
  card: {
    flex: 1,
    backgroundColor: colors.surfaceContainerLowest,
    borderRadius: radius.lg,
    padding: spacing.md,
    alignItems: 'center',
    ...ambientShadow,
  },
  photo: {
    width: 88,
    height: 88,
    borderRadius: radius.full,
    marginBottom: spacing.sm,
    backgroundColor: colors.surfaceContainerLow,
  },
  photoPlaceholder: { alignItems: 'center', justifyContent: 'center' },
  initial: { ...typography.h1, color: colors.primary },
  name: {
    ...typography.bodyMedium,
    textAlign: 'center',
    fontFamily: typography.h2.fontFamily,
  },
  title: { ...typography.caption, textAlign: 'center', marginTop: 2 },
  party: {
    marginTop: spacing.sm,
    backgroundColor: colors.primary,
    paddingHorizontal: spacing.sm,
    paddingVertical: 2,
    borderRadius: radius.sm,
  },
  partyText: { ...typography.caption, color: colors.white, fontWeight: '700' },
});
