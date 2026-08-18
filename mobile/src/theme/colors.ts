export const colors = {
  primary: '#0B6E99',
  primaryDark: '#064E6E',
  primaryContainer: '#B8E4F7',
  primarySoft: '#E6F5FB',
  secondary: '#1B8F6A',
  secondaryContainer: '#D4F5E8',
  accent: '#E8A317',
  accentSoft: '#FFF4D6',
  background: '#F3F7FA',
  surface: '#F3F7FA',
  surfaceContainerLowest: '#FFFFFF',
  surfaceContainerLow: '#E8EEF3',
  onSurface: '#152028',
  onSurfaceVariant: '#4A5963',
  tertiary: '#C45C12',
  error: '#BA1A1A',
  outlineVariant: '#8A96A0',
  white: '#FFFFFF',
  black: '#000000',
  navActiveBg: '#D9F0FA',
} as const;

export const quickAccessTints: Record<string, { bg: string; icon: string }> = {
  badge: { bg: '#D9F0FA', icon: '#0B6E99' },
  newspaper: { bg: '#FFE8CC', icon: '#C45C12' },
  pending_actions: { bg: '#E0F7EF', icon: '#1B8F6A' },
  schedule: { bg: '#E8E4FF', icon: '#5B4DB8' },
  contact_page: { bg: '#FFE0E6', icon: '#C6284C' },
  gavel: { bg: '#E4EEF7', icon: '#1A5276' },
  construction: { bg: '#FFF0D6', icon: '#B7791F' },
  campaign: { bg: '#E0F2FE', icon: '#0369A1' },
  account_balance: { bg: '#DCFCE7', icon: '#15803D' },
  groups: { bg: '#F3E8FF', icon: '#7E22CE' },
};

export const ghostBorder = (opacity = 0.15) =>
  `rgba(11, 110, 153, ${opacity})`;

export const ambientShadow = {
  shadowColor: '#0B6E99',
  shadowOffset: { width: 0, height: 6 },
  shadowOpacity: 0.08,
  shadowRadius: 16,
  elevation: 3,
} as const;
