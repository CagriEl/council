import { MaterialIcons } from '@expo/vector-icons';
import { BlurView } from 'expo-blur';
import { Linking, Platform, Pressable, StyleSheet, Text, View } from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { APP_CONFIG } from '../config';
import { colors, radius, spacing, typography } from '../theme';

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
            <MaterialIcons
              name={tab.icon}
              size={24}
              color={active ? colors.primary : colors.onSurfaceVariant}
            />
            <Text style={[styles.label, active && styles.labelActive]}>{tab.label}</Text>
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
    backgroundColor: 'rgba(255, 255, 255, 0.95)',
  },
  row: {
    flexDirection: 'row',
    paddingTop: spacing.sm,
    paddingHorizontal: spacing.sm,
  },
  tab: {
    flex: 1,
    alignItems: 'center',
    gap: 4,
    paddingVertical: spacing.sm,
  },
  label: {
    ...typography.caption,
    fontSize: 11,
  },
  labelActive: {
    color: colors.primary,
    fontWeight: '600',
  },
});
