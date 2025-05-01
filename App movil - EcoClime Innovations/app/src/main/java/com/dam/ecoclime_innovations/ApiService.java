package com.dam.ecoclime_innovations;

import java.util.List;
import retrofit2.Call;
import retrofit2.http.*;

// Interfaz de Retrofit para la comunicación con la API
public interface ApiService {


    //-----INICIO DE SESIÓN Y REGISTRO-----




    // Endpoint para registrar un usuario
    @POST("usuarios/register")
    Call<Usuario> registerUser(@Body Usuario usuario);

    // Endpoint para hacer login
    @POST("usuarios/login")
    Call<String> loginUser(@Body Usuario usuario);









    //-------OBTENER DATOS DE USUARIO-------


    // Endpoint para obtener datos del usuario por email
    @GET("usuarios/user/{email}")
    Call<Usuario> obtenerUsuarioPorEmail(@Path("email") String email);

    // Endpoint alternativo para obtener datos del usuario por email (vía query parameter)
    @GET("usuarios")
    Call<Usuario> obtenerUsuarioPorEmailQuery(@Query("email") String email);







    //----CITAS------




    // Endpoints para citas
    @GET("citas/usuario/{usuarioId}")
    Call<List<Cita>> obtenerHistorialCitas(@Path("usuarioId") int usuarioId);

    @GET("citas/email/{email}")
    Call<List<Cita>> obtenerHistorialCitasPorEmail(@Path("email") String email);

    @GET("citas/{citaId}")
    Call<Cita> obtenerCita(@Path("citaId") int citaId);

    @POST("citas/{usuarioId}")
    Call<Cita> agendarCita(@Path("usuarioId") int usuarioId, @Body Cita cita);

    @PUT("citas/{citaId}")
    Call<Void> actualizarCita(@Path("citaId") int citaId, @Body Cita cita);

    @DELETE("citas/{citaId}")
    Call<Void> eliminarCita(@Path("citaId") int citaId);
}
