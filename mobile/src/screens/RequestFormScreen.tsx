import { useState } from 'react';
import {
  Alert,
  KeyboardAvoidingView,
  Platform,
  Pressable,
  ScrollView,
  StyleSheet,
  Text,
  View,
} from 'react-native';
import { useRouter } from 'expo-router';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import { FormField } from '../components/FormField';
import { PhotoPicker } from '../components/PhotoPicker';
import { ScreenHeader } from '../components/ScreenHeader';
import { submitContactForm } from '../services/contactService';
import { colors, radius, spacing, typography } from '../theme';

type FormErrors = {
  name?: string;
  phone?: string;
  message?: string;
};

export function RequestFormScreen() {
  const router = useRouter();
  const insets = useSafeAreaInsets();
  const [name, setName] = useState('');
  const [phone, setPhone] = useState('');
  const [message, setMessage] = useState('');
  const [photoUri, setPhotoUri] = useState<string | null>(null);
  const [errors, setErrors] = useState<FormErrors>({});
  const [submitting, setSubmitting] = useState(false);

  const validate = (): boolean => {
    const next: FormErrors = {};
    if (name.trim().length < 3) next.name = 'Ad soyad en az 3 karakter olmalı.';
    if (!/^[0-9+\s()-]{7,}$/.test(phone.trim())) next.phone = 'Geçerli bir telefon numarası girin.';
    if (message.trim().length < 5) next.message = 'Açıklama en az 5 karakter olmalı.';
    setErrors(next);
    return Object.keys(next).length === 0;
  };

  const handleSubmit = async () => {
    if (!validate()) return;

    setSubmitting(true);
    try {
      await submitContactForm({
        name: name.trim(),
        phone: phone.trim(),
        message: message.trim(),
        photoUri,
      });
      Alert.alert('Başarılı', 'Talebiniz alındı. En kısa sürede size dönüş yapılacaktır.', [
        { text: 'Tamam', onPress: () => router.back() },
      ]);
    } catch (error) {
      const detail =
        error instanceof Error && error.message
          ? error.message
          : 'Talebiniz şu an iletilemedi. Lütfen internet bağlantınızı kontrol edip tekrar deneyin.';
      Alert.alert('Gönderilemedi', detail);
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <View style={styles.container}>
      <ScreenHeader title="Talep Formu" onBack={() => router.back()} />
      <KeyboardAvoidingView
        style={styles.flex}
        behavior={Platform.OS === 'ios' ? 'padding' : undefined}
      >
        <ScrollView
          contentContainerStyle={{
            padding: spacing.xl,
            paddingBottom: insets.bottom + spacing['4xl'],
          }}
          keyboardShouldPersistTaps="handled"
        >
          <Text style={styles.hint}>
            Talep ve şikayetlerinizi bu form aracılığıyla iletebilirsiniz.
          </Text>

          <FormField
            label="Ad Soyad *"
            value={name}
            onChangeText={setName}
            placeholder="Adınız Soyadınız"
            error={errors.name}
            autoComplete="name"
          />
          <FormField
            label="Telefon *"
            value={phone}
            onChangeText={setPhone}
            placeholder="05XX XXX XX XX"
            keyboardType="phone-pad"
            error={errors.phone}
            autoComplete="tel"
          />
          <FormField
            label="Açıklama *"
            value={message}
            onChangeText={setMessage}
            placeholder="Talebinizi kısaca açıklayın..."
            multiline
            numberOfLines={5}
            style={styles.textarea}
            error={errors.message}
            textAlignVertical="top"
          />

          <PhotoPicker uri={photoUri} onChange={setPhotoUri} />

          <Pressable
            style={[styles.submitBtn, submitting && styles.submitBtnDisabled]}
            onPress={handleSubmit}
            disabled={submitting}
          >
            <Text style={styles.submitText}>{submitting ? 'Gönderiliyor...' : 'Gönder'}</Text>
          </Pressable>
        </ScrollView>
      </KeyboardAvoidingView>
    </View>
  );
}

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: colors.background,
  },
  flex: {
    flex: 1,
  },
  hint: {
    ...typography.bodySmall,
    marginBottom: spacing['2xl'],
  },
  textarea: {
    minHeight: 120,
    paddingTop: spacing.md,
  },
  submitBtn: {
    backgroundColor: colors.primary,
    borderRadius: radius.md,
    minHeight: 52,
    alignItems: 'center',
    justifyContent: 'center',
    width: '90%',
    alignSelf: 'center',
    marginTop: spacing.md,
  },
  submitBtnDisabled: {
    opacity: 0.6,
  },
  submitText: {
    ...typography.bodyMedium,
    color: colors.white,
    fontSize: 16,
  },
});
