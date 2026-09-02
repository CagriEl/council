import { StyleSheet, Text, View } from 'react-native';
import { MaterialIcons } from '@expo/vector-icons';
import { useRouter } from 'expo-router';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { BottomNavBar, TabKey } from '../components/BottomNavBar';
import { ScreenHeader } from '../components/ScreenHeader';
import { colors, radius, spacing, typography } from '../theme';

export function AltyapiCalismalariScreen() {
  const router = useRouter();
  const insets = useSafeAreaInsets();

  const handleTabPress = (tab: TabKey, route?: string) => {
    if (route) router.push(route as never);
  };

  return (
    <View style={styles.container}>
      <ScreenHeader title="Altyapı Çalışmaları" />

      <View style={[styles.content, { paddingBottom: insets.bottom + 100 }]}>
        <View style={styles.iconWrap}>
          <MaterialIcons name="construction" size={40} color={colors.primary} />
        </View>
        <Text style={styles.title}>İçerikler güncelleniyor</Text>
        <Text style={styles.message}>
          Altyapı çalışmaları bölümü yenileniyor. Güncel bilgiler kısa süre içinde burada
          yayınlanacaktır.
        </Text>
      </View>

      <BottomNavBar activeTab="infrastructure" onTabPress={handleTabPress} />
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
    maxWidth: 340,
  },
});
