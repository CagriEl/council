import { useEffect, useMemo, useRef, useState } from 'react';
import {
  ActivityIndicator,
  Animated,
  Pressable,
  ScrollView,
  StyleSheet,
  Text,
  View,
} from 'react-native';
import { BlurView } from 'expo-blur';
import { Platform } from 'react-native';
import { useRouter } from 'expo-router';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { EmptyState } from '../components/EmptyState';
import { ScreenHeader } from '../components/ScreenHeader';
import { fetchTransportSchedules } from '../services/scheduleService';
import type { RouteSchedule } from '../types/schedule';
import { ambientShadow, colors, radius, spacing, typography } from '../theme';

function parseTime(value: string): number {
  const [h, m] = value.split(':').map(Number);
  return h * 60 + m;
}

function getNextDeparture(times: string[], nowMinutes: number) {
  const sorted = [...times].sort((a, b) => parseTime(a) - parseTime(b));
  for (const time of sorted) {
    if (parseTime(time) >= nowMinutes) {
      return time;
    }
  }
  return sorted[0] ?? '--:--';
}

function minutesUntil(time: string, nowMinutes: number): number {
  const target = parseTime(time);
  if (target >= nowMinutes) return target - nowMinutes;
  return 24 * 60 - nowMinutes + target;
}

