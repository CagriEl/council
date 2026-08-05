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

  const openTopUp = () => {
    Linking.openURL(APP_CONFIG.baylanCardTopUpUrl);
  };

  return (
    <View style={styles.container}>
      <View style={[styles.content, { paddingBottom: insets.bottom + 100 }]}>
        <View style={styles.iconWrap}>
          <MaterialIcons name="credit-card" size={40} color={colors.primary} />
        </View>
        <Text style={styles.title}>Kartlı Su Yükleme</Text>
        <Text style={styles.message}>
          Kartlı su sayacınıza bakiye yüklemek için güvenli e-belediye portalına yönlendirileceksiniz.
          İşlem portal üzerinde tamamlanır.
        </Text>

        <Pressable style={styles.btn} onPress={openTopUp}>
          <MaterialIcons name="open-in-new" size={20} color={colors.white} />
          <Text style={styles.btnText}>Yükleme Portalını Aç</Text>
        </Pressable>

        <View style={styles.tips}>
          <Text style={styles.tipTitle}>Nasıl yapılır?</Text>
          <Text style={styles.tip}>1. Portala giriş yapın veya abone bilgilerinizi girin</Text>
          <Text style={styles.tip}>2. Kart / sayaç bilgilerinizi kontrol edin</Text>
          <Text style={styles.tip}>3. Yüklemek istediğiniz tutarı seçip ödemeyi tamamlayın</Text>
        </View>

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
  message: {
    ...typography.body,
    color: colors.onSurfaceVariant,
    textAlign: 'center',
    maxWidth: 340,
  },
  btn: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: spacing.sm,
    backgroundColor: colors.primary,
    paddingHorizontal: spacing['2xl'],
    paddingVertical: spacing.md,
    borderRadius: radius.md,
  },
  btnText: {
    ...typography.bodyMedium,
    color: colors.white,
  },
  tips: {
    width: '100%',
    maxWidth: 360,
    backgroundColor: colors.surfaceContainerLowest,
    borderRadius: radius.lg,
    padding: spacing.lg,
    gap: spacing.sm,
  },
  tipTitle: {
    ...typography.bodyMedium,
    marginBottom: spacing.xs,
  },
  tip: {
    ...typography.bodySmall,
    color: colors.onSurfaceVariant,
  },
  help: {
    ...typography.caption,
    color: colors.primary,
    fontWeight: '600',
  },
});
