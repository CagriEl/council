import { Tabs } from 'expo-router';

export default function TabsLayout() {
  return (
    <Tabs screenOptions={{ headerShown: false, tabBarStyle: { display: 'none' } }}>
      <Tabs.Screen name="index" />
      <Tabs.Screen name="payment" />
      <Tabs.Screen name="card" />
      <Tabs.Screen name="infrastructure" />
      <Tabs.Screen name="directory" />
    </Tabs>
  );
}
