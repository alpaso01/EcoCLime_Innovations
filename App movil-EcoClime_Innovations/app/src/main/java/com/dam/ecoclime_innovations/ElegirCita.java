package com.dam.ecoclime_innovations;

import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.ImageButton;
import android.widget.Toast;
import androidx.cardview.widget.CardView;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;

public class ElegirCita extends BaseActivity implements View.OnClickListener {
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

        // Configurar listeners
        cardElegirEmpresa.setOnClickListener(v -> {
            Intent intent = new Intent(ElegirCita.this, citas_empresa.class);
            pasarDatosUsuario(intent);
            startActivity(intent);
        });

        cardElegirParticular.setOnClickListener(v -> {
            Intent intent = new Intent(ElegirCita.this, citas_particulares.class);
            pasarDatosUsuario(intent);
            startActivity(intent);
        });

        // Configurar navegación inferior
        setupBottomNavigation();
        
        // Configurar botón de atrás
        ImageButton botonAtras = findViewById(R.id.botonAtras);
        botonAtras.setOnClickListener(v -> onBackPressed());
    }

    @Override
    protected void setupBottomNavigation() {
        // Configurar los botones de navegación personalizados
        View navHome = findViewById(R.id.nav_home);
        View navWeb = findViewById(R.id.nav_web);
        View navAccount = findViewById(R.id.nav_account);
        
        // Botón Inicio
        navHome.setOnClickListener(v -> {
            Intent intent = new Intent(this, pantalla_principal.class);
            pasarDatosUsuario(intent);
            startActivity(intent);
            finish();
        });
        
        // Botón Web
        navWeb.setOnClickListener(v -> {
            Intent intent = new Intent(this, VistaWebActivity.class);
            pasarDatosUsuario(intent);
            startActivity(intent);
        });
        
        // Botón Cuenta
        navAccount.setOnClickListener(v -> {
            Intent intent = new Intent(this, MiPerfilActivity.class);
            pasarDatosUsuario(intent);
            startActivity(intent);
        });
    }

    @Override
    protected int getSelectedNavigationItemId() {
        // No se selecciona ningún ítem ya que no estamos en la pantalla principal
        return -1;
    }
    
    @Override
    public void onClick(View v) {
        // Manejar clics en las vistas si es necesario
        // Actualmente el manejo de clics se hace con lambdas en los listeners
    }
}