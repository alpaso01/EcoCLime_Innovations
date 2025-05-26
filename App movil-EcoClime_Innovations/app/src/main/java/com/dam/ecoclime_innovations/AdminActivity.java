package com.dam.ecoclime_innovations;

import android.content.Intent;
import android.os.Bundle;
import android.widget.Button;
import android.widget.TextView;

public class AdminActivity extends BaseActivity {
    private TextView welcomeText;
    private Button btnCrearTrabajador;
    private Button btnCrearAdmin;
    private Button btnHistorialUsuarios;
    private Button btnAtras;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_admin);

        initializeComponents();
        setupButtons();
    }

    private void initializeComponents() {
        welcomeText = findViewById(R.id.welcomeText);
        btnCrearTrabajador = findViewById(R.id.btnCrearTrabajador);
        btnCrearAdmin = findViewById(R.id.btnCrearAdmin);
        btnHistorialUsuarios = findViewById(R.id.btnHistorialUsuarios);
        btnAtras = findViewById(R.id.btnAtras);

        // Configurar texto de bienvenida
        // Usar el userEmail de la clase base que ya se inicializó en onCreate de BaseActivity
        if (userEmail != null && !userEmail.isEmpty()) {
            welcomeText.setText("Bienvenid@ Administrador");
        } else {
            welcomeText.setText("Bienvenid@ Administrador");
        }
    }

    private void setupButtons() {
        btnCrearTrabajador.setOnClickListener(v -> {
            Intent intent = new Intent(AdminActivity.this, CrearTrabajadorActivity.class);
            pasarDatosUsuario(intent); // Usar el método de BaseActivity para pasar datos
            startActivity(intent);
        });

        btnCrearAdmin.setOnClickListener(v -> {
            Intent intent = new Intent(AdminActivity.this, CrearAdminActivity.class);
            pasarDatosUsuario(intent); // Usar el método de BaseActivity para pasar datos
            startActivity(intent);
        });

        btnHistorialUsuarios.setOnClickListener(v -> {
            Intent intent = new Intent(AdminActivity.this, HistorialUsuariosActivity.class);
            pasarDatosUsuario(intent); // Usar el método de BaseActivity para pasar datos
            startActivity(intent);
        });
        
        btnAtras.setOnClickListener(v -> {
            Intent intent = new Intent(AdminActivity.this, LoginActivity.class);
            intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_NEW_TASK);
            startActivity(intent);
            finish();
        });
    }

    @Override
    protected int getSelectedNavigationItemId() {
        return R.id.navigation_home;
    }
} 