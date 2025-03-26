package com.dam.ecoclime_innovations;

import retrofit2.Call;
import retrofit2.http.Body;
import retrofit2.http.POST;

public interface CitaApi {
    @POST("citas")
    Call<Void> enviarCita(@Body Cita cita);
}
