import { useEffect, useRef, useState } from 'react';
import {
  ActivityIndicator,
  Animated,
  Dimensions,
  Image,
  ScrollView,
  StyleSheet,
  Text,
  View,
} from 'react-native';
import { BlurView } from 'expo-blur';
import { LinearGradient } from 'expo-linear-gradient';
import { Platform } from 'react-native';
import { useRouter } from 'expo-router';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { ScreenHeader } from '../components/ScreenHeader';
import { fetchMayorProfile, type MayorProfile } from '../services/mayorService';
import { ambientShadow, colors, radius, spacing, typography } from '../theme';

const { width: SW, height: SH } = Dimensions.get('window');
const HERO_H = SH * 0.48;

export function MudurScreen() {
  const router = useRouter();
  const insets = useSafeAreaInsets();
  const [scrollY, setScrollY] = useState(0);
  const [mayor, setMayor] = useState<MayorProfile | null>(null);
  const [imageFailed, setImageFailed] = useState(false);
  const scale = useRef(new Animated.Value(1)).current;

  useEffect(() => {
    fetchMayorProfile().then(setMayor);
  }, []);

  const onScroll = (e: { nativeEvent: { contentOffset: { y: number } } }) => {
    const y = e.nativeEvent.contentOffset.y;
    setScrollY(y);
    scale.setValue(1 + Math.min(y / 1000, 0.08));
  };

  if (!mayor) {
    return (
      <View style={[styles.container, styles.centered]}>
        <ActivityIndicator size="large" color={colors.primary} />
      </View>
    );
  }

  const initials = mayor.name
    .split(' ')
    .map((part) => part[0])
    .join('')
    .slice(0, 2)
    .toUpperCase();

  return (
    <View style={styles.container}>
      <ScreenHeader
        title="Belediye Başkanı"
        scrollY={scrollY}
        transparent={scrollY < 120}
        onBack={() => router.back()}
      />
      <Animated.ScrollView
        onScroll={onScroll}
        scrollEventThrottle={16}
        contentContainerStyle={{ paddingBottom: insets.bottom + spacing['4xl'] }}
      >
        <Animated.View style={[styles.hero, { height: HERO_H, transform: [{ scale }] }]}>
          {mayor.imageUrl && !imageFailed ? (
            <Image
              source={{ uri: mayor.imageUrl }}
              style={styles.heroImage}
              resizeMode="cover"
              onError={() => setImageFailed(true)}
            />
          ) : (
            <View style={styles.heroPlaceholder}>
              <Text style={styles.heroInitials}>{initials}</Text>
            </View>
          )}
          <LinearGradient
            colors={['transparent', 'rgba(0,0,0,0.55)']}
            style={styles.heroGradient}
          />
        </Animated.View>

        <View style={styles.identityCard}>
          <View style={styles.accentBar} />
          <Text style={styles.name}>{mayor.name}</Text>
          <Text style={styles.title}>{mayor.title}</Text>
        </View>

        {Platform.OS === 'ios' ? (
          <BlurView intensity={40} tint="light" style={styles.glassCard}>
            <Text style={styles.sectionTitle}>Biyografi</Text>
            <Text style={styles.bio}>{mayor.biography}</Text>
          </BlurView>
        ) : (
          <View style={[styles.glassCard, styles.androidGlass]}>
            <Text style={styles.sectionTitle}>Biyografi</Text>
            <Text style={styles.bio}>{mayor.biography}</Text>
          </View>
        )}

        {mayor.message ? (
          <LinearGradient
            colors={[colors.primary, '#004d6b']}
            style={styles.messageCard}
          >
            <Text style={styles.quoteMark}>"</Text>
            <Text style={styles.message}>{mayor.message}</Text>
          </LinearGradient>
        ) : null}
      </Animated.ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: colors.background,
  },
  centered: {
    alignItems: 'center',
    justifyContent: 'center',
  },
  hero: {
    width: SW,
    overflow: 'hidden',
    backgroundColor: colors.primaryContainer,
  },
  heroImage: {
    width: SW,
    height: '100%',
  },
  heroPlaceholder: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: colors.primary,
  },
  heroInitials: {
    fontSize: 72,
    fontWeight: '700',
    color: colors.white,
    opacity: 0.4,
  },
  heroGradient: {
    ...StyleSheet.absoluteFill,
  },
  identityCard: {
    marginHorizontal: spacing.xl,
    marginTop: -spacing['4xl'],
    backgroundColor: colors.surfaceContainerLowest,
    borderRadius: radius.xl,
    padding: spacing['2xl'],
    ...ambientShadow,
    overflow: 'hidden',
  },
  accentBar: {
    position: 'absolute',
    left: 0,
    top: 0,
    bottom: 0,
    width: 4,
    backgroundColor: colors.primary,
  },
  name: {
    ...typography.h1,
  },
  title: {
    ...typography.body,
    color: colors.onSurfaceVariant,
    marginTop: spacing.xs,
  },
  glassCard: {
    marginHorizontal: spacing.xl,
    marginTop: spacing['2xl'],
    borderRadius: radius.xl,
    padding: spacing['2xl'],
    overflow: 'hidden',
  },
  androidGlass: {
    backgroundColor: 'rgba(255,255,255,0.85)',
  },
  sectionTitle: {
    ...typography.h2,
    marginBottom: spacing.md,
  },
  bio: {
    ...typography.body,
    color: colors.onSurfaceVariant,
    lineHeight: 26,
  },
  messageCard: {
    marginHorizontal: spacing.xl,
    marginTop: spacing['2xl'],
    borderRadius: radius.xl,
    padding: spacing['2xl'],
  },
  quoteMark: {
    fontSize: 48,
    color: 'rgba(255,255,255,0.35)',
    lineHeight: 48,
    marginBottom: spacing.sm,
  },
  message: {
    ...typography.body,
    color: colors.white,
    lineHeight: 26,
  },
});
