import type { ConfigContext, ExpoConfig } from 'expo/config';

const APP_ENV = process.env.APP_ENV ?? 'development';
const IS_PRODUCTION = APP_ENV === 'production';

const PROD_API_BASE_URL = 'https://yeni.kirklarelidev.com.tr/public/api';
const DEV_API_BASE_URLS = ['http://kirklareli.test/api', PROD_API_BASE_URL];

/**
 * Expo yapılandırması. Production native build için: APP_ENV=production
 */
export default ({ config }: ConfigContext): ExpoConfig => {
  const android: NonNullable<ExpoConfig['android']> = {
    package: 'com.corporateportal.app',
    versionCode: 1,
    adaptiveIcon: {
      backgroundColor: '#ffffff',
      foregroundImage: './assets/belediye-logo.png',
    },
  };

  if (!IS_PRODUCTION) {
    Object.assign(android, { usesCleartextTraffic: true });
  }

  return {
    ...config,
    name: 'Kırklareli Belediyesi',
    slug: 'kirklareli-belediyesi',
    version: '1.0.0',
    orientation: 'portrait',
    icon: './assets/belediye-logo.png',
    scheme: 'kirklareli',
    userInterfaceStyle: 'light',
    splash: {
      image: './assets/belediye-logo.png',
      resizeMode: 'contain',
      backgroundColor: '#f7f9fb',
    },
    newArchEnabled: true,
    ios: {
      supportsTablet: false,
      bundleIdentifier: 'com.corporateportal.app',
      buildNumber: '1',
      infoPlist: {
        NSAllowsArbitraryLoads: !IS_PRODUCTION,
        UIBackgroundModes: ['remote-notification'],
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
        'expo-notifications',
        {
          icon: './assets/belediye-logo.png',
          color: '#00668a',
        },
      ],
      './plugins/withAndroidReleaseSigning.js',
    ],
    experiments: {
      typedRoutes: true,
    },
    extra: {
      appEnv: APP_ENV,
      apiBaseUrls: IS_PRODUCTION ? [PROD_API_BASE_URL] : DEV_API_BASE_URLS,
      siteBaseUrl: IS_PRODUCTION
        ? 'https://yeni.kirklarelidev.com.tr/public'
        : 'http://kirklareli.test',
    },
  } as ExpoConfig;
};
