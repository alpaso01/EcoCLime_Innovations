package com.dam.ecoclime_innovations;

import android.content.Intent;
import android.os.Bundle;
import android.view.animation.Animation;
import android.view.animation.AnimationUtils;
import android.widget.Button;

import androidx.appcompat.app.AppCompatActivity;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;

public class pantalla_principal extends AppCompatActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_pantalla_principal);

        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main), (v, insets) -> {
            Insets systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars());
            v.setPadding(systemBars.left, systemBars.top, systemBars.right, systemBars.bottom);
            return insets;
        });

        // Cargar animación
        Animation animEntrada = AnimationUtils.loadAnimation(this, R.anim.anim_boton_entrada);

        // Botones
        Button botonElegir = findViewById(R.id.botonElegir);
        Button botonAnularCita = findViewById(R.id.botonAnularCita);
        Button botonHistorialCitas = findViewById(R.id.botonHistorialCitas);

        // Aplicar animación de entrada
        botonElegir.startAnimation(animEntrada);
        botonAnularCita.startAnimation(animEntrada);
        botonHistorialCitas.startAnimation(animEntrada);

        // Listeners
        botonElegir.setOnClickListener(v -> {
            v.animate().scaleX(0.95f).scaleY(0.95f).setDuration(100).withEndAction(() -> {
                Intent intent = new Intent(pantalla_principal.this, ElegirCita.class);
                startActivity(intent);
                v.setScaleX(1f);
                v.setScaleY(1f);
            }).start();
        });

        botonAnularCita.setOnClickListener(v -> {
            v.animate().scaleX(0.95f).scaleY(0.95f).setDuration(100).withEndAction(() -> {
                Intent intent = new Intent(pantalla_principal.this, anular_cita.class);
                startActivity(intent);
                v.setScaleX(1f);
                v.setScaleY(1f);
            }).start();
        });

        botonHistorialCitas.setOnClickListener(v -> {
            v.animate().scaleX(0.95f).scaleY(0.95f).setDuration(100).withEndAction(() -> {
                Intent intent = new Intent(pantalla_principal.this, historial_citas.class);
                startActivity(intent);
                v.setScaleX(1f);
                v.setScaleY(1f);
            }).start();
        });

        Button botonAtrasPrincipal = findViewById(R.id.botonAtrasPrincipal);
        botonAtrasPrincipal.startAnimation(animEntrada);
        botonAtrasPrincipal.setOnClickListener(view -> {
            Intent intent = new Intent(pantalla_principal.this, LoginActivity.class);
            startActivity(intent);
        });
    }
}
