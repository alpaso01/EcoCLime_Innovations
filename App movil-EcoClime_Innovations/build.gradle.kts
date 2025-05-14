// Top-level build file where you can add configuration options common to all sub-projects/modules.
plugins {
    id("com.android.application") version "8.1.0" apply false
    id("org.jetbrains.kotlin.android") version "1.9.0" apply false
    id("androidx.navigation.safeargs.kotlin") version "2.7.5" apply false
    id("com.diffplug.spotless") version "6.20.0"
}

// Configuración común para todos los proyectos
subprojects {
    // Configuración de compilación para Java y Kotlin
    plugins.withType<JavaBasePlugin> {
        extensions.configure<JavaPluginExtension> {
            toolchain {
                languageVersion.set(JavaLanguageVersion.of(8))
            }
        }
    }

        // Configuración de Kotlin
    plugins.withId("org.jetbrains.kotlin.android") {
        tasks.withType<org.jetbrains.kotlin.gradle.tasks.KotlinCompile>().configureEach {
            kotlinOptions {
                jvmTarget = "1.8"
                freeCompilerArgs = freeCompilerArgs + "-opt-in=kotlin.RequiresOptIn"
            }
        }
    }
}

// Configuración de Spotless para formateo de código
spotless {
    kotlin {
        target("**/*.kt")
        ktlint()
        trimTrailingWhitespace()
        indentWithTabs()
        endWithNewline()
    }
    
    format("misc") {
        target("*.md", ".gitignore")
        trimTrailingWhitespace()
        indentWithTabs()
        endWithNewline()
    }
}

// La tarea clean ya está definida por el plugin de Gradle