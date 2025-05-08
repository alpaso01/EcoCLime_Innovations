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
    // Asegurarse que la URL base es correcta
    // Para el emulador Android estándar, 10.0.2.2 apunta al localhost de la máquina host
    private static final String BASE_URL = "http://10.0.2.2:8085/";
    private static Retrofit retrofit = null;

    public static Retrofit getRetrofitInstance() {
        if (retrofit == null) {
            // Configurar el interceptor de logging
            HttpLoggingInterceptor loggingInterceptor = new HttpLoggingInterceptor();
            loggingInterceptor.setLevel(HttpLoggingInterceptor.Level.BODY);

            // Configurar el cliente OkHttp
            OkHttpClient client = new OkHttpClient.Builder()
                    .addInterceptor(loggingInterceptor)
                    .connectTimeout(30, TimeUnit.SECONDS)
                    .readTimeout(30, TimeUnit.SECONDS)
                    .writeTimeout(30, TimeUnit.SECONDS)
                    .build();

            // Configurar Gson
            Gson gson = new GsonBuilder()
                    .setLenient()
                    .create();

            // Crear la instancia de Retrofit
            retrofit = new Retrofit.Builder()
                    .baseUrl(BASE_URL)
                    .client(client)
                    .addConverterFactory(ScalarsConverterFactory.create())
                    .addConverterFactory(GsonConverterFactory.create(gson))
                    .build();
        }
        return retrofit;
    }

}