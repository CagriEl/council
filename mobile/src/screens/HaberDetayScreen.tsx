import { useEffect, useState } from 'react';
import {
  ActivityIndicator,
  Dimensions,
  Image,
  Linking,
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
import {
  fetchAnnouncementById,
  fetchAnnouncementDetail,
} from '../services/newsService';
import { buildNewsFallbackUrl, stripHtml } from '../utils/format';
import { colors, radius, spacing, typography } from '../theme';

const { width: SW } = Dimensions.get('window');
const HERO_H = 260;

export function HaberDetayScreen() {
  const router = useRouter();
  const insets = useSafeAreaInsets();
  const params = useLocalSearchParams<{
    id: string;
    slug?: string;
    title?: string;
    imageUrl?: string;
    excerpt?: string;
    formattedDate?: string;
    categoryLabel?: string;
    contentHtml?: string;
    fileUrl?: string;
  }>();

  const [imageSrc, setImageSrc] = useState(params.imageUrl || null);
  const [title, setTitle] = useState(params.title ?? 'Duyuru');
  const [content, setContent] = useState(
    stripHtml(params.contentHtml) || params.excerpt || '',
  );
  const [fileUrl, setFileUrl] = useState(params.fileUrl || null);
  const [categoryLabel, setCategoryLabel] = useState(params.categoryLabel ?? '');
  const [formattedDate, setFormattedDate] = useState(params.formattedDate ?? '');
  const [loadingDetail, setLoadingDetail] = useState(!params.contentHtml);

  useEffect(() => {
    let cancelled = false;

    const loadDetail = async () => {
      setLoadingDetail(true);

      let item = null;
      if (params.slug) {
        item = await fetchAnnouncementDetail(String(params.slug));
      }
      if (!item && params.id) {
        item = await fetchAnnouncementById(Number(params.id));
      }

      if (cancelled || !item) {
        setLoadingDetail(false);
        return;
      }

      setTitle(item.title);
      setContent(stripHtml(item.contentHtml) || item.excerpt);
      if (item.imageUrl) setImageSrc(item.imageUrl);
      if (item.fileUrl) setFileUrl(item.fileUrl);
      if (item.categoryLabel) setCategoryLabel(item.categoryLabel);
      if (item.formattedDate) setFormattedDate(item.formattedDate);
      setLoadingDetail(false);
    };

    loadDetail();
    return () => {
      cancelled = true;
    };
  }, [params.slug, params.id]);

  return (
    <View style={styles.container}>
      <ScrollView contentContainerStyle={{ paddingBottom: insets.bottom + spacing['4xl'] }}>
        <View style={[styles.hero, { height: HERO_H }]}>
          {imageSrc ? (
            <Image
              source={{ uri: imageSrc }}
              style={styles.heroImage}
              resizeMode="cover"
              onError={() => {
                if (imageSrc) setImageSrc(buildNewsFallbackUrl(imageSrc));
              }}
            />
          ) : (
            <View style={styles.heroPlaceholder}>
              <Text style={styles.heroEmoji}>📢</Text>
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
          {categoryLabel ? (
            <Text style={styles.badge}>{categoryLabel}</Text>
          ) : null}
          {formattedDate ? (
            <Text style={styles.date}>{formattedDate}</Text>
          ) : null}
          <Text style={styles.title}>{title}</Text>
          {loadingDetail ? (
            <ActivityIndicator color={colors.primary} style={{ marginTop: spacing.lg }} />
          ) : (
            <Text style={styles.content}>{content || 'İçerik bulunamadı.'}</Text>
          )}

          {fileUrl ? (
            <Pressable
              style={styles.fileBtn}
              onPress={() => Linking.openURL(fileUrl)}
            >
              <MaterialIcons name="picture-as-pdf" size={22} color={colors.white} />
              <Text style={styles.fileBtnText}>Dosyayı Görüntüle / İndir</Text>
            </Pressable>
          ) : null}
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
  hero: {
    width: SW,
    backgroundColor: colors.surfaceContainerLow,
  },
  heroImage: {
    width: SW,
    height: HERO_H,
  },
  heroPlaceholder: {
    flex: 1,
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: colors.primaryContainer,
  },
  heroEmoji: {
    fontSize: 56,
  },
  heroOverlay: {
    ...StyleSheet.absoluteFill,
  },
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
  body: {
    padding: spacing.xl,
    gap: spacing.sm,
  },
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
  date: {
    ...typography.bodySmall,
  },
  title: {
    ...typography.h1,
    marginTop: spacing.sm,
  },
  content: {
    ...typography.body,
    marginTop: spacing.lg,
    color: colors.onSurfaceVariant,
  },
  fileBtn: {
    marginTop: spacing['2xl'],
    backgroundColor: colors.primary,
    borderRadius: radius.md,
    paddingVertical: spacing.md,
    paddingHorizontal: spacing.lg,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: spacing.sm,
  },
  fileBtnText: {
    ...typography.bodyMedium,
    color: colors.white,
  },
});
