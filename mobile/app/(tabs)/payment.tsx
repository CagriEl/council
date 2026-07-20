import { useEffect } from 'react';
import { Linking, Pressable, StyleSheet, Text, View } from 'react-native';
import { APP_CONFIG } from '../../src/config';
import { colors, radius, spacing, typography } from '../../src/theme';

export default function PaymentScreen() {
  useEffect(() => {
    Linking.openURL(APP_CONFIG.eBelediyeUrl);
  }, []);

  return (
    <View style={styles.container}>
      <Text style={styles.title}>E-Belediye</Text>
      <Text style={styles.message}>
        Ödeme işlemleri için e-belediye portalına yönlendiriliyorsunuz.
      </Text>
      <Pressable style={styles.btn} onPress={() => Linking.openURL(APP_CONFIG.eBelediyeUrl)}>
        <Text style={styles.btnText}>E-Belediye'yi Aç</Text>
      </Pressable>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: colors.background,
    alignItems: 'center',
    justifyContent: 'center',
    padding: spacing.xl,
    gap: spacing.lg,
  },
  title: {
    ...typography.h1,
  },
  message: {
    ...typography.body,
    color: colors.onSurfaceVariant,
    textAlign: 'center',
  },
  btn: {
    backgroundColor: colors.primary,
    paddingHorizontal: spacing['2xl'],
    paddingVertical: spacing.md,
    borderRadius: radius.md,
  },
  btnText: {
    ...typography.bodyMedium,
    color: colors.white,
  },
});
