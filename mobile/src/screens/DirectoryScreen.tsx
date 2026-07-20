import { useEffect, useState } from 'react';
import { ActivityIndicator, Linking, Pressable, ScrollView, StyleSheet, Text, View } from 'react-native';
import { MaterialIcons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { BottomNavBar, TabKey } from '../components/BottomNavBar';
import { ScreenHeader } from '../components/ScreenHeader';
import { fetchDirectory, type DirectoryEntry } from '../services/directoryService';
import { APP_CONFIG } from '../config';
import { ambientShadow, colors, radius, spacing, typography } from '../theme';

export function DirectoryScreen() {
  const router = useRouter();
  const insets = useSafeAreaInsets();
  const [entries, setEntries] = useState<DirectoryEntry[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchDirectory().then((data) => {
      setEntries(data);
      setLoading(false);
    });
  }, []);

  const handleTabPress = (tab: TabKey, route?: string) => {
    if (route) router.push(route as never);
  };

  return (
    <View style={styles.container}>
      <ScreenHeader title="Rehber" onBack={() => router.canGoBack() ? router.back() : undefined} />
      {loading ? (
        <View style={styles.centered}>
          <ActivityIndicator size="large" color={colors.primary} />
        </View>
      ) : (
        <ScrollView
          contentContainerStyle={{
            padding: spacing.xl,
            paddingBottom: insets.bottom + 100,
            gap: spacing.md,
          }}
        >
          <Pressable
            style={[styles.card, styles.highlightCard]}
            onPress={() => Linking.openURL(`tel:${APP_CONFIG.callCenterPhone}`)}
          >
            <View style={styles.iconWrap}>
              <MaterialIcons name="support-agent" size={24} color={colors.white} />
            </View>
            <View style={styles.info}>
              <Text style={[styles.name, styles.highlightText]}>Çağrı Merkezi</Text>
              <Text style={[styles.phone, styles.highlightText]}>444 01 39</Text>
            </View>
            <MaterialIcons name="phone" size={22} color={colors.white} />
          </Pressable>

          {entries.map((entry) => (
            <Pressable
              key={entry.id}
              style={styles.card}
              onPress={() => Linking.openURL(`tel:${entry.phone.replace(/\s/g, '')}`)}
            >
              <View style={styles.iconWrap}>
                <MaterialIcons name="business" size={24} color={colors.primary} />
              </View>
              <View style={styles.info}>
                <Text style={styles.name}>{entry.name}</Text>
                <Text style={styles.address}>{entry.address}</Text>
                <Text style={styles.phone}>{entry.phone}</Text>
              </View>
              <MaterialIcons name="phone" size={22} color={colors.primary} />
            </Pressable>
          ))}
        </ScrollView>
      )}
      <BottomNavBar activeTab="directory" onTabPress={handleTabPress} />
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
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: colors.surfaceContainerLowest,
    borderRadius: radius.lg,
    padding: spacing.lg,
    gap: spacing.md,
    ...ambientShadow,
  },
  highlightCard: {
    backgroundColor: colors.primary,
  },
  iconWrap: {
    width: 44,
    height: 44,
    borderRadius: radius.full,
    backgroundColor: colors.surfaceContainerLow,
    alignItems: 'center',
    justifyContent: 'center',
  },
  info: {
    flex: 1,
  },
  name: {
    ...typography.bodyMedium,
    fontFamily: typography.h2.fontFamily,
  },
  highlightText: {
    color: colors.white,
  },
  address: {
    ...typography.caption,
    marginTop: 2,
  },
  phone: {
    ...typography.bodySmall,
    color: colors.primary,
    marginTop: 4,
  },
});
