import MaterialIcons from '@expo/vector-icons/MaterialIcons';
import type { ReactNode } from 'react';
import { Pressable, StyleSheet, Text, View } from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { colors, spacing, typography } from '../theme';

type Props = {
  title: string;
  scrollY?: number;
  onBack?: () => void;
  transparent?: boolean;
  rightAction?: ReactNode;
};

export function ScreenHeader({ title, scrollY = 0, onBack, transparent = false, rightAction }: Props) {
  const insets = useSafeAreaInsets();
  const opaque = scrollY > 40;
  const bgOpacity = transparent ? Math.min(scrollY / 80, 0.97) : 1;

  return (
    <View
      style={[
        styles.wrap,
        {
          paddingTop: insets.top + spacing.sm,
          backgroundColor: transparent
            ? `rgba(247, 249, 251, ${bgOpacity})`
            : colors.background,
          borderBottomWidth: opaque ? 1 : 0,
          borderBottomColor: 'rgba(0,102,138,0.08)',
        },
      ]}
    >
      <View style={styles.row}>
        {onBack ? (
          <Pressable style={styles.backBtn} onPress={onBack}>
            <MaterialIcons
              name="arrow-back"
              size={22}
              color={transparent && !opaque ? colors.white : colors.primary}
            />
          </Pressable>
        ) : (
          <View style={styles.sideSlot} />
        )}
        <Text
          style={[
            styles.title,
            transparent && !opaque && styles.titleLight,
          ]}
          numberOfLines={1}
        >
          {title}
        </Text>
        <View style={styles.sideSlot}>{rightAction ?? null}</View>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  wrap: {
    zIndex: 10,
  },
  row: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: spacing.lg,
    paddingBottom: spacing.md,
  },
  backBtn: {
    width: 40,
    height: 40,
    borderRadius: 20,
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: colors.surfaceContainerLow,
  },
  sideSlot: {
    width: 40,
    minHeight: 40,
    alignItems: 'center',
    justifyContent: 'center',
  },
  title: {
    ...typography.h2,
    flex: 1,
    textAlign: 'center',
  },
  titleLight: {
    color: colors.white,
  },
});
