package com.dam.ecoclime_innovations;

import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class RetrofitRecuperacionClient {
    public static RecuperacionApi getApi() {
        // Siempre crea una nueva instancia para evitar problemas de caché
        okhttp3.logging.HttpLoggingInterceptor logging = new okhttp3.logging.HttpLoggingInterceptor();
        logging.setLevel(okhttp3.logging.HttpLoggingInterceptor.Level.BODY);
        okhttp3.OkHttpClient client = new okhttp3.OkHttpClient.Builder()
                .addInterceptor(logging)
                .build();
        Retrofit retrofit = new Retrofit.Builder()
                .baseUrl("http://10.0.2.2:8085/") // Cambia por tu IP/backend
                .client(client)
                .addConverterFactory(GsonConverterFactory.create())
                .build();
        android.util.Log.d("RetrofitRecuperacionClient", "Retrofit creado y listo para peticiones a http://10.0.2.2:8085/");
        return retrofit.create(RecuperacionApi.class);
    }
}