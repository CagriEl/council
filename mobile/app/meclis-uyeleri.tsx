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

function isLightColor(hex: string): boolean {
  const raw = hex.replace('#', '');
  if (raw.length < 6) return false;
  const r = parseInt(raw.slice(0, 2), 16);
  const g = parseInt(raw.slice(2, 4), 16);
  const b = parseInt(raw.slice(4, 6), 16);
  return (r * 299 + g * 587 + b * 114) / 1000 > 160;
}

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
          renderItem={({ item }) => {
            const partyBg = item.partyColor ?? colors.primary;
            const lightParty = isLightColor(partyBg);
            return (
              <View style={styles.card}>
                <View style={[styles.accentBar, { backgroundColor: partyBg }]} />
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
                  <View style={[styles.party, { backgroundColor: partyBg }]}>
                    <Text style={[styles.partyText, lightParty && styles.partyTextDark]}>
                      {item.party}
                    </Text>
                  </View>
                ) : null}
              </View>
            );
          }}
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
    borderRadius: radius.xl,
    padding: spacing.md,
    alignItems: 'center',
    overflow: 'hidden',
    borderWidth: 1,
    borderColor: 'rgba(11, 110, 153, 0.06)',
    ...ambientShadow,
  },
  accentBar: {
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
    height: 4,
  },
  photo: {
    width: 88,
    height: 88,
    borderRadius: radius.full,
    marginTop: spacing.sm,
    marginBottom: spacing.sm,
    backgroundColor: colors.primarySoft,
    borderWidth: 3,
    borderColor: colors.white,
  },
  photoPlaceholder: { alignItems: 'center', justifyContent: 'center' },
  initial: { ...typography.h1, color: colors.primary },
  name: {
    ...typography.bodyMedium,
    textAlign: 'center',
    fontFamily: typography.h2.fontFamily,
    color: colors.onSurface,
  },
  title: { ...typography.caption, textAlign: 'center', marginTop: 2, color: colors.onSurfaceVariant },
  party: {
    marginTop: spacing.sm,
    paddingHorizontal: spacing.sm,
    paddingVertical: 4,
    borderRadius: radius.full,
  },
  partyText: { ...typography.caption, color: colors.white, fontWeight: '700' },
  partyTextDark: { color: colors.onSurface },
});
