package com.dam.ecoclime_innovations;

import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;

public class pantalla_principal extends AppCompatActivity {
    
    private static final String TAG = "pantalla_principal";
    private String userEmail;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_pantalla_principal);
        
        // Obtener el email del usuario
        userEmail = getIntent().getStringExtra("userEmail");
        if (userEmail == null || userEmail.isEmpty()) {
            Toast.makeText(this, "Error: Datos de usuario no válidos", Toast.LENGTH_SHORT).show();
            finish();
            return;
        }
        
        Log.d(TAG, "userEmail obtenido: " + userEmail);

        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main), (v, insets) -> {
            Insets systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars());
            v.setPadding(systemBars.left, systemBars.top, systemBars.right, systemBars.bottom);
            return insets;
        });

        // Inicializar botones
        Button botonElegir = findViewById(R.id.botonElegir);
        Button botonHistorialCitas = findViewById(R.id.botonHistorialCitas);
        Button botonAtrasPrincipal = findViewById(R.id.botonAtrasPrincipal);

        // Configurar listeners sin animaciones complejas para mejorar rendimiento
        botonElegir.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent intent = new Intent(pantalla_principal.this, ElegirCita.class);
                intent.putExtra("userEmail", userEmail);
                startActivity(intent);
            }
        });



        botonHistorialCitas.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Log.d(TAG, "Iniciando historial_citas con email: " + userEmail);
                Intent intent = new Intent(pantalla_principal.this, historial_citas.class);
                intent.putExtra("userEmail", userEmail);
                startActivity(intent);
            }
        });

        botonAtrasPrincipal.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent intent = new Intent(pantalla_principal.this, LoginActivity.class);
                startActivity(intent);
                finish();
            }
        });
    }
}
