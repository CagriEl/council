const { getDefaultConfig } = require('expo/metro-config');

/** @type {import('expo/metro-config').MetroConfig} */
const config = getDefaultConfig(__dirname);

// Kullanılmayan vector-icon fontlarını paket dışı bırak (yalnızca MaterialIcons).
config.resolver.blockList = [
  /node_modules[/\\]@expo[/\\]vector-icons[/\\]build[/\\]vendor[/\\]react-native-vector-icons[/\\]Fonts[/\\](?!MaterialIcons\.ttf$).*/,
];

module.exports = config;
