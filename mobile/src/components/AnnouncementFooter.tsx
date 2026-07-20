import { MaterialIcons } from '@expo/vector-icons';
import { Linking, Pressable, StyleSheet, Text, View } from 'react-native';
import { APP_CONFIG } from '../config';
import { ambientShadow, colors, radius, spacing, typography } from '../theme';

type SocialLink = { id: string; label: string; url: string };

type Props = {
  socialLinks: SocialLink[];
};

export function AnnouncementFooter({ socialLinks }: Props) {
  return (
    <View style={styles.row}>
      <View style={[styles.card, styles.labelCard]}>
        <MaterialIcons name="campaign" size={22} color={colors.primary} />
        <Text style={styles.labelText}>Resmi{'\n'}duyurular</Text>
      </View>

      <Pressable
        style={[styles.card, styles.phoneCard]}
        onPress={() => Linking.openURL(`tel:${APP_CONFIG.aloBelediyePhone}`)}
      >
        <Text style={styles.phoneNumber}>153</Text>
        <Text style={styles.phoneLabel}>Alo Belediye</Text>
      </Pressable>

      <View style={[styles.card, styles.socialCard]}>
        {socialLinks.map((link) => (
          <Pressable key={link.id} onPress={() => Linking.openURL(link.url)}>
            <MaterialIcons name="photo-camera" size={24} color={colors.primary} />
          </Pressable>
        ))}
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  row: {
    flexDirection: 'row',
    gap: spacing.md,
    paddingHorizontal: spacing.xl,
    marginTop: spacing['2xl'],
  },
  card: {
    flex: 1,
    backgroundColor: colors.surfaceContainerLowest,
    borderRadius: radius.lg,
    padding: spacing.lg,
    alignItems: 'center',
    justifyContent: 'center',
    minHeight: 88,
    ...ambientShadow,
  },
  labelCard: {
    gap: spacing.sm,
  },
  labelText: {
    ...typography.caption,
    textAlign: 'center',
    color: colors.onSurface,
  },
  phoneCard: {
    backgroundColor: colors.primary,
  },
  phoneNumber: {
    ...typography.h1,
    color: colors.white,
    fontSize: 28,
  },
  phoneLabel: {
    ...typography.caption,
    color: 'rgba(255,255,255,0.85)',
    marginTop: 2,
  },
  socialCard: {
    flexDirection: 'row',
    gap: spacing.md,
  },
});
