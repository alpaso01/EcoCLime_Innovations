package com.dam.ecoclime_innovations;

import android.content.Intent;
import android.os.Bundle;
import android.widget.Button;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;

public class anular_cita extends AppCompatActivity {
    private String userEmail;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        setContentView(R.layout.activity_anular_cita);

        // Obtener email del usuario desde el intent
        userEmail = getIntent().getStringExtra("userEmail");

        if (userEmail == null || userEmail.isEmpty()) {
            Toast.makeText(this, "Error: Usuario no identificado", Toast.LENGTH_SHORT).show();
            finish();
            return;
        }

        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main), (v, insets) -> {
            Insets systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars());
            v.setPadding(systemBars.left, systemBars.top, systemBars.right, systemBars.bottom);
            return insets;
        });
        //boton de atras hacia pantalla principal

        Button botonAtrasAnular = findViewById(R.id.botonAtrasAnular);
        botonAtrasAnular.setOnClickListener(view -> {
            Intent intent = new Intent(anular_cita.this, pantalla_principal.class);
            intent.putExtra("userEmail", userEmail);
            startActivity(intent);
            finish();
        });
    }
}