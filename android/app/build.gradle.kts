import java.util.Properties

plugins { id("com.android.application") }

val signingFile = rootProject.file("signing.properties")
val localSigning = Properties().apply { if (signingFile.exists()) signingFile.inputStream().use { load(it) } }

android {
    namespace = "site.flexboxstudio.psseleraku"
    compileSdk { version = release(36) { minorApiLevel = 1 } }
    buildToolsVersion = "36.0.0"
    defaultConfig {
        applicationId = "site.flexboxstudio.psseleraku"
        minSdk = 24
        targetSdk = 36
        versionCode = 1
        versionName = "1.0.0"
    }
    if (signingFile.exists()) {
        signingConfigs.create("localRelease") {
            storeFile = rootProject.file(localSigning.getProperty("storeFile"))
            storePassword = localSigning.getProperty("storePassword")
            keyAlias = localSigning.getProperty("keyAlias")
            keyPassword = localSigning.getProperty("keyPassword")
        }
    }
    buildTypes {
        release {
            isMinifyEnabled = false
            if (signingFile.exists()) signingConfig = signingConfigs.getByName("localRelease")
        }
    }
    compileOptions {
        sourceCompatibility = JavaVersion.VERSION_17
        targetCompatibility = JavaVersion.VERSION_17
    }
    lint { abortOnError = true }
}

dependencies {
    implementation("com.google.androidbrowserhelper:androidbrowserhelper:2.8.0")
    testImplementation("junit:junit:4.13.2")
}
