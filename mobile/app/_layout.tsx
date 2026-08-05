import 'react-native-gesture-handler';
import { Stack } from 'expo-router';
import { StatusBar } from 'expo-status-bar';
import { useFonts, Manrope_500Medium, Manrope_600SemiBold, Manrope_700Bold } from '@expo-google-fonts/manrope';
import { Inter_400Regular, Inter_500Medium, Inter_600SemiBold } from '@expo-google-fonts/inter';
import { ActivityIndicator, View } from 'react-native';
import { usePushNotifications } from '../src/hooks/usePushNotifications';
import { colors } from '../src/theme';

function AppShell() {
  usePushNotifications();

  return (
    <>
      <StatusBar style="dark" />
      <Stack screenOptions={{ headerShown: false, animation: 'slide_from_right' }}>
        <Stack.Screen name="(tabs)" />
        <Stack.Screen name="haberler" />
        <Stack.Screen name="news" />
        <Stack.Screen name="ihaleler" />
        <Stack.Screen name="meclis-uyeleri" />
        <Stack.Screen name="mudur" />
        <Stack.Screen name="meclis-kararlari" />
        <Stack.Screen name="saatler" />
        <Stack.Screen name="requests/new" />
        <Stack.Screen name="directory" />
        <Stack.Screen name="notifications" />
      </Stack>
    </>
  );
}

export default function RootLayout() {
  const [loaded] = useFonts({
    Manrope_500Medium,
    Manrope_600SemiBold,
    Manrope_700Bold,
    Inter_400Regular,
    Inter_500Medium,
    Inter_600SemiBold,
  });

  if (!loaded) {
    return (
      <View style={{ flex: 1, alignItems: 'center', justifyContent: 'center', backgroundColor: colors.background }}>
        <ActivityIndicator size="large" color={colors.primary} />
      </View>
    );
  }

  return <AppShell />;
}
