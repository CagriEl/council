import { useEffect, useRef, useState } from 'react';
import {
  ActivityIndicator,
  Dimensions,
  Image,
  NativeScrollEvent,
  NativeSyntheticEvent,
  Pressable,
  ScrollView,
  StyleSheet,
  Text,
  View,
} from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { buildNewsFallbackUrl } from '../utils/format';
import type { NewsItem } from '../services/newsService';
import { ambientShadow, colors, radius, spacing, typography } from '../theme';

const { width: SW } = Dimensions.get('window');
const BANNER_W = SW - spacing.xl * 2;
const BANNER_H = 200;

type Props = {
  items: NewsItem[];
  loading?: boolean;
  onPressItem?: (item: NewsItem) => void;
};

function BannerImage({ uri, title }: { uri: string | null; title: string }) {
  const [src, setSrc] = useState(uri);
  const [failed, setFailed] = useState(false);

  if (!src || failed) {
    return (
      <View style={styles.placeholder}>
        <Text style={styles.placeholderEmoji}>📢</Text>
        <Text style={styles.placeholderText} numberOfLines={2}>{title}</Text>
      </View>
    );
  }

  return (
    <Image
      source={{ uri: src }}
      style={styles.image}
      resizeMode="cover"
      onError={() => {
        const fallback = buildNewsFallbackUrl(src);
        if (fallback !== src) {
          setSrc(fallback);
        } else {
          setFailed(true);
        }
      }}
    />
  );
}

export function HeroBanner({ items, loading, onPressItem }: Props) {
  const scrollRef = useRef<ScrollView>(null);
  const [activeIndex, setActiveIndex] = useState(0);

  useEffect(() => {
    if (items.length <= 1) return undefined;
    const timer = setInterval(() => {
      setActiveIndex((prev) => {
        const next = (prev + 1) % items.length;
        scrollRef.current?.scrollTo({ x: next * (BANNER_W + spacing.md), animated: true });
        return next;
      });
    }, 5000);
    return () => clearInterval(timer);
  }, [items.length]);

  const onScroll = (e: NativeSyntheticEvent<NativeScrollEvent>) => {
    const index = Math.round(e.nativeEvent.contentOffset.x / (BANNER_W + spacing.md));
    setActiveIndex(index);
  };

  if (loading) {
    return <View style={[styles.skeleton, { width: BANNER_W, height: BANNER_H }]} />;
  }

  if (!items.length) return null;

  return (
    <View>
      <ScrollView
        ref={scrollRef}
        horizontal
        pagingEnabled={false}
        snapToInterval={BANNER_W + spacing.md}
        decelerationRate="fast"
        showsHorizontalScrollIndicator={false}
        onScroll={onScroll}
        scrollEventThrottle={16}
        contentContainerStyle={styles.scrollContent}
      >
        {items.map((item) => (
          <Pressable
            key={item.id}
            style={[styles.card, { width: BANNER_W, height: BANNER_H }]}
            onPress={() => onPressItem?.(item)}
          >
            <BannerImage uri={item.imageUrl} title={item.title} />
            <LinearGradient
              colors={['transparent', 'rgba(0,0,0,0.75)']}
              style={styles.overlay}
            >
              <Text style={styles.badge}>{item.categoryLabel}</Text>
              <Text style={styles.title} numberOfLines={2}>{item.title}</Text>
              <Text style={styles.date}>{item.formattedDate}</Text>
            </LinearGradient>
          </Pressable>
        ))}
      </ScrollView>
      <View style={styles.dots}>
        {items.map((item, i) => (
          <View key={item.id} style={[styles.dot, i === activeIndex && styles.dotActive]} />
        ))}
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  scrollContent: {
    paddingHorizontal: spacing.xl,
    gap: spacing.md,
  },
  card: {
    borderRadius: radius.xl,
    overflow: 'hidden',
    backgroundColor: colors.surfaceContainerLow,
    ...ambientShadow,
  },
  image: {
    width: '100%',
    height: '100%',
  },
  placeholder: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: colors.primaryContainer,
    padding: spacing.xl,
  },
  placeholderEmoji: {
    fontSize: 40,
    marginBottom: spacing.sm,
  },
  placeholderText: {
    ...typography.bodySmall,
    textAlign: 'center',
    color: colors.onSurface,
  },
  overlay: {
    ...StyleSheet.absoluteFill,
    justifyContent: 'flex-end',
    padding: spacing.lg,
  },
  badge: {
    ...typography.caption,
    color: colors.white,
    backgroundColor: 'rgba(0,102,138,0.8)',
    alignSelf: 'flex-start',
    paddingHorizontal: spacing.sm,
    paddingVertical: 4,
    borderRadius: radius.sm,
    marginBottom: spacing.sm,
    overflow: 'hidden',
  },
  title: {
    ...typography.h2,
    color: colors.white,
    fontSize: 18,
  },
  date: {
    ...typography.caption,
    color: 'rgba(255,255,255,0.85)',
    marginTop: 4,
  },
  dots: {
    flexDirection: 'row',
    justifyContent: 'center',
    gap: 6,
    marginTop: spacing.md,
  },
  dot: {
    width: 6,
    height: 6,
    borderRadius: 3,
    backgroundColor: colors.surfaceContainerLow,
  },
  dotActive: {
    width: 18,
    backgroundColor: colors.primary,
  },
  skeleton: {
    marginHorizontal: spacing.xl,
    borderRadius: radius.xl,
    backgroundColor: colors.surfaceContainerLow,
    alignItems: 'center',
    justifyContent: 'center',
  },
});
