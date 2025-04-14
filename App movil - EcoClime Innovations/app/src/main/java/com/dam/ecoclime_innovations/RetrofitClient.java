package com.dam.ecoclime_innovations;

import com.google.android.datatransport.BuildConfig;
import com.google.gson.Gson;
import com.google.gson.GsonBuilder;
import android.util.Log;

import okhttp3.OkHttpClient;
import okhttp3.logging.HttpLoggingInterceptor;
import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;
import retrofit2.converter.scalars.ScalarsConverterFactory;

import java.util.concurrent.TimeUnit;

public class RetrofitClient {

    private static Retrofit retrofit;
    private static final String TAG = "RetrofitClient";

    public static Retrofit getClient() {
        if (retrofit == null) {
            Log.d(TAG, "Creando cliente Retrofit...");

            HttpLoggingInterceptor interceptor = new HttpLoggingInterceptor();
            if (BuildConfig.DEBUG) {
                interceptor.setLevel(HttpLoggingInterceptor.Level.BODY);
            } else {
                interceptor.setLevel(HttpLoggingInterceptor.Level.NONE);
            }

            OkHttpClient client = new OkHttpClient.Builder()
                    .addInterceptor(interceptor)
                    .connectTimeout(30, TimeUnit.SECONDS)
                    .writeTimeout(30, TimeUnit.SECONDS)
                    .readTimeout(30, TimeUnit.SECONDS)
                    .addInterceptor(chain -> {
                        Log.d(TAG, "Conexión a la API: " + chain.request().url().toString());
                        return chain.proceed(chain.request());
                    })
                    .build();

            Gson gson = new GsonBuilder()
                    .setLenient()
                    .create();

            retrofit = new Retrofit.Builder()
                    .baseUrl("http://10.0.2.2:8085/")
                    .client(client)
                    .addConverterFactory(ScalarsConverterFactory.create())  // Para String (primero)
                    .addConverterFactory(GsonConverterFactory.create(gson)) // Para JSON (segundo)
                    .build();

            Log.d(TAG, "Cliente Retrofit creado con la base URL: " + "http://10.0.2.2:8085/");
        }

        return retrofit;
    }
}