package com.dam.ecoclime_innovations;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.TextView;

import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;

public class AtencionClienteActivity extends BaseActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_atencion_cliente);

        // Configurar el padding para la barra de estado
        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main), (v, insets) -> {
            Insets systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars());
            v.setPadding(systemBars.left, systemBars.top, systemBars.right, systemBars.bottom);
            return insets;
        });

        // Configurar el texto de contacto
        TextView textContacto = findViewById(R.id.textContacto);
        textContacto.setText("Nuestro horario de atención es de 9:00 a 18:00 de lunes a viernes.\n\n" +
                "Teléfono: +34 91 123 45 67\n" +
                "Email: soporte@ecoclime.es");

        // Configurar el botón de volver
        findViewById(R.id.btnVolver).setOnClickListener(v -> onBackPressed());

        // Configurar navegación inferior
        setupBottomNavigation();
    }

    @Override
    protected int getSelectedNavigationItemId() {
        // No se selecciona ningún ítem ya que no estamos en la pantalla principal
        return -1;
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
}