package com.dam.ecoclime_innovations;

import retrofit2.Call;
import retrofit2.http.Body;
import retrofit2.http.DELETE;
import retrofit2.http.GET;
import retrofit2.http.POST;
import retrofit2.http.Path;
import retrofit2.http.Query;

import java.util.List;

// Interfaz de Retrofit para la comunicación con la API
public interface ApiService {

    // Endpoint para registrar un usuario
    @POST("/usuarios/register")
    Call<Usuario> registerUser(@Body Usuario usuario);

    // Endpoint para hacer login
    @POST("/usuarios/login")  // Cambié de @GET a @POST
    Call<String> loginUser(@Body LoginRequest loginRequest);

    // Endpoint para agendar una cita
    @POST("citas/agendar/{usuarioId}")
    Call<Cita> agendarCita(@Body Cita cita, @Path("usuarioId") int usuarioId);

    // Endpoint para obtener el historial de citas de un usuario
    @GET("citas/historial/{usuarioId}")
    Call<List<Cita>> obtenerHistorialCitas(@Path("usuarioId") int usuarioId);

    // Endpoint para anular una cita
    @DELETE("citas/anular/{citaId}")
    Call<String> anularCita(@Path("citaId") int citaId);

    @POST("citas")
    Call<Void> enviarCita(@Body Cita cita);
}
