package com.dam.ecoclime_innovations;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.ImageButton;
import android.widget.TextView;

import androidx.appcompat.app.AppCompatActivity;

public class PoliticaPrivacidadActivity extends AppCompatActivity {
    
    private ImageButton btnVolver;
    private TextView tvPolitica;
    private String email;
    private String password;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_politica_privacidad);

        // Obtener datos del usuario
        Bundle extras = getIntent().getExtras();
        if (extras != null) {
            email = extras.getString("email");
            password = extras.getString("password");
        }

        inicializarVistas();
        configurarListeners();
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

    private void pasarDatosUsuario(Intent intent) {
        Bundle bundle = new Bundle();
        bundle.putString("email", email);
        bundle.putString("password", password);
        intent.putExtras(bundle);
    }

    private void configurarListeners() {
        btnVolver.setOnClickListener(v -> {
            Intent intent = new Intent(this, pantalla_principal.class);
            startActivity(intent);
            finish();
        });
    }

    @Override
    public void onBackPressed() {
        Intent intent = new Intent(this, pantalla_principal.class);
        startActivity(intent);
        finish();
    }
}
