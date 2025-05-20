package com.dam.ecoclime_innovations;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.ImageButton;
import android.widget.TextView;

import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;

public class TerminosCondicionesActivity extends BaseActivity {
    
    private ImageButton btnVolver;
    private TextView tvTerminos;
    private String email;
    private String password;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_terminos_condiciones);

        // Configurar el padding para la barra de estado
        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main), (v, insets) -> {
            Insets systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars());
            v.setPadding(systemBars.left, systemBars.top, systemBars.right, systemBars.bottom);
            return insets;
        });

        // Obtener datos del usuario
        userEmail = getIntent().getStringExtra("userEmail");
        if (userEmail == null || userEmail.isEmpty()) {
            userEmail = getIntent().getStringExtra("email");
        }
        password = getIntent().getStringExtra("password");

        inicializarVistas();
        configurarListeners();
        
        // Configurar navegación inferior
        setupBottomNavigation();
    }

    private void inicializarVistas() {
        btnVolver = findViewById(R.id.btnVolver);
        tvTerminos = findViewById(R.id.tvTerminos);

        // Aquí puedes cargar el texto de los términos y condiciones
        tvTerminos.setText("TÉRMINOS Y CONDICIONES DE USO\n\n" +
                "1. ACEPTACIÓN DE LOS TÉRMINOS\n" +
                "Al usar nuestra aplicación, aceptas estos términos y condiciones.\n\n" +
                "2. SERVICIOS\n" +
                "EcoClime Innovations proporciona servicios relacionados con la innovación y sostenibilidad.\n\n" +
                "3. USO DE LA APLICACIÓN\n" +
                "El Usuario se compromete a:\n" +
                "- Usar la aplicación de manera lícita\n" +
                "- No realizar actividades fraudulentas\n" +
                "- No dañar la aplicación\n\n" +
                "4. CUENTAS DE USUARIO\n" +
                "- El Usuario debe proporcionar información veraz\n" +
                "- El Usuario es responsable de mantener la confidencialidad de sus credenciales\n\n" +
                "5. PROPIEDAD INTELECTUAL\n" +
                "Todos los contenidos son propiedad de EcoClime Innovations.\n\n" +
                "6. LIMITACIÓN DE RESPONSABILIDAD\n" +
                "EcoClime Innovations no se hace responsable de:\n" +
                "- La disponibilidad del servicio\n" +
                "- Los errores o fallos en el servicio\n" +
                "- Los daños causados por el uso de la aplicación\n\n" +
                "7. MODIFICACIONES\n" +
                "EcoClime Innovations se reserva el derecho a modificar estos términos.\n\n" +
                "8. CONTACTO\n" +
                "Para cualquier consulta sobre los términos y condiciones, contacta con:\n" +
                "soporte@ecoclime.com");
    }

    @Override
    protected void pasarDatosUsuario(Intent intent) {
        super.pasarDatosUsuario(intent);
        if (email != null) {
            intent.putExtra("email", email);
        }
        if (password != null) {
            intent.putExtra("password", password);
        }
    }

    private void configurarListeners() {
        btnVolver.setOnClickListener(v -> onBackPressed());
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
    public void onBackPressed() {
        Intent intent = new Intent(this, pantalla_principal.class);
        pasarDatosUsuario(intent);
        startActivity(intent);
        finish();
    }
}
