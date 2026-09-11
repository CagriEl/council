import { useEffect, useRef, useState } from 'react';
import {
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
import { buildNewsFallbackUrl } from '../utils/format';
import type { NewsItem } from '../services/newsService';
import { ambientShadow, colors, radius, spacing, typography } from '../theme';

const { width: SW } = Dimensions.get('window');
const BANNER_W = SW - spacing.xl * 2;
const BANNER_H = 210;

type Props = {
  items: NewsItem[];
  loading?: boolean;
  onPressItem?: (item: NewsItem) => void;
};

function BannerImage({ uri }: { uri: string | null }) {
  const [src, setSrc] = useState(uri);
  const [failed, setFailed] = useState(false);

  useEffect(() => {
    setSrc(uri);
    setFailed(false);
  }, [uri]);

  if (!src || failed) {
    return <View style={styles.placeholder} />;
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
        {items.map((item) => {
          const title = (item.title || item.excerpt || 'Duyuru').trim();
          return (
            <Pressable
              key={item.id}
              style={[styles.card, { width: BANNER_W }]}
              onPress={() => onPressItem?.(item)}
            >
              <View style={styles.media}>
                <BannerImage uri={item.imageUrl} />
              </View>
              <View style={styles.body}>
                <Text style={styles.badge}>{item.categoryLabel || 'Duyuru'}</Text>
                <Text style={styles.title} numberOfLines={3}>
                  {title}
                </Text>
                {item.formattedDate ? (
                  <Text style={styles.date}>{item.formattedDate}</Text>
                ) : null}
              </View>
            </Pressable>
          );
        })}
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
    backgroundColor: colors.surfaceContainerLowest,
    ...ambientShadow,
  },
  media: {
    height: 120,
    backgroundColor: colors.primarySoft,
  },
  image: {
    width: '100%',
    height: '100%',
  },
  placeholder: {
    flex: 1,
    backgroundColor: colors.primary,
  },
  body: {
    paddingHorizontal: spacing.lg,
    paddingVertical: spacing.md,
    gap: 6,
    backgroundColor: colors.surfaceContainerLowest,
  },
  badge: {
    ...typography.caption,
    color: colors.secondary,
    fontWeight: '700',
    alignSelf: 'flex-start',
  },
  title: {
    fontFamily: typography.h2.fontFamily,
    fontSize: 16,
    lineHeight: 22,
    color: colors.onSurface,
    fontWeight: '700',
  },
  date: {
    ...typography.caption,
    color: colors.onSurfaceVariant,
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
    backgroundColor: colors.primarySoft,
  },
});
