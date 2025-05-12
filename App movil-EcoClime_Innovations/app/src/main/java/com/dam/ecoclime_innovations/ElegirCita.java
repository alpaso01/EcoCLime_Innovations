package com.dam.ecoclime_innovations;

import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.ImageButton;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;
import androidx.cardview.widget.CardView;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;

public class ElegirCita extends AppCompatActivity {
    private static final String TAG = "ElegirCita";
    private String userEmail;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_eligir_cita);

        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main), (v, insets) -> {
            Insets systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars());
            v.setPadding(systemBars.left, systemBars.top, systemBars.right, systemBars.bottom);
            return insets;
        });

        // Obtener email del usuario desde el intent
        userEmail = getIntent().getStringExtra("userEmail");
        
        if (userEmail == null || userEmail.isEmpty()) {
            Toast.makeText(this, "Error: Usuario no identificado", Toast.LENGTH_SHORT).show();
            startActivity(new Intent(this, LoginActivity.class));
            finish();
            return;
        }
        
        Log.d(TAG, "userEmail obtenido: " + userEmail);

        // Inicializar vistas
        CardView cardElegirEmpresa = findViewById(R.id.botonElegirEmpresa);
        CardView cardElegirParticular = findViewById(R.id.botonElegirParticular);
        ImageButton botonAtras = findViewById(R.id.botonAtras);

        // Configurar listeners sin animaciones complejas para mejorar rendimiento
        cardElegirEmpresa.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Log.d(TAG, "Iniciando citas_empresa con email: " + userEmail);
                Intent intent = new Intent(ElegirCita.this, citas_empresa.class);
                intent.putExtra("userEmail", userEmail);
                startActivity(intent);
            }
        });

        cardElegirParticular.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Log.d(TAG, "Iniciando citas_particulares con email: " + userEmail);
                Intent intent = new Intent(ElegirCita.this, citas_particulares.class);
                intent.putExtra("userEmail", userEmail);
                startActivity(intent);
            }
        });

        botonAtras.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                finish(); // Simplemente volvemos a la pantalla anterior
            }
        });
    }
    
    @Override
    protected void onResume() {
        super.onResume();
        Log.d(TAG, "onResume - ElegirCita");
    }
}
