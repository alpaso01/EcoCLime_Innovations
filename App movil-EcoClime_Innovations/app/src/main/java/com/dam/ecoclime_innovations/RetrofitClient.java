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
            // Configurar el interceptor de logging
            HttpLoggingInterceptor loggingInterceptor = new HttpLoggingInterceptor(message -> {
                Log.d(TAG, "OkHttp: " + message);
            });
            loggingInterceptor.setLevel(HttpLoggingInterceptor.Level.BODY);

            // Configurar el cliente OkHttp con timeouts más largos y reintentos
            OkHttpClient client = new OkHttpClient.Builder()
                    .addInterceptor(loggingInterceptor)
                    .connectTimeout(60, TimeUnit.SECONDS)
                    .readTimeout(60, TimeUnit.SECONDS)
                    .writeTimeout(60, TimeUnit.SECONDS)
                    .retryOnConnectionFailure(true)
                    .build();

            // Configurar Gson
            Gson gson = new GsonBuilder()
                    .setLenient()
                    .create();

            // Obtener la URL base
            String baseUrl = getBaseUrl();
            Log.d(TAG, "URL base seleccionada: " + baseUrl);

            // Crear la instancia de Retrofit
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

    // Método para verificar la conectividad
    public static boolean isNetworkAvailable(Context context) {
        ConnectivityManager connectivityManager = (ConnectivityManager) context.getSystemService(Context.CONNECTIVITY_SERVICE);
        if (connectivityManager != null) {
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
                Network network = connectivityManager.getActiveNetwork();
                if (network != null) {
                    NetworkCapabilities capabilities = connectivityManager.getNetworkCapabilities(network);
                    if (capabilities != null) {
                        boolean isConnected = capabilities.hasCapability(NetworkCapabilities.NET_CAPABILITY_INTERNET);
                        Log.d(TAG, "Estado de la red: " + (isConnected ? "Conectado" : "Desconectado"));
                        
                        if (isConnected) {
                            if (capabilities.hasTransport(NetworkCapabilities.TRANSPORT_WIFI)) {
                                Log.d(TAG, "Conectado a WiFi");
                                WifiManager wifiManager = (WifiManager) context.getApplicationContext().getSystemService(Context.WIFI_SERVICE);
                                if (wifiManager != null) {
                                    int ipAddress = wifiManager.getConnectionInfo().getIpAddress();
                                    String ipString = String.format("%d.%d.%d.%d",
                                            (ipAddress & 0xff),
                                            (ipAddress >> 8 & 0xff),
                                            (ipAddress >> 16 & 0xff),
                                            (ipAddress >> 24 & 0xff));
                                    Log.d(TAG, "Dirección IP del dispositivo (WiFi): " + ipString);
                                    
                                    // Intentar hacer ping a las posibles IPs del servidor
                                    for (String ip : POSSIBLE_IPS) {
                                        try {
                                            InetAddress address = InetAddress.getByName(ip);
                                            boolean reachable = address.isReachable(1000);
                                            Log.d(TAG, "Ping a " + ip + ": " + (reachable ? "Alcanzable" : "No alcanzable"));
                                            
                                            // Intentar conectar al puerto 8085
                                            if (reachable) {
                                                try (Socket socket = new Socket()) {
                                                    socket.connect(new java.net.InetSocketAddress(ip, 8085), 1000);
                                                    Log.d(TAG, "Puerto 8085 abierto en " + ip);
                                                    
                                                    // Intentar hacer una petición HTTP
                                                    try {
                                                        URL url = new URL("http://" + ip + ":8085/");
                                                        HttpURLConnection connection = (HttpURLConnection) url.openConnection();
                                                        connection.setConnectTimeout(1000);
                                                        connection.setReadTimeout(1000);
                                                        connection.setRequestMethod("GET");
                                                        int responseCode = connection.getResponseCode();
                                                        Log.d(TAG, "Respuesta HTTP de " + ip + ": " + responseCode);
                                                        
                                                        if (responseCode == HttpURLConnection.HTTP_OK) {
                                                            lastWorkingUrl = "http://" + ip + ":8085/";
                                                            Log.d(TAG, "URL funcional encontrada: " + lastWorkingUrl);
                                                        }
                                                    } catch (IOException e) {
                                                        Log.e(TAG, "Error en petición HTTP a " + ip + ": " + e.getMessage());
                                                    }
                                                } catch (IOException e) {
                                                    Log.e(TAG, "Puerto 8085 cerrado en " + ip + ": " + e.getMessage());
                                                }
                                            }
                                        } catch (Exception e) {
                                            Log.e(TAG, "Error al hacer ping a " + ip + ": " + e.getMessage());
                                        }
                                    }
                                }
                            } else if (capabilities.hasTransport(NetworkCapabilities.TRANSPORT_CELLULAR)) {
                                Log.d(TAG, "Conectado a red móvil");
                                // Para redes móviles, intentar con la IP pública del servidor
                                for (String ip : POSSIBLE_IPS) {
                                    try {
                                        InetAddress address = InetAddress.getByName(ip);
                                        boolean reachable = address.isReachable(1000);
                                        Log.d(TAG, "Ping a " + ip + " (red móvil): " + (reachable ? "Alcanzable" : "No alcanzable"));
                                        
                                        // Intentar conectar al puerto 8085
                                        if (reachable) {
                                            try (Socket socket = new Socket()) {
                                                socket.connect(new java.net.InetSocketAddress(ip, 8085), 1000);
                                                Log.d(TAG, "Puerto 8085 abierto en " + ip + " (red móvil)");
                                                
                                                // Intentar hacer una petición HTTP
                                                try {
                                                    URL url = new URL("http://" + ip + ":8085/");
                                                    HttpURLConnection connection = (HttpURLConnection) url.openConnection();
                                                    connection.setConnectTimeout(1000);
                                                    connection.setReadTimeout(1000);
                                                    connection.setRequestMethod("GET");
                                                    int responseCode = connection.getResponseCode();
                                                    Log.d(TAG, "Respuesta HTTP de " + ip + " (red móvil): " + responseCode);
                                                    
                                                    if (responseCode == HttpURLConnection.HTTP_OK) {
                                                        lastWorkingUrl = "http://" + ip + ":8085/";
                                                        Log.d(TAG, "URL funcional encontrada: " + lastWorkingUrl);
                                                    }
                                                } catch (IOException e) {
                                                    Log.e(TAG, "Error en petición HTTP a " + ip + " (red móvil): " + e.getMessage());
                                                }
                                            } catch (IOException e) {
                                                Log.e(TAG, "Puerto 8085 cerrado en " + ip + " (red móvil): " + e.getMessage());
                                            }
                                        }
                                    } catch (Exception e) {
                                        Log.e(TAG, "Error al hacer ping a " + ip + " (red móvil): " + e.getMessage());
                                    }
                                }
                            }
                        }
                        return isConnected;
                    }
                }
            } else {
                NetworkInfo activeNetworkInfo = connectivityManager.getActiveNetworkInfo();
                boolean isConnected = activeNetworkInfo != null && activeNetworkInfo.isConnected();
                Log.d(TAG, "Estado de la red (API < 23): " + (isConnected ? "Conectado" : "Desconectado"));
                return isConnected;
            }
        }
        Log.d(TAG, "No se pudo obtener el estado de la red");
        return false;
    }

    // Método para reiniciar la instancia de Retrofit
    public static void resetInstance() {
        retrofit = null;
        Log.d(TAG, "Instancia de Retrofit reiniciada");
    }
}