import { TextStyle } from 'react-native';

export const fonts = {
  heading: 'Manrope_700Bold',
  headingSemi: 'Manrope_600SemiBold',
  headingMedium: 'Manrope_500Medium',
  body: 'Inter_400Regular',
  bodyMedium: 'Inter_500Medium',
  bodySemi: 'Inter_600SemiBold',
} as const;

export const typography = {
  h1: {
    fontFamily: fonts.heading,
    fontSize: 24,
    lineHeight: 30,
    letterSpacing: -0.48,
    color: '#191c1e',
  } satisfies TextStyle,
  h2: {
    fontFamily: fonts.headingSemi,
    fontSize: 20,
    lineHeight: 26,
    letterSpacing: -0.4,
    color: '#191c1e',
  } satisfies TextStyle,
  body: {
    fontFamily: fonts.body,
    fontSize: 16,
    lineHeight: 24,
    color: '#191c1e',
  } satisfies TextStyle,
  bodyMedium: {
    fontFamily: fonts.bodyMedium,
    fontSize: 16,
    lineHeight: 24,
    color: '#191c1e',
  } satisfies TextStyle,
  bodySmall: {
    fontFamily: fonts.body,
    fontSize: 14,
    lineHeight: 20,
    color: '#3f484e',
  } satisfies TextStyle,
  label: {
    fontFamily: fonts.bodyMedium,
    fontSize: 13,
    lineHeight: 18,
    color: '#3f484e',
  } satisfies TextStyle,
  caption: {
    fontFamily: fonts.body,
    fontSize: 12,
    lineHeight: 16,
    color: '#3f484e',
  } satisfies TextStyle,
} as const;
