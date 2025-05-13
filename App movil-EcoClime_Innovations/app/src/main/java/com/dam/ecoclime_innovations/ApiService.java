package com.dam.ecoclime_innovations;

import java.util.List;
import retrofit2.Call;
import retrofit2.http.*;

public interface ApiService {

    //-----INICIO DE SESIÓN Y REGISTRO-----

    @POST("api/usuarios/registro")
    Call<Usuario> registerUser(@Body Usuario usuario);

    @POST("api/usuarios/login")
    Call<Usuario> loginUser(@Body Usuario usuario);



    //-------OBTENER DATOS DE USUARIO-------

    @GET("api/usuarios/user/{email}")
    Call<Usuario> obtenerUsuarioPorEmail(@Path("email") String email);

    @GET("api/usuarios")
    Call<Usuario> obtenerUsuarioPorEmailQuery(@Query("email") String email);


    //----CITAS------

    @GET("api/citas/historial/{usuarioId}")
    Call<List<Cita>> obtenerHistorialCitas(@Path("usuarioId") int usuarioId);

    @GET("api/citas/email/{email}")
    Call<List<Cita>> obtenerHistorialCitasPorEmail(@Path("email") String email);

    @GET("api/citas/{citaId}")
    Call<Cita> obtenerCita(@Path("citaId") int citaId);

    @POST("api/citas/crear/{usuarioId}")
    Call<Cita> crearCita(@Path("usuarioId") int usuarioId, @Body Cita cita);

    @POST("api/citas/agendar/{usuarioId}")
    Call<Cita> agendarCita(@Path("usuarioId") int usuarioId, @Body Cita cita);

    @PUT("api/citas/{citaId}")
    Call<Void> actualizarCita(@Path("citaId") int citaId, @Body Cita cita);

    @DELETE("api/citas/anular/{citaId}")
    Call<Void> eliminarCita(@Path("citaId") int citaId);
}
