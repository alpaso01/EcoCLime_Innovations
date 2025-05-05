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
    private static final String BASE_URL = "http://10.0.2.2:8085/api/";
    private static Retrofit retrofit = null;

    public static Retrofit getRetrofitInstance() {
        if (retrofit == null) {
            retrofit = new Retrofit.Builder()
                    .baseUrl(BASE_URL)
                    .addConverterFactory(GsonConverterFactory.create())
                    .build();
        }
        return retrofit;
    }

}