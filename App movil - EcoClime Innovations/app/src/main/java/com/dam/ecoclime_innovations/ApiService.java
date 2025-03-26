package com.dam.ecoclime_innovations;

import retrofit2.Call;
import retrofit2.http.Body;
import retrofit2.http.DELETE;
import retrofit2.http.GET;
import retrofit2.http.POST;
import retrofit2.http.Path;

import java.util.List;

// Interfaz de Retrofit para la comunicación con la API
public interface ApiService {

    // Endpoint para registrar un usuario
    @POST("api/ApiEcoClimeInnovations/usuarios/register")
    Call<Usuario> registerUser(@Body Usuario usuario);

    // Endpoint para hacer login
    @POST("api/ApiEcoClimeInnovations/usuarios/login")
    Call<String> loginUser(@Body Usuario usuario);

    // Endpoint para agendar una cita
    @POST("citas/agendar/{usuarioId}")
    Call<Cita> agendarCita(@Body Cita cita, @Path("usuarioId") Long usuarioId);

    // Endpoint para obtener el historial de citas de un usuario
    @GET("citas/historial/{usuarioId}")
    Call<List<Cita>> obtenerHistorialCitas(@Path("usuarioId") Long usuarioId);

    // Endpoint para anular una cita
    @DELETE("citas/anular/{citaId}")
    Call<String> anularCita(@Path("citaId") Long citaId);
}
