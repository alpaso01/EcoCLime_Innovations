package com.dam.ecoclime_innovations;

import android.util.Log;

import java.util.concurrent.TimeUnit;

import okhttp3.OkHttpClient;
import okhttp3.logging.HttpLoggingInterceptor;
import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;
import retrofit2.converter.scalars.ScalarsConverterFactory;

import com.google.gson.Gson;
import com.google.gson.GsonBuilder;

public class RetrofitClient {
    private static final String BASE_URL = "http://10.0.2.2:8085/";
    private static Retrofit retrofit = null;

    public static Retrofit getRetrofitInstance() {
        if (retrofit == null) {
            // Crear un interceptor de logging para depuración
            HttpLoggingInterceptor loggingInterceptor = new HttpLoggingInterceptor(message -> 
                Log.d("RetrofitClient", message))
                .setLevel(HttpLoggingInterceptor.Level.BODY);

            // Crear un cliente OkHttp con el interceptor y timeouts más largos
            OkHttpClient client = new OkHttpClient.Builder()
                .addInterceptor(loggingInterceptor)
                .connectTimeout(60, TimeUnit.SECONDS)  // Aumentar tiempo de conexión
                .readTimeout(60, TimeUnit.SECONDS)     // Aumentar tiempo de lectura
                .writeTimeout(60, TimeUnit.SECONDS)    // Aumentar tiempo de escritura
                .retryOnConnectionFailure(true)        // Reintentar en caso de fallo
                .build();

            // Configurar Gson para ser más flexible
            Gson gson = new GsonBuilder()
                .setLenient()
                .serializeNulls()   // Serializar valores nulos
                .create();

            // Construir Retrofit con el cliente OkHttp
            retrofit = new Retrofit.Builder()
                .baseUrl(BASE_URL)
                .addConverterFactory(ScalarsConverterFactory.create()) // Para respuestas de texto plano (primero para prioridad)
                .addConverterFactory(GsonConverterFactory.create(gson)) // Para respuestas JSON
                .client(client)
                .build();
            
            Log.d("RetrofitClient", "Iniciando petición a: " + BASE_URL);
        }
        return retrofit;
    }

    // Método para limpiar la instancia de Retrofit (útil en caso de errores)
    public static void clearInstance() {
        retrofit = null;
        Log.d("RetrofitClient", "Instancia de Retrofit reiniciada");
    }

    // Mantener el método original por compatibilidad
    public static Retrofit getClient() {
        return getRetrofitInstance();
    }

    // Método para verificar la conexión
    public static boolean isServerAvailable() {
        try {
            OkHttpClient client = new OkHttpClient.Builder()
                    .connectTimeout(5, TimeUnit.SECONDS)
                    .build();

            okhttp3.Request request = new okhttp3.Request.Builder()
                    .url(BASE_URL)
                    .build();

            okhttp3.Response response = client.newCall(request).execute();
            return response.isSuccessful();
        } catch (Exception e) {
            Log.e("RetrofitClient", "Error al verificar el servidor: " + e.getMessage());
            return false;
        }
    }
}