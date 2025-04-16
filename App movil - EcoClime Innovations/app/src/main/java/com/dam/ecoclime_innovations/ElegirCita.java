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

public class ElegirCita extends AppCompatActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_eligir_cita);

        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main), (v, insets) -> {
            Insets systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars());
            v.setPadding(systemBars.left, systemBars.top, systemBars.right, systemBars.bottom);
            return insets;
        });

        // Cargar animación
        Animation animEntrada = AnimationUtils.loadAnimation(this, R.anim.anim_boton_entrada);

        // Botones
        Button botonElegirEmpresa = findViewById(R.id.botonElegirEmpresa);
        Button botonElegirParticular = findViewById(R.id.botonElegirParticular);
        Button botonAtras = findViewById(R.id.botonAtras);

        // Aplicar animación de entrada
        botonElegirEmpresa.startAnimation(animEntrada);
        botonElegirParticular.startAnimation(animEntrada);
        botonAtras.startAnimation(animEntrada);

        // Listeners
        botonElegirEmpresa.setOnClickListener(v -> {
            v.animate().scaleX(0.95f).scaleY(0.95f).setDuration(100).withEndAction(() -> {
                Intent intent = new Intent(ElegirCita.this, citas_empresa.class);
                startActivity(intent);
                v.setScaleX(1f);
                v.setScaleY(1f);
            }).start();
        });

        botonElegirParticular.setOnClickListener(v -> {
            v.animate().scaleX(0.95f).scaleY(0.95f).setDuration(100).withEndAction(() -> {
                Intent intent = new Intent(ElegirCita.this, citas_particulares.class);
                startActivity(intent);
                v.setScaleX(1f);
                v.setScaleY(1f);
            }).start();
        });

        botonAtras.setOnClickListener(v -> {
            Intent intent = new Intent(ElegirCita.this, pantalla_principal.class);
            startActivity(intent);
        });
    }
}
