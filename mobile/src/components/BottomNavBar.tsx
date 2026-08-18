import { MaterialIcons } from '@expo/vector-icons';
import { BlurView } from 'expo-blur';
import { Linking, Platform, Pressable, StyleSheet, Text, View } from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { APP_CONFIG } from '../config';
import { ambientShadow, colors, radius, spacing, typography } from '../theme';

export type TabKey = 'home' | 'payment' | 'card' | 'infrastructure' | 'directory';

type TabItem = {
  key: TabKey;
  label: string;
  icon: keyof typeof MaterialIcons.glyphMap;
  route?: string;
  external?: string;
};

const TABS: TabItem[] = [
  { key: 'home', label: 'Hizmetler', icon: 'home', route: '/' },
  { key: 'payment', label: 'Ödeme', icon: 'payment', external: APP_CONFIG.eBelediyeUrl },
  { key: 'card', label: 'Kart', icon: 'credit-card', route: '/card' },
  { key: 'infrastructure', label: 'Altyapı', icon: 'construction', route: '/infrastructure' },
  { key: 'directory', label: 'Rehber', icon: 'contacts', route: '/directory' },
];

type Props = {
  activeTab: TabKey;
  onTabPress: (tab: TabKey, route?: string) => void;
};

export function BottomNavBar({ activeTab, onTabPress }: Props) {
  const insets = useSafeAreaInsets();

  const handlePress = async (tab: TabItem) => {
    if (tab.external) {
      await Linking.openURL(tab.external);
      return;
    }
    onTabPress(tab.key, tab.route);
  };

  const content = (
    <View style={[styles.row, { paddingBottom: Math.max(insets.bottom, spacing.sm) }]}>
      {TABS.map((tab) => {
        const active = activeTab === tab.key;
        return (
          <Pressable key={tab.key} style={styles.tab} onPress={() => handlePress(tab)}>
            <View style={[styles.iconPill, active && styles.iconPillActive]}>
              <MaterialIcons
                name={tab.icon}
                size={22}
                color={active ? colors.primary : colors.onSurfaceVariant}
              />
            </View>
            <Text style={[styles.label, active && styles.labelActive]} numberOfLines={1}>
              {tab.label}
            </Text>
          </Pressable>
        );
      })}
    </View>
  );

  if (Platform.OS === 'ios') {
    return (
      <BlurView intensity={70} tint="light" style={styles.wrapper}>
        {content}
      </BlurView>
    );
  }

  return <View style={[styles.wrapper, styles.androidGlass]}>{content}</View>;
}

const styles = StyleSheet.create({
  wrapper: {
    position: 'absolute',
    left: spacing.lg,
    right: spacing.lg,
    bottom: spacing.sm,
    borderRadius: radius.xl,
    overflow: 'hidden',
  },
  androidGlass: {
    backgroundColor: 'rgba(255, 255, 255, 0.97)',
    borderWidth: 1,
    borderColor: 'rgba(11, 110, 153, 0.08)',
    ...ambientShadow,
  },
  row: {
    flexDirection: 'row',
    paddingTop: spacing.sm,
    paddingHorizontal: spacing.xs,
  },
  tab: {
    flex: 1,
    alignItems: 'center',
    gap: 2,
    paddingVertical: spacing.xs,
  },
  iconPill: {
    width: 40,
    height: 32,
    borderRadius: radius.lg,
    alignItems: 'center',
    justifyContent: 'center',
  },
  iconPillActive: {
    backgroundColor: colors.navActiveBg,
  },
  label: {
    ...typography.caption,
    fontSize: 10,
    color: colors.onSurfaceVariant,
  },
  labelActive: {
    color: colors.primary,
    fontWeight: '700',
  },
});
