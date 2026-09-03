import fs from 'fs';
import path from 'path';
import type { ConfigContext, ExpoConfig } from 'expo/config';

const APP_ENV = process.env.APP_ENV ?? 'development';
const IS_PRODUCTION = APP_ENV === 'production';

const PROD_API_BASE_URL = 'https://kirklareli.bel.tr/api';
const DEV_API_BASE_URLS = [PROD_API_BASE_URL, 'http://kirklareli.test/api'];

const EAS_PROJECT_ID =
  process.env.EAS_PROJECT_ID ?? 'a74e138c-3264-48d0-b522-1f8fb402843a';

export default ({ config }: ConfigContext): ExpoConfig => {
  const googleServicesPath = path.join(__dirname, 'google-services.json');
  const hasGoogleServices = fs.existsSync(googleServicesPath);

  const android: NonNullable<ExpoConfig['android']> = {
    package: 'com.kirklarelibelediyesi',
    versionCode: 7,
    adaptiveIcon: {
      backgroundColor: '#0B6E99',
      foregroundImage: './assets/android-icon-foreground.png',
      backgroundImage: './assets/android-icon-background.png',
    },
    permissions: ['CAMERA', 'READ_MEDIA_IMAGES'],
    ...(hasGoogleServices ? { googleServicesFile: './google-services.json' } : {}),
  };

  if (!IS_PRODUCTION) {
    Object.assign(android, { usesCleartextTraffic: true });
  }

  return {
    ...config,
    name: 'Kırklareli Belediyesi',
    slug: 'kirklareli-belediyesi',
    version: '1.0.5',
    orientation: 'portrait',
    icon: './assets/icon.png',
    scheme: 'kirklareli',
    userInterfaceStyle: 'light',
    splash: {
      image: './assets/splash-icon.png',
      resizeMode: 'contain',
      backgroundColor: '#0B6E99',
    },
    newArchEnabled: true,
    ios: {
      supportsTablet: false,
      bundleIdentifier: 'com.kirklarelibelediyesi',
      buildNumber: '6',
      infoPlist: {
        NSAllowsArbitraryLoads: !IS_PRODUCTION,
        UIBackgroundModes: ['remote-notification'],
        NSCameraUsageDescription:
          'Talep formuna fotoğraf eklemek için kamera erişimi gerekir.',
        NSPhotoLibraryUsageDescription:
          'Talep formuna fotoğraf eklemek için galeri erişimi gerekir.',
      },
    },
    android,
    web: {
      favicon: './assets/belediye-logo.png',
      bundler: 'metro',
    },
    plugins: [
      'expo-router',
      'expo-font',
      [
        'expo-image-picker',
        {
          photosPermission: 'Talep formuna fotoğraf eklemek için galeri erişimi gerekir.',
          cameraPermission: 'Talep formuna fotoğraf eklemek için kamera erişimi gerekir.',
        },
      ],
      [
        'expo-notifications',
        {
          icon: './assets/belediye-logo.png',
          color: '#0B6E99',
        },
      ],
      [
        'expo-build-properties',
        {
          ios: {
            buildReactNativeFromSource: false,
          },
          android: {
            // Play Store "kod karartma" eşiği + boyut optimizasyonu
            enableMinifyInReleaseBuilds: true,
            enableShrinkResourcesInReleaseBuilds: true,
            enableBundleCompression: true,
            // Emülatör + eski 32-bit cihazları çıkar (Play Store arm64 split)
            buildArchs: ['arm64-v8a'],
            networkInspector: false,
            extraProguardRules: `
-keep class expo.modules.** { *; }
-keep @expo.modules.core.interfaces.DoNotStrip class *
-keepclassmembers class * {
  @expo.modules.core.interfaces.DoNotStrip *;
}
-keep class com.swmansion.rnscreens.** { *; }
-keep class expo.modules.notifications.** { *; }
-keep class com.swmansion.reanimated.** { *; }
-keep class com.swmansion.worklets.** { *; }
            `.trim(),
          },
        },
      ],
      './plugins/withAndroidReleaseSigning.js',
      './plugins/withAndroidSizeOptimizations.js',
    ],
    experiments: {
      typedRoutes: true,
    },
    extra: {
      appEnv: APP_ENV,
      apiBaseUrls: IS_PRODUCTION ? [PROD_API_BASE_URL] : DEV_API_BASE_URLS,
      siteBaseUrl: 'https://kirklareli.bel.tr',
      eas: {
        projectId: EAS_PROJECT_ID,
      },
    },
  } as ExpoConfig;
};
