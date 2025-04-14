package com.dam.ecoclime_innovations;

import com.google.android.datatransport.BuildConfig;
import android.util.Log;  // Importa Log para los logs en Android

import okhttp3.OkHttpClient;
import okhttp3.logging.HttpLoggingInterceptor;
import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;
import retrofit2.converter.scalars.ScalarsConverterFactory;

import java.util.concurrent.TimeUnit;

public class RetrofitClient {

    private static Retrofit retrofit;

    // Log para indicar si la conexión a la API está configurada
    private static final String TAG = "RetrofitClient";  // Usamos una etiqueta para los logs

    public static Retrofit getClient() {
        if (retrofit == null) {

            // Log para verificar si se está creando la instancia de Retrofit
            Log.d(TAG, "Creando cliente Retrofit...");

            // Configuración del interceptor de logging
            HttpLoggingInterceptor interceptor = new HttpLoggingInterceptor();

            // Solo habilitar logs en modo DEBUG
            if (BuildConfig.DEBUG) {
                interceptor.setLevel(HttpLoggingInterceptor.Level.BODY);  // Detalles completos para depuración
            } else {
                interceptor.setLevel(HttpLoggingInterceptor.Level.NONE);  // Desactivar logs en producción
            }

            // Configuración del cliente OkHttp con tiempos de espera ajustados
            OkHttpClient client = new OkHttpClient.Builder()
                    .addInterceptor(interceptor)  // Interceptor de logging
                    .connectTimeout(30, TimeUnit.SECONDS)  // Tiempo de conexión
                    .writeTimeout(30, TimeUnit.SECONDS)    // Tiempo de escritura
                    .readTimeout(30, TimeUnit.SECONDS)     // Tiempo de lectura
                    .addInterceptor(chain -> {
                        // Log para cada solicitud de red antes de enviarla
                        Log.d(TAG, "Conexión a la API: " + chain.request().url().toString());
                        return chain.proceed(chain.request());
                    })
                    .build();

            // Configuración de Retrofit
            retrofit = new Retrofit.Builder()
                    .baseUrl("http://10.0.2.2:8085/")  // Usa esta IP si estás en emulador, cambiar si es un dispositivo físico
                    .client(client)  // Cliente con configuración OkHttp
                    .addConverterFactory(GsonConverterFactory.create())     // Para JSON
                    .addConverterFactory(ScalarsConverterFactory.create())  // Para String
                    .build();

            // Log para confirmar que Retrofit está listo
            Log.d(TAG, "Cliente Retrofit creado con la base URL: " + "http://10.0.2.2:8085/");
        }

        return retrofit;
    }
}
