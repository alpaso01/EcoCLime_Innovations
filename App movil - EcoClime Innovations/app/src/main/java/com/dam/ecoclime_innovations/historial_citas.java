package com.dam.ecoclime_innovations;

import android.content.Intent;
import android.os.Bundle;
import android.widget.Button;

import androidx.appcompat.app.AppCompatActivity;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;

public class historial_citas extends AppCompatActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        setContentView(R.layout.activity_historial_citas);
        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main), (v, insets) -> {
            Insets systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars());
            v.setPadding(systemBars.left, systemBars.top, systemBars.right, systemBars.bottom);
            return insets;
        });
        //boton de atras hacia pantalla principal

        Button botonAtrasHistorial = findViewById(R.id.botonAtrasHistorial);
        botonAtrasHistorial.setOnClickListener(view -> {
            Intent intent = new Intent(historial_citas.this , pantalla_principal.class);
            startActivity(intent);
        });
    }
}