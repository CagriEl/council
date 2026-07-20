import Constants from 'expo-constants';

type AppExtra = {
  appEnv?: string;
  apiBaseUrls?: string[];
  siteBaseUrl?: string;
};

const extra = (Constants.expoConfig?.extra ?? {}) as AppExtra;

const PROD_API_BASE_URLS = ['https://yeni.kirklarelidev.com.tr/public/api'] as const;
const DEV_API_BASE_URLS = [
  'http://kirklareli.test/api',
  'https://yeni.kirklarelidev.com.tr/public/api',
] as const;

/** Native build sırasında app.config.ts extra alanından gelir; geliştirmede __DEV__ kullanılır. */
export const APP_ENV =
  extra.appEnv ?? (__DEV__ ? 'development' : 'production');

const isProduction = APP_ENV === 'production';

export const APP_CONFIG = {
  apiBaseUrls: extra.apiBaseUrls ?? (isProduction ? PROD_API_BASE_URLS : DEV_API_BASE_URLS),
  siteBaseUrl:
    extra.siteBaseUrl ??
    (isProduction ? 'https://yeni.kirklarelidev.com.tr/public' : 'http://kirklareli.test'),
  eBelediyeUrl: 'https://e-belediye.kirklareli.bel.tr',
  baylanCardTopUpUrl: 'https://e-belediye.kirklareli.bel.tr',
  aloBelediyePhone: '153',
  callCenterPhone: '4440139',
  instagramUrl: 'https://www.instagram.com/kirklarelibelediyesi',
  apiTimeoutMs: 8000,
  newsTimeoutMs: 12000,
} as const;
