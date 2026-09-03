/**
 * Release AAB boyutunu düşürmek için:
 * - native debug sembollerini AAB'den çıkar
 * - GIF/WebP native codec'lerini kapat
 * - yalnızca tr/en kaynaklarını paketle
 */
const {
  withAppBuildGradle,
  withGradleProperties,
} = require('@expo/config-plugins');

function withAndroidSizeOptimizations(config) {
  config = withGradleProperties(config, (gradleConfig) => {
    const updates = {
      'expo.gif.enabled': 'false',
      'expo.webp.enabled': 'false',
      'expo.webp.animated': 'false',
      'android.enableBundleCompression': 'true',
    };

    for (const [key, value] of Object.entries(updates)) {
      const existing = gradleConfig.modResults.find((item) => item.key === key);
      if (existing) {
        existing.value = value;
      } else {
        gradleConfig.modResults.push({ type: 'property', key, value });
      }
    }

    return gradleConfig;
  });

  config = withAppBuildGradle(config, (gradleConfig) => {
    let contents = gradleConfig.modResults.contents;

    if (!contents.includes('debugSymbolLevel')) {
      contents = contents.replace(
        /defaultConfig\s*\{/,
        `defaultConfig {
        ndk {
            debugSymbolLevel 'NONE'
        }
        resConfigs "tr", "en"`,
      );
    }

    gradleConfig.modResults.contents = contents;
    return gradleConfig;
  });

  return config;
}

module.exports = withAndroidSizeOptimizations;
