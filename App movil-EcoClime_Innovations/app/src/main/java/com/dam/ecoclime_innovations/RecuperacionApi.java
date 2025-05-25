package com.dam.ecoclime_innovations;

import java.util.Map;
import retrofit2.Call;
import retrofit2.http.Body;
import retrofit2.http.POST;

public interface RecuperacionApi {
    @POST("/api/auth/enviar-codigo")
    Call<Void> enviarCodigo(@Body Map<String, String> body);

    @POST("/api/auth/verificar-codigo")
    Call<Void> verificarCodigo(@Body Map<String, String> body);

    @POST("/api/auth/restablecer-contrasena")
    Call<Void> restablecerContrasena(@Body Map<String, String> body);
}
