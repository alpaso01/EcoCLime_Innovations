package com.dam.ecoclime_innovations;

import android.util.Log;
import android.os.Build;
import android.content.Context;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.net.wifi.WifiManager;
import android.net.Network;
import android.net.NetworkCapabilities;
import java.net.InetAddress;
import java.net.UnknownHostException;
import java.net.Socket;
import java.io.IOException;
import java.net.HttpURLConnection;
import java.net.URL;

import java.util.concurrent.TimeUnit;

import okhttp3.OkHttpClient;
import okhttp3.logging.HttpLoggingInterceptor;
import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;
import retrofit2.converter.scalars.ScalarsConverterFactory;

import com.google.gson.Gson;
import com.google.gson.GsonBuilder;

public class RetrofitClient {
    private static final String TAG = "RetrofitClient";
    // URLs para diferentes configuraciones
    private static final String EMULATOR_BASE_URL = "http://10.0.2.2:8085/";
    private static final String VIRTUALBOX_BASE_URL = "http://192.168.56.1:8085/";
    private static final String LOCAL_NETWORK_BASE_URL = "http://192.168.1.1:8085/";
    private static final String LOCALHOST_BASE_URL = "http://localhost:8085/";
    private static final String[] POSSIBLE_IPS = {
        "192.168.1.1",
        "192.168.0.1",
        "192.168.56.1",
        "10.0.2.2"
    };
    private static Retrofit retrofit = null;
    private static String lastWorkingUrl = null;

    private static boolean isEmulator() {
        return (Build.BRAND.startsWith("generic") && Build.DEVICE.startsWith("generic"))
                || Build.FINGERPRINT.startsWith("generic")
                || Build.FINGERPRINT.startsWith("unknown")
                || Build.HARDWARE.contains("goldfish")
                || Build.HARDWARE.contains("ranchu")
                || Build.MODEL.contains("google_sdk")
                || Build.MODEL.contains("Emulator")
                || Build.MODEL.contains("Android SDK built for x86")
                || Build.MANUFACTURER.contains("Genymotion")
                || Build.PRODUCT.contains("sdk_gphone")
                || Build.PRODUCT.contains("google_sdk")
                || Build.PRODUCT.contains("sdk")
                || Build.PRODUCT.contains("sdk_x86")
                || Build.PRODUCT.contains("vbox86p")
                || Build.PRODUCT.contains("emulator")
                || Build.PRODUCT.contains("simulator");
    }

    private static String getBaseUrl() {
        // Si tenemos una URL que funcionó anteriormente, intentar usarla primero
        if (lastWorkingUrl != null) {
            Log.d(TAG, "Usando última URL funcional: " + lastWorkingUrl);
            return lastWorkingUrl;
        }

        if (isEmulator()) {
            Log.d(TAG, "Usando URL para emulador: " + EMULATOR_BASE_URL);
            return EMULATOR_BASE_URL;
        } else {
            // Intentar primero con VirtualBox
            Log.d(TAG, "Usando URL para dispositivo físico (VirtualBox): " + VIRTUALBOX_BASE_URL);
            return VIRTUALBOX_BASE_URL;
        }
    }

    public static Retrofit getRetrofitInstance() {
        if (retrofit == null) {
            String baseUrl;
            if (isEmulator()) {
                baseUrl = EMULATOR_BASE_URL;
                Log.d(TAG, "Usando URL para emulador: " + baseUrl);
            } else if (lastWorkingUrl != null) {
                baseUrl = lastWorkingUrl;
                Log.d(TAG, "Usando última URL funcional: " + baseUrl);
            } else {
                baseUrl = LOCAL_NETWORK_BASE_URL;
                Log.d(TAG, "Usando URL de red local: " + baseUrl);
            }

            // Configurar interceptor para logging
            HttpLoggingInterceptor loggingInterceptor = new HttpLoggingInterceptor();
            loggingInterceptor.setLevel(HttpLoggingInterceptor.Level.BODY);

            // Configurar cliente OkHttp
            OkHttpClient client = new OkHttpClient.Builder()
                    .addInterceptor(loggingInterceptor)
                    .connectTimeout(15, TimeUnit.SECONDS)
                    .readTimeout(15, TimeUnit.SECONDS)
                    .writeTimeout(15, TimeUnit.SECONDS)
                    .build();

            // Configurar Gson
            Gson gson = new GsonBuilder()
                    .setDateFormat("yyyy-MM-dd'T'HH:mm:ss")
                    .create();

            // Crear instancia de Retrofit
            retrofit = new Retrofit.Builder()
                    .baseUrl(baseUrl)
                    .client(client)
                    .addConverterFactory(ScalarsConverterFactory.create())
                    .addConverterFactory(GsonConverterFactory.create(gson))
                    .build();

            Log.d(TAG, "Instancia de Retrofit creada con éxito");
        }
        return retrofit;
    }

    // Método para verificar la conectividad usando APIs modernas
    public static boolean isNetworkAvailable(Context context) {
        ConnectivityManager connectivityManager = (ConnectivityManager) context.getSystemService(Context.CONNECTIVITY_SERVICE);
        if (connectivityManager != null) {
            Network network = connectivityManager.getActiveNetwork();
            if (network != null) {
                NetworkCapabilities capabilities = connectivityManager.getNetworkCapabilities(network);
                return capabilities != null && (
                    capabilities.hasTransport(NetworkCapabilities.TRANSPORT_WIFI) ||
                    capabilities.hasTransport(NetworkCapabilities.TRANSPORT_CELLULAR) ||
                    capabilities.hasTransport(NetworkCapabilities.TRANSPORT_ETHERNET));
            }
        }
        return false;
    }

    // Método para reiniciar la instancia de Retrofit
    public static void resetInstance() {
        retrofit = null;
        Log.d(TAG, "Instancia de Retrofit reiniciada");
    }
}