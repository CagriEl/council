import { MaterialIcons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import { StyleSheet, Text, View } from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { BottomNavBar, TabKey } from '../../src/components/BottomNavBar';
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
        <Text style={styles.title}>Su Kartı Yükleme</Text>
        <Text style={styles.message}>
          Çok yakında kartlı su yükleme hizmeti aktif edilecektir.
        </Text>
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
  message: {
    ...typography.body,
    color: colors.onSurfaceVariant,
    textAlign: 'center',
    maxWidth: 320,
  },
});
