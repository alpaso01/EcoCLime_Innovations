package com.dam.ecoclime_innovations;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
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

        // Botón para Citas Particulares
        Button botonCogerCitaParticular = findViewById(R.id.botonCogerCitaParticular);
        botonCogerCitaParticular.setOnClickListener(v -> {
            Intent intent = new Intent(pantalla_principal.this, citas_particulares.class);
            startActivity(intent);
        });

        // Botón para Citas Empresas
        Button botonCogerCitaEmpresas = findViewById(R.id.botonCogerCitaEmpresas);
        botonCogerCitaEmpresas.setOnClickListener(v -> {
            Intent intent = new Intent(pantalla_principal.this, citas_empresa.class);
            startActivity(intent);
        });

        // Botón para Anular Cita
        Button botonAnularCita = findViewById(R.id.botonAnularCita);
        botonAnularCita.setOnClickListener(v -> {
            Intent intent = new Intent(pantalla_principal.this, anular_cita.class);
            startActivity(intent);
        });

        // Botón para Historial de Citas
        Button botonHistorialCitas = findViewById(R.id.botonHistorialCitas);
        botonHistorialCitas.setOnClickListener(v -> {
            Intent intent = new Intent(pantalla_principal.this, historial_citas.class);
            startActivity(intent);
        });

        // Botón de atras para volver a login

        Button botonAtrasPrincipal = findViewById(R.id.botonAtrasPrincipal);
        botonAtrasPrincipal.setOnClickListener(view -> {
            Intent intent = new Intent(pantalla_principal.this , LoginActivity.class);
            startActivity(intent);
        });
    }
}
