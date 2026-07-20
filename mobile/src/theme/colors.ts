export const colors = {
  primary: '#00668a',
  primaryContainer: '#6cb8e2',
  background: '#f7f9fb',
  surface: '#f7f9fb',
  surfaceContainerLowest: '#ffffff',
  surfaceContainerLow: '#f2f4f6',
  onSurface: '#191c1e',
  onSurfaceVariant: '#3f484e',
  tertiary: '#875205',
  error: '#ba1a1a',
  outlineVariant: '#71787e',
  white: '#ffffff',
  black: '#000000',
} as const;

export const ghostBorder = (opacity = 0.15) =>
  `rgba(0, 102, 138, ${opacity})`;

export const ambientShadow = {
  shadowColor: '#000000',
  shadowOffset: { width: 0, height: 8 },
  shadowOpacity: 0.04,
  shadowRadius: 24,
  elevation: 4,
} as const;
