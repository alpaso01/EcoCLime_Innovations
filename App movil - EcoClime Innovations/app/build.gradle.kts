plugins {
    id("com.android.application")
}

android {
    namespace = "com.dam.ecoclime_innovations"
    compileSdk = 35

    defaultConfig {
        applicationId = "com.dam.ecoclime_innovations"
        minSdk = 24
        targetSdk = 34
        versionCode = 1
        versionName = "1.0"
    }

    buildTypes {
        getByName("release") {
            isMinifyEnabled = false
            proguardFiles(
                getDefaultProguardFile("proguard-android-optimize.txt"),
                "proguard-rules.pro"
            )
        }
    }

    compileOptions {
        sourceCompatibility = JavaVersion.VERSION_1_8
        targetCompatibility = JavaVersion.VERSION_1_8
    }
}

dependencies {
    implementation("androidx.appcompat:appcompat:1.3.1")
    implementation("com.google.android.material:material:1.6.0")
    implementation("androidx.activity:activity:1.2.4")
    implementation("androidx.constraintlayout:constraintlayout:2.1.1")
    implementation("com.google.firebase:firebase-inappmessaging:19.0.5")

    // Retrofit + Gson
    implementation("com.squareup.retrofit2:retrofit:2.9.0")
    implementation("com.squareup.retrofit2:converter-gson:2.9.0")
    implementation("com.squareup.retrofit2:converter-scalars:2.9.0")
    implementation("com.squareup.okhttp3:okhttp:4.9.1")
    implementation("com.squareup.okhttp3:logging-interceptor:4.9.1")
    implementation(libs.monitor)
    implementation(libs.ext.junit)
    testImplementation("junit:junit:4.12")

    // Evitamos errores por clases duplicadas
    configurations.all {
        exclude(group = "com.google.guava", module = "listenablefuture")
    }
}
