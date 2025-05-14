package com.dam.ecoclime_innovations;

import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.os.Looper;

import androidx.appcompat.app.AppCompatActivity;

public class PantallaCargaActivity extends AppCompatActivity {
    // Duración de la pantalla de carga en milisegundos (3.5 segundos)
    private static final int SPLASH_DURATION = 3500;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_pantalla_carga);

        // Usar un Handler para cambiar a la siguiente actividad después del tiempo de espera
        new Handler(Looper.getMainLooper()).postDelayed(new Runnable() {
            @Override
            public void run() {
                // Iniciar la actividad de login
                Intent intent = new Intent(PantallaCargaActivity.this, LoginActivity.class);
                startActivity(intent);
                // Cerrar esta actividad para que el usuario no pueda volver atrás
                finish();
            }
        }, SPLASH_DURATION);
    }
}
