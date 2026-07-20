import { MaterialIcons } from '@expo/vector-icons';
import { BlurView } from 'expo-blur';
import { Image, Platform, Pressable, StyleSheet, Text, View } from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { ambientShadow, colors, radius, spacing, typography } from '../theme';

const belediyeLogo = require('../../assets/belediye-logo.png');

type Props = {
  notificationCount?: number;
  onNotificationPress?: () => void;
};

export function TopAppBar({ notificationCount = 0, onNotificationPress }: Props) {
  const insets = useSafeAreaInsets();

  const content = (
    <View style={[styles.inner, { paddingTop: insets.top + spacing.sm }]}>
      <View style={styles.brand}>
        <View style={styles.logo}>
          <Image source={belediyeLogo} style={styles.logoImage} resizeMode="contain" />
        </View>
        <View>
          <Text style={styles.title}>Kırklareli Belediyesi</Text>
          <Text style={styles.subtitle}>Resmi Mobil Uygulama</Text>
        </View>
      </View>
      <Pressable style={styles.notifBtn} onPress={onNotificationPress}>
        <MaterialIcons name="notifications-none" size={24} color={colors.onSurface} />
        {notificationCount > 0 ? (
          <View style={styles.badge}>
            <Text style={styles.badgeText}>{notificationCount > 9 ? '9+' : notificationCount}</Text>
          </View>
        ) : null}
      </Pressable>
    </View>
  );

  if (Platform.OS === 'ios') {
    return (
      <BlurView intensity={60} tint="light" style={styles.wrapper}>
        {content}
      </BlurView>
    );
  }

  return <View style={[styles.wrapper, styles.androidGlass]}>{content}</View>;
}

const styles = StyleSheet.create({
  wrapper: {
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
    zIndex: 10,
  },
  androidGlass: {
    backgroundColor: 'rgba(247, 249, 251, 0.92)',
  },
  inner: {
    paddingHorizontal: spacing.xl,
    paddingBottom: spacing.md,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
  },
  brand: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: spacing.md,
    flex: 1,
  },
  logo: {
    width: 44,
    height: 44,
    borderRadius: radius.lg,
    backgroundColor: colors.white,
    alignItems: 'center',
    justifyContent: 'center',
    overflow: 'hidden',
    ...ambientShadow,
  },
  logoImage: {
    width: 40,
    height: 40,
  },
  title: {
    ...typography.h2,
    fontSize: 17,
  },
  subtitle: {
    ...typography.caption,
    marginTop: 2,
  },
  notifBtn: {
    width: 44,
    height: 44,
    borderRadius: radius.full,
    backgroundColor: colors.surfaceContainerLow,
    alignItems: 'center',
    justifyContent: 'center',
  },
  badge: {
    position: 'absolute',
    top: 6,
    right: 6,
    minWidth: 18,
    height: 18,
    borderRadius: radius.full,
    backgroundColor: colors.tertiary,
    alignItems: 'center',
    justifyContent: 'center',
    paddingHorizontal: 4,
  },
  badgeText: {
    color: colors.white,
    fontSize: 10,
    fontWeight: '700',
  },
});