export function SaatlerScreen() {
  const router = useRouter();
  const insets = useSafeAreaInsets();
  const [routes, setRoutes] = useState<RouteSchedule[]>([]);
  const [loading, setLoading] = useState(true);
  const [weekend, setWeekend] = useState(false);
  const [selectedRouteId, setSelectedRouteId] = useState('');
  const [now, setNow] = useState(new Date());
  const pulse = useRef(new Animated.Value(1)).current;

  useEffect(() => {
    fetchTransportSchedules().then((data) => {
      setRoutes(data);
      setSelectedRouteId(data[0]?.id ?? '');
      setLoading(false);
    });
  }, []);

  useEffect(() => {
    const timer = setInterval(() => setNow(new Date()), 60000);
    return () => clearInterval(timer);
  }, []);

  const route = routes.find((r) => r.id === selectedRouteId) ?? routes[0];
  const schedule = route ? (weekend ? route.weekend : route.weekday) : [];
  const nowMinutes = now.getHours() * 60 + now.getMinutes();
  const nextDeparture = getNextDeparture(schedule, nowMinutes);
  const countdown = minutesUntil(nextDeparture, nowMinutes);

  useEffect(() => {
    if (countdown > 15) return undefined;
    const anim = Animated.loop(
      Animated.sequence([
        Animated.timing(pulse, { toValue: 1.06, duration: 600, useNativeDriver: true }),
        Animated.timing(pulse, { toValue: 1, duration: 600, useNativeDriver: true }),
      ]),
    );
    anim.start();
    return () => anim.stop();
  }, [countdown, pulse]);

  const timeline = useMemo(() => {
    return schedule.map((time) => {
      const mins = parseTime(time);
      let status: 'past' | 'active' | 'next' = 'next';
      if (mins < nowMinutes) status = 'past';
      else if (time === nextDeparture) status = 'active';
      return { time, status, note: route?.notes?.[time] };
    });
  }, [schedule, nowMinutes, nextDeparture, route?.notes]);

  if (loading) {
    return (
      <View style={styles.container}>
        <ScreenHeader title="Sefer Saatleri" onBack={() => router.back()} />
        <View style={styles.centered}>
          <ActivityIndicator size="large" color={colors.primary} />
        </View>
      </View>
    );
  }

  if (!route) {
    return (
      <View style={styles.container}>
        <ScreenHeader title="Sefer Saatleri" onBack={() => router.back()} />
        <EmptyState title="Sefer bulunamadı" message="Sefer saatleri henüz yüklenemedi." />
      </View>
    );
  }

  const NextCard = (
    <Animated.View style={[styles.nextCardInner, countdown <= 15 && { transform: [{ scale: pulse }] }]}>
      <Text style={styles.nextLabel}>Sonraki Sefer</Text>
      <Text style={[styles.nextTime, { color: route.color }]}>{nextDeparture}</Text>
      <Text style={styles.countdown}>
        {countdown <= 0 ? 'Kalkıyor' : `${countdown} dakika kaldı`}
      </Text>
    </Animated.View>
  );

  return (
    <View style={styles.container}>
      <ScreenHeader title="Sefer Saatleri" onBack={() => router.back()} />
      <ScrollView contentContainerStyle={{ paddingBottom: insets.bottom + spacing['4xl'] }}>
        {Platform.OS === 'ios' ? (
          <BlurView intensity={35} tint="light" style={styles.nextCard}>
            {NextCard}
          </BlurView>
        ) : (
          <View style={[styles.nextCard, styles.androidGlass]}>{NextCard}</View>
        )}

        <View style={styles.toggleRow}>
          <Pressable
            style={[styles.toggleBtn, !weekend && styles.toggleActive]}
            onPress={() => setWeekend(false)}
          >
            <Text style={[styles.toggleText, !weekend && styles.toggleTextActive]}>Hafta İçi</Text>
          </Pressable>
          <Pressable
            style={[styles.toggleBtn, weekend && styles.toggleActive]}
            onPress={() => setWeekend(true)}
          >
            <Text style={[styles.toggleText, weekend && styles.toggleTextActive]}>Hafta Sonu</Text>
          </Pressable>
        </View>

        <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={styles.chips}>
          {routes.map((r) => (
            <Pressable
              key={r.id}
              style={[
                styles.chip,
                selectedRouteId === r.id && { backgroundColor: r.color },
              ]}
              onPress={() => setSelectedRouteId(r.id)}
            >
              <Text style={[styles.chipText, selectedRouteId === r.id && styles.chipTextActive]}>
                {r.label}
              </Text>
            </Pressable>
          ))}
        </ScrollView>

        <View style={styles.timeline}>
          {timeline.map((row) => (
            <View
              key={row.time}
              style={[
                styles.departureRow,
                row.status === 'past' && styles.departurePast,
                row.status === 'active' && styles.departureActive,
              ]}
            >
              <View style={[styles.hatBadge, { backgroundColor: route.color }]}>
                <Text style={styles.hatText}>{route.id === 'sehir' ? 'A' : 'B'}</Text>
              </View>
              <Text style={styles.departureTime}>{row.time}</Text>
              {row.note ? <Text style={styles.noteTag}>{row.note}</Text> : null}
            </View>
          ))}
        </View>
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: colors.background,
  },
  centered: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
  },
  nextCard: {
    margin: spacing.xl,
    borderRadius: radius.xl,
    overflow: 'hidden',
    ...ambientShadow,
  },
  androidGlass: {
    backgroundColor: 'rgba(255,255,255,0.9)',
  },
  nextCardInner: {
    padding: spacing['2xl'],
    alignItems: 'center',
  },
  nextLabel: {
    ...typography.label,
  },
  nextTime: {
    fontSize: 48,
    fontWeight: '700',
    marginVertical: spacing.sm,
  },
  countdown: {
    ...typography.body,
    color: colors.onSurfaceVariant,
  },
  toggleRow: {
    flexDirection: 'row',
    marginHorizontal: spacing.xl,
    backgroundColor: colors.surfaceContainerLow,
    borderRadius: radius.md,
    padding: 4,
  },
  toggleBtn: {
    flex: 1,
    paddingVertical: spacing.sm,
    alignItems: 'center',
    borderRadius: radius.sm,
  },
  toggleActive: {
    backgroundColor: colors.surfaceContainerLowest,
  },
  toggleText: {
    ...typography.bodySmall,
  },
  toggleTextActive: {
    color: colors.primary,
    fontWeight: '600',
  },
  chips: {
    paddingHorizontal: spacing.xl,
    paddingVertical: spacing.lg,
    gap: spacing.sm,
  },
  chip: {
    paddingHorizontal: spacing.lg,
    paddingVertical: spacing.sm,
    borderRadius: radius.full,
    backgroundColor: colors.surfaceContainerLow,
  },
  chipText: {
    ...typography.bodySmall,
    color: colors.onSurface,
  },
  chipTextActive: {
    color: colors.white,
    fontWeight: '600',
  },
  timeline: {
    paddingHorizontal: spacing.xl,
    gap: spacing.sm,
  },
  departureRow: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: colors.surfaceContainerLowest,
    borderRadius: radius.md,
    padding: spacing.md,
    gap: spacing.md,
    ...ambientShadow,
  },
  departurePast: {
    opacity: 0.45,
  },
  departureActive: {
    backgroundColor: 'rgba(0,102,138,0.08)',
  },
  hatBadge: {
    width: 28,
    height: 28,
    borderRadius: radius.full,
    alignItems: 'center',
    justifyContent: 'center',
  },
  hatText: {
    color: colors.white,
    fontSize: 12,
    fontWeight: '700',
  },
  departureTime: {
    ...typography.bodyMedium,
    fontFamily: typography.h2.fontFamily,
    flex: 1,
  },
  noteTag: {
    ...typography.caption,
    color: colors.tertiary,
    backgroundColor: 'rgba(135,82,5,0.1)',
    paddingHorizontal: spacing.sm,
    paddingVertical: 2,
    borderRadius: radius.sm,
    overflow: 'hidden',
  },
});
