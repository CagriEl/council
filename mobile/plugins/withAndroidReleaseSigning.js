/**
 * Release AAB/APK imzalaması için gradle.properties anahtarlarını build.gradle'a bağlar.
 * Değerler android-signing.properties dosyasından scripts/apply-android-signing.js ile aktarılır.
 */
const { withAppBuildGradle } = require('@expo/config-plugins');

const RELEASE_SIGNING = `
        release {
            if (project.hasProperty('KIRKLARELI_UPLOAD_STORE_FILE')) {
                storeFile file(KIRKLARELI_UPLOAD_STORE_FILE)
                storePassword KIRKLARELI_UPLOAD_STORE_PASSWORD
                keyAlias KIRKLARELI_UPLOAD_KEY_ALIAS
                keyPassword KIRKLARELI_UPLOAD_KEY_PASSWORD
            }
        }`;

const RELEASE_SIGNING_MARKER = 'KIRKLARELI_UPLOAD_STORE_FILE';

function withAndroidReleaseSigning(config) {
  return withAppBuildGradle(config, (gradleConfig) => {
    let contents = gradleConfig.modResults.contents;

    if (contents.includes(RELEASE_SIGNING_MARKER)) {
      return gradleConfig;
    }

    contents = contents.replace(
      /(signingConfigs\s*\{\s*debug\s*\{[\s\S]*?\n\s*\})\s*\}/,
      `$1${RELEASE_SIGNING}\n    }`,
    );

    contents = contents.replace(
      /(buildTypes\s*\{[\s\S]*?release\s*\{[\s\S]*?)signingConfig signingConfigs\.debug/,
      `$1signingConfig project.hasProperty('${RELEASE_SIGNING_MARKER}') ? signingConfigs.release : signingConfigs.debug`,
    );

    gradleConfig.modResults.contents = contents;
    return gradleConfig;
  });
}

module.exports = withAndroidReleaseSigning;
