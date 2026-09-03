import { StyleSheet, Text, View } from 'react-native';
import MaterialIcons from '@expo/vector-icons/MaterialIcons';
import { useRouter } from 'expo-router';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { ScreenHeader } from '../components/ScreenHeader';
import { colors, radius, spacing, typography } from '../theme';

export function SaatlerScreen() {
  const router = useRouter();
  const insets = useSafeAreaInsets();

  return (
    <View style={styles.container}>
      <ScreenHeader title="Sefer Saatleri" onBack={() => router.back()} />

      <View style={[styles.content, { paddingBottom: insets.bottom + spacing['4xl'] }]}>
        <View style={styles.iconWrap}>
          <MaterialIcons name="directions-bus" size={40} color={colors.primary} />
        </View>
        <View style={styles.badge}>
          <MaterialIcons name="schedule" size={18} color={colors.secondary} />
          <Text style={styles.badgeText}>Yakında yayında</Text>
        </View>
        <Text style={styles.title}>Sefer saatleri yakında</Text>
        <Text style={styles.message}>
          Otobüs ve servis sefer saatleri bölümü kısa süre içinde burada yayınlanacaktır.
        </Text>
      </View>
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
  title: {
    ...typography.h1,
    textAlign: 'center',
  },
  message: {
    ...typography.body,
    color: colors.onSurfaceVariant,
    textAlign: 'center',
    maxWidth: 340,
  },
});
