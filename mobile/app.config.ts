import type { ConfigContext, ExpoConfig } from 'expo/config';

const APP_ENV = process.env.APP_ENV ?? 'development';
const IS_PRODUCTION = APP_ENV === 'production';

const PROD_API_BASE_URL = 'https://kirklareli.bel.tr/api';
const DEV_API_BASE_URLS = [PROD_API_BASE_URL, 'http://kirklareli.test/api'];

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
      siteBaseUrl: 'https://kirklareli.bel.tr',
    },
  } as ExpoConfig;
};
