import { useState } from 'react';
import { StyleSheet, Text, TextInput, TextInputProps, View } from 'react-native';
import { colors, ghostBorder, radius, spacing, typography } from '../theme';

type Props = TextInputProps & {
  label: string;
  error?: string;
};

export function FormField({ label, error, style, ...props }: Props) {
  const [focused, setFocused] = useState(false);

  return (
    <View style={styles.wrap}>
      <Text style={styles.label}>{label}</Text>
      <TextInput
        {...props}
        style={[
          styles.input,
          focused && styles.inputFocused,
          error ? styles.inputError : null,
          style,
        ]}
        placeholderTextColor={colors.onSurfaceVariant}
        onFocus={(e) => {
          setFocused(true);
          props.onFocus?.(e);
        }}
        onBlur={(e) => {
          setFocused(false);
          props.onBlur?.(e);
        }}
      />
      {error ? <Text style={styles.error}>{error}</Text> : null}
    </View>
  );
}

const styles = StyleSheet.create({
  wrap: {
    marginBottom: spacing.lg,
  },
  label: {
    ...typography.label,
    marginBottom: spacing.sm,
  },
  input: {
    backgroundColor: colors.surfaceContainerLow,
    borderRadius: radius.md,
    paddingHorizontal: spacing.lg,
    paddingVertical: spacing.md,
    ...typography.body,
    minHeight: 48,
  },
  inputFocused: {
    backgroundColor: colors.surfaceContainerLowest,
    borderWidth: 1,
    borderColor: ghostBorder(0.25),
  },
  inputError: {
    borderWidth: 1,
    borderColor: colors.error,
  },
  error: {
    ...typography.caption,
    color: colors.error,
    marginTop: spacing.xs,
  },
});
