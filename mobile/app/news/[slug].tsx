import { useEffect, useState } from 'react';
import {
  ActivityIndicator,
  Dimensions,
  Image,
  Pressable,
  ScrollView,
  StyleSheet,
  Text,
  View,
} from 'react-native';
import { LinearGradient } from 'expo-linear-gradient';
import { MaterialIcons } from '@expo/vector-icons';
import { useLocalSearchParams, useRouter } from 'expo-router';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { fetchNewsDetail, newsContent } from '../../src/services/newsArticleService';
import { buildNewsFallbackUrl } from '../../src/utils/format';
import { colors, radius, spacing, typography } from '../../src/theme';

const { width: SW } = Dimensions.get('window');
const HERO_H = 260;

export default function NewsDetailScreen() {
  const router = useRouter();
  const insets = useSafeAreaInsets();
  const { slug } = useLocalSearchParams<{ slug: string }>();
  const [loading, setLoading] = useState(true);
  const [title, setTitle] = useState('Haber');
  const [content, setContent] = useState('');
  const [categoryLabel, setCategoryLabel] = useState('');
  const [formattedDate, setFormattedDate] = useState('');
  const [imageSrc, setImageSrc] = useState<string | null>(null);

  useEffect(() => {
    if (!slug) return undefined;

    fetchNewsDetail(String(slug)).then((article) => {
      if (!article) {
        setLoading(false);
        return;
      }
      setTitle(article.title);
      setContent(newsContent(article));
      setCategoryLabel(article.categoryLabel);
      setFormattedDate(article.formattedDate);
      setImageSrc(article.imageUrl);
      setLoading(false);
    });

    return undefined;
  }, [slug]);

  return (
    <View style={styles.container}>
      <ScrollView contentContainerStyle={{ paddingBottom: insets.bottom + spacing['4xl'] }}>
        <View style={[styles.hero, { height: HERO_H }]}>
          {imageSrc ? (
            <Image
              source={{ uri: imageSrc }}
              style={styles.heroImage}
              resizeMode="cover"
              onError={() => imageSrc && setImageSrc(buildNewsFallbackUrl(imageSrc))}
            />
          ) : (
            <View style={styles.heroPlaceholder}>
              <Text style={styles.heroEmoji}>📰</Text>
            </View>
          )}
          <LinearGradient colors={['transparent', 'rgba(0,0,0,0.6)']} style={styles.heroOverlay} />
          <Pressable
            style={[styles.backBtn, { top: insets.top + spacing.sm }]}
            onPress={() => router.back()}
          >
            <MaterialIcons name="arrow-back" size={22} color={colors.white} />
          </Pressable>
        </View>

        <View style={styles.body}>
          {categoryLabel ? <Text style={styles.badge}>{categoryLabel}</Text> : null}
          {formattedDate ? <Text style={styles.date}>{formattedDate}</Text> : null}
          <Text style={styles.title}>{title}</Text>
          {loading ? (
            <ActivityIndicator color={colors.primary} style={{ marginTop: spacing.lg }} />
          ) : (
            <Text style={styles.content}>{content || 'İçerik bulunamadı.'}</Text>
          )}
        </View>
      </ScrollView>
    </View>
  );
}

const styles = StyleSheet.create({
  container: { flex: 1, backgroundColor: colors.background },
  hero: { width: SW, backgroundColor: colors.surfaceContainerLow },
  heroImage: { width: SW, height: HERO_H },
  heroPlaceholder: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: colors.primaryContainer,
  },
  heroEmoji: { fontSize: 56 },
  heroOverlay: { ...StyleSheet.absoluteFill },
  backBtn: {
    position: 'absolute',
    left: spacing.lg,
    width: 40,
    height: 40,
    borderRadius: radius.full,
    backgroundColor: 'rgba(0,0,0,0.35)',
    alignItems: 'center',
    justifyContent: 'center',
  },
  body: { padding: spacing.xl, gap: spacing.sm },
  badge: {
    ...typography.caption,
    color: colors.primary,
    alignSelf: 'flex-start',
    backgroundColor: 'rgba(0,102,138,0.1)',
    paddingHorizontal: spacing.sm,
    paddingVertical: 4,
    borderRadius: radius.sm,
    overflow: 'hidden',
  },
  date: { ...typography.bodySmall },
  title: { ...typography.h1, marginTop: spacing.sm },
  content: { ...typography.body, marginTop: spacing.lg, color: colors.onSurfaceVariant },
});
