import { MaterialIcons } from '@expo/vector-icons';
import * as ImagePicker from 'expo-image-picker';
import { Alert, Image, Pressable, StyleSheet, Text, View } from 'react-native';
import { colors, radius, spacing, typography } from '../theme';

type Props = {
  uri: string | null;
  onChange: (uri: string | null) => void;
};

export function PhotoPicker({ uri, onChange }: Props) {
  const pickImage = async () => {
    const permission = await ImagePicker.requestMediaLibraryPermissionsAsync();
    if (!permission.granted) {
      Alert.alert('İzin Gerekli', 'Fotoğraf eklemek için galeri izni vermeniz gerekiyor.');
      return;
    }

    const result = await ImagePicker.launchImageLibraryAsync({
      mediaTypes: ['images'],
      quality: 0.8,
    });

    if (!result.canceled && result.assets[0]) {
      onChange(result.assets[0].uri);
    }
  };

  return (
    <View style={styles.wrap}>
      <Text style={styles.label}>Fotoğraf (Opsiyonel)</Text>
      {uri ? (
        <View style={styles.previewWrap}>
          <Image source={{ uri }} style={styles.preview} />
          <Pressable style={styles.removeBtn} onPress={() => onChange(null)}>
            <MaterialIcons name="close" size={18} color={colors.white} />
          </Pressable>
        </View>
      ) : (
        <Pressable style={styles.picker} onPress={pickImage}>
          <MaterialIcons name="add-a-photo" size={28} color={colors.primary} />
          <Text style={styles.pickerText}>Galeriden seç</Text>
        </Pressable>
      )}
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
  picker: {
    backgroundColor: colors.surfaceContainerLow,
    borderRadius: radius.md,
    minHeight: 100,
    alignItems: 'center',
    justifyContent: 'center',
    gap: spacing.sm,
  },
  pickerText: {
    ...typography.bodySmall,
    color: colors.primary,
  },
  previewWrap: {
    position: 'relative',
  },
  preview: {
    width: '100%',
    height: 180,
    borderRadius: radius.md,
  },
  removeBtn: {
    position: 'absolute',
    top: spacing.sm,
    right: spacing.sm,
    width: 32,
    height: 32,
    borderRadius: radius.full,
    backgroundColor: 'rgba(0,0,0,0.55)',
    alignItems: 'center',
    justifyContent: 'center',
  },
});
