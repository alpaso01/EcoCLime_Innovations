package com.dam.ecoclime_innovations;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.ImageButton;
import android.widget.TextView;

import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;

public class PoliticaPrivacidadActivity extends BaseActivity {
    
    private ImageButton btnVolver;
    private TextView tvPolitica;
    private String email;
    private String password;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_politica_privacidad);

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
        tvPolitica = findViewById(R.id.tvPolitica);

        // Aquí puedes cargar el texto de la política de privacidad
        tvPolitica.setText("POLÍTICA DE PRIVACIDAD\n\n" +
                "1. RESPONSABLE DEL TRATAMIENTO DE DATOS\n" +
                "EcoClime Innovations es el responsable del tratamiento de los datos personales del Usuario.\n\n" +
                "2. FINALIDAD DEL TRATAMIENTO\n" +
                "Los datos personales se recopilan para:\n" +
                "- Gestionar el registro y acceso al servicio\n" +
                "- Prestar el servicio solicitado\n" +
                "- Mantener la comunicación con el Usuario\n\n" +
                "3. LEGITIMACIÓN DEL TRATAMIENTO\n" +
                "El tratamiento de datos está legitimado por el consentimiento del Usuario.\n\n" +
                "4. DESTINATARIOS DE LOS DATOS\n" +
                "Los datos no se cederán a terceros salvo obligación legal.\n\n" +
                "5. DERECHOS DEL USUARIO\n" +
                "El Usuario tiene derecho a:\n" +
                "- Acceder a sus datos\n" +
                "- Rectificarlos\n" +
                "- Suprimirlos\n" +
                "- Limitar su tratamiento\n" +
                "- Oponerse al tratamiento\n" +
                "- Portabilidad de los datos\n\n" +
                "6. MEDIDAS DE SEGURIDAD\n" +
                "Se aplican medidas técnicas y organizativas para garantizar la seguridad de los datos.\n\n" +
                "7. CONTACTO\n" +
                "Para cualquier consulta sobre protección de datos, contacta con:\n" +
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
