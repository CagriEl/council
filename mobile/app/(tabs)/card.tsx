import { MaterialIcons } from '@expo/vector-icons';
import { Linking, Pressable, StyleSheet, Text, View } from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { useRouter } from 'expo-router';
import { BottomNavBar, TabKey } from '../../src/components/BottomNavBar';
import { APP_CONFIG } from '../../src/config';
import { colors, radius, spacing, typography } from '../../src/theme';

export default function CardScreen() {
  const router = useRouter();
  const insets = useSafeAreaInsets();

  const handleTabPress = (tab: TabKey, route?: string) => {
    if (route) router.push(route as never);
  };

  return (
    <View style={styles.container}>
      <View style={[styles.content, { paddingBottom: insets.bottom + 100 }]}>
        <View style={styles.iconWrap}>
          <MaterialIcons name="credit-card" size={40} color={colors.primary} />
        </View>
        <Text style={styles.title}>Kartlı Su Yükleme</Text>
        <View style={styles.badge}>
          <MaterialIcons name="schedule" size={18} color={colors.secondary} />
          <Text style={styles.badgeText}>Yakında yayında</Text>
        </View>
        <Text style={styles.message}>
          Kartlı su sayacınıza bakiye yükleme özelliği çok yakında uygulama içinde kullanıma
          açılacaktır.
        </Text>

        <Pressable onPress={() => Linking.openURL(`tel:${APP_CONFIG.callCenterPhone}`)}>
          <Text style={styles.help}>Yardım: {APP_CONFIG.callCenterPhone}</Text>
        </Pressable>
      </View>
      <BottomNavBar activeTab="card" onTabPress={handleTabPress} />
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: colors.background,
  },
  content: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    padding: spacing.xl,
    gap: spacing.lg,
  },
  iconWrap: {
    width: 80,
    height: 80,
    borderRadius: radius.full,
    backgroundColor: colors.surfaceContainerLow,
    alignItems: 'center',
    justifyContent: 'center',
  },
  title: {
    ...typography.h1,
    textAlign: 'center',
  },
  badge: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: spacing.xs,
    backgroundColor: colors.secondaryContainer,
    paddingHorizontal: spacing.lg,
    paddingVertical: spacing.sm,
    borderRadius: radius.full,
  },
  badgeText: {
    ...typography.bodyMedium,
    color: colors.secondary,
  },
  message: {
    ...typography.body,
    color: colors.onSurfaceVariant,
    textAlign: 'center',
    maxWidth: 340,
  },
  help: {
    ...typography.caption,
    color: colors.primary,
    fontWeight: '600',
  },
});
