import MaterialIcons from '@expo/vector-icons/MaterialIcons';
import * as ImagePicker from 'expo-image-picker';
import { Alert, Image, Pressable, StyleSheet, Text, View } from 'react-native';
import { colors, radius, spacing, typography } from '../theme';

type Props = {
  uri: string | null;
  onChange: (uri: string | null) => void;
};

export function PhotoPicker({ uri, onChange }: Props) {
  const applyAsset = (asset: ImagePicker.ImagePickerAsset) => {
    onChange(asset.uri);
  };

  const pickFromGallery = async () => {
    const permission = await ImagePicker.requestMediaLibraryPermissionsAsync();
    if (!permission.granted) {
      Alert.alert('İzin Gerekli', 'Fotoğraf eklemek için galeri izni vermeniz gerekiyor.');
      return;
    }

    const result = await ImagePicker.launchImageLibraryAsync({
      mediaTypes: ['images'],
      quality: 0.7,
      allowsEditing: true,
      aspect: [4, 3],
    });

    if (!result.canceled && result.assets[0]) {
      applyAsset(result.assets[0]);
    }
  };

  const takePhoto = async () => {
    const permission = await ImagePicker.requestCameraPermissionsAsync();
    if (!permission.granted) {
      Alert.alert('İzin Gerekli', 'Fotoğraf çekmek için kamera izni vermeniz gerekiyor.');
      return;
    }

    const result = await ImagePicker.launchCameraAsync({
      mediaTypes: ['images'],
      quality: 0.7,
      allowsEditing: true,
      aspect: [4, 3],
    });

    if (!result.canceled && result.assets[0]) {
      applyAsset(result.assets[0]);
    }
  };

  const showOptions = () => {
    Alert.alert('Fotoğraf Ekle', 'Nasıl eklemek istersiniz?', [
      { text: 'Kamera', onPress: () => void takePhoto() },
      { text: 'Galeri', onPress: () => void pickFromGallery() },
      { text: 'İptal', style: 'cancel' },
    ]);
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
        <View style={styles.actions}>
          <Pressable style={styles.picker} onPress={showOptions}>
            <MaterialIcons name="add-a-photo" size={28} color={colors.primary} />
            <Text style={styles.pickerText}>Kamera veya galeri</Text>
          </Pressable>
          <View style={styles.row}>
            <Pressable style={styles.secondaryBtn} onPress={() => void takePhoto()}>
              <MaterialIcons name="photo-camera" size={20} color={colors.primary} />
              <Text style={styles.secondaryText}>Çek</Text>
            </Pressable>
            <Pressable style={styles.secondaryBtn} onPress={() => void pickFromGallery()}>
              <MaterialIcons name="photo-library" size={20} color={colors.primary} />
              <Text style={styles.secondaryText}>Galeri</Text>
            </Pressable>
          </View>
        </View>
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
  actions: {
    gap: spacing.sm,
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
  row: {
    flexDirection: 'row',
    gap: spacing.sm,
  },
  secondaryBtn: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: spacing.xs,
    backgroundColor: colors.surfaceContainerLowest,
    borderRadius: radius.md,
    borderWidth: 1,
    borderColor: 'rgba(11, 110, 153, 0.12)',
    paddingVertical: spacing.md,
  },
  secondaryText: {
    ...typography.bodySmall,
    color: colors.primary,
    fontWeight: '600',
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
