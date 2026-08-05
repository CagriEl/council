import { MaterialIcons } from '@expo/vector-icons';
import { Pressable, StyleSheet, Text, View } from 'react-native';
import type { QuickAccessItem } from '../services/dashboardService';
import { ambientShadow, colors, radius, spacing, typography } from '../theme';

const ICON_MAP: Record<QuickAccessItem['icon'], keyof typeof MaterialIcons.glyphMap> = {
  badge: 'badge',
  newspaper: 'newspaper',
  pending_actions: 'pending-actions',
  schedule: 'schedule',
  contact_page: 'contact-page',
  gavel: 'gavel',
  construction: 'construction',
  campaign: 'campaign',
  account_balance: 'account-balance',
  groups: 'groups',
};

type Props = {
  items: QuickAccessItem[];
  onPress: (item: QuickAccessItem) => void;
};

function QuickAccessCard({ item, onPress }: { item: QuickAccessItem; onPress: () => void }) {
  return (
    <Pressable style={styles.card} onPress={onPress}>
      <View style={styles.iconWrap}>
        <MaterialIcons name={ICON_MAP[item.icon]} size={26} color={colors.primary} />
        {item.badge ? (
          <View style={styles.badge}>
            <Text style={styles.badgeText}>{item.badge}</Text>
          </View>
        ) : null}
      </View>
      <Text style={styles.label}>{item.label}</Text>
    </Pressable>
  );
}

export function QuickAccessGrid({ items, onPress }: Props) {
  return (
    <View style={styles.grid}>
      {items.map((item) => (
        <QuickAccessCard key={item.id} item={item} onPress={() => onPress(item)} />
      ))}
    </View>
  );
}

const styles = StyleSheet.create({
  grid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: spacing.md,
    paddingHorizontal: spacing.xl,
  },
  card: {
    width: '47%',
    backgroundColor: colors.surfaceContainerLowest,
    borderRadius: radius.lg,
    padding: spacing.lg,
    ...ambientShadow,
  },
  iconWrap: {
    width: 48,
    height: 48,
    borderRadius: radius.full,
    backgroundColor: colors.surfaceContainerLow,
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: spacing.md,
  },
  label: {
    ...typography.bodyMedium,
    fontFamily: typography.body.fontFamily,
    color: colors.onSurface,
  },
  badge: {
    position: 'absolute',
    top: -4,
    right: -4,
    minWidth: 18,
    height: 18,
    borderRadius: radius.full,
    backgroundColor: colors.tertiary,
    alignItems: 'center',
    justifyContent: 'center',
  },
  badgeText: {
    color: colors.white,
    fontSize: 10,
    fontWeight: '700',
  },
});
