import Constants from 'expo-constants';

type AppExtra = {
  appEnv?: string;
  apiBaseUrls?: string[];
  siteBaseUrl?: string;
};

const extra = (Constants.expoConfig?.extra ?? {}) as AppExtra;

const PROD_API_BASE_URLS = ['https://kirklareli.bel.tr/api'] as const;
const DEV_API_BASE_URLS = [
  'https://kirklareli.bel.tr/api',
  'http://kirklareli.test/api',
] as const;

/** Native build sırasında app.config.ts extra alanından gelir; geliştirmede __DEV__ kullanılır. */
export const APP_ENV =
  extra.appEnv ?? (__DEV__ ? 'development' : 'production');

const isProduction = APP_ENV === 'production';

export const APP_CONFIG = {
  apiBaseUrls: extra.apiBaseUrls ?? (isProduction ? PROD_API_BASE_URLS : DEV_API_BASE_URLS),
  siteBaseUrl:
    extra.siteBaseUrl ??
    (isProduction ? 'https://kirklareli.bel.tr' : 'https://kirklareli.bel.tr'),
  eBelediyeUrl: 'https://e-belediye.kirklareli.bel.tr',
  /** Kartlı su / Baylan yükleme — e-belediye portalı */
  baylanCardTopUpUrl: 'https://e-belediye.kirklareli.bel.tr',
  aloBelediyePhone: '153',
  callCenterPhone: '4440139',
  instagramUrl: 'https://www.instagram.com/kirklarelibelediyesi',
  apiTimeoutMs: 10000,
  newsTimeoutMs: 15000,
} as const;
