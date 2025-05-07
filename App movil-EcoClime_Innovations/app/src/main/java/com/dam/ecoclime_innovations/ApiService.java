package com.dam.ecoclime_innovations;

import java.util.List;
import retrofit2.Call;
import retrofit2.http.*;

public interface ApiService {

    //-----INICIO DE SESIÓN Y REGISTRO-----

    @POST("usuarios/registro")
    Call<Usuario> registerUser(@Body Usuario usuario);

    @POST("usuarios/login")
    Call<Usuario> loginUser(@Body Usuario usuario);



    //-------OBTENER DATOS DE USUARIO-------

    @GET("usuarios/user/{email}")
    Call<Usuario> obtenerUsuarioPorEmail(@Path("email") String email);

    @GET("usuarios")
    Call<Usuario> obtenerUsuarioPorEmailQuery(@Query("email") String email);



    //----CITAS------

    @GET("citas/historial/{usuarioId}")
    Call<List<Cita>> obtenerHistorialCitas(@Path("usuarioId") int usuarioId);

    @GET("citas/email/{email}")
    Call<List<Cita>> obtenerHistorialCitasPorEmail(@Path("email") String email);

    @GET("citas/{citaId}")
    Call<Cita> obtenerCita(@Path("citaId") int citaId);

    @POST("citas/agendar/{usuarioId}")
    Call<Cita> agendarCita(@Path("usuarioId") int usuarioId, @Body Cita cita);

    @PUT("citas/{citaId}")
    Call<Void> actualizarCita(@Path("citaId") int citaId, @Body Cita cita);

    @DELETE("citas/anular/{citaId}")
    Call<Void> eliminarCita(@Path("citaId") int citaId);
}
