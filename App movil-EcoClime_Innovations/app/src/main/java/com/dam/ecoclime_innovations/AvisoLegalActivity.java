package com.dam.ecoclime_innovations;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.ImageButton;
import android.widget.TextView;

import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;

public class AvisoLegalActivity extends BaseActivity {
    
    private ImageButton btnVolver;
    private TextView tvAviso;
    private String email;
    private String password;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_aviso_legal);

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
        tvAviso = findViewById(R.id.tvAviso);

        // Aquí puedes cargar el texto del aviso legal
        tvAviso.setText("AVISO LEGAL\n\n" +
                "1. IDENTIFICACIÓN\n" +
                "EcoClime Innovations\n" +
                "CIF: B12345678\n" +
                "Domicilio: Calle Ejemplo, 123\n" +
                "Email: info@ecoclime.com\n\n" +
                "2. OBJETO\n" +
                "Este sitio web proporciona servicios relacionados con la innovación y sostenibilidad.\n\n" +
                "3. USO DEL SITIO WEB\n" +
                "El acceso y uso del sitio web es responsabilidad del Usuario.\n\n" +
                "4. PROPIEDAD INTELECTUAL\n" +
                "Todos los contenidos del sitio web son propiedad de EcoClime Innovations.\n\n" +
                "5. EXCLUSIÓN DE GARANTÍAS Y RESPONSABILIDAD\n" +
                "EcoClime Innovations no se hace responsable de:\n" +
                "- La disponibilidad y continuidad del funcionamiento del sitio web\n" +
                "- La ausencia de errores en el contenido\n" +
                "- Los daños causados por el uso del sitio web\n\n" +
                "6. MODIFICACIONES\n" +
                "EcoClime Innovations se reserva el derecho a modificar el contenido del sitio web.\n\n" +
                "7. LEY APLICABLE Y JURISDICCIÓN\n" +
                "Las presentes condiciones se regirán por la legislación española.\n\n" +
                "8. CONTACTO\n" +
                "Para cualquier consulta, contacta con:\n" +
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
