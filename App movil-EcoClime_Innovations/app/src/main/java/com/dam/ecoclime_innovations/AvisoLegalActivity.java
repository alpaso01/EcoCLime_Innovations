package com.dam.ecoclime_innovations;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.ImageButton;
import android.widget.TextView;

import androidx.appcompat.app.AppCompatActivity;

public class AvisoLegalActivity extends AppCompatActivity {
    
    private ImageButton btnVolver;
    private TextView tvAviso;
    private String email;
    private String password;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_aviso_legal);

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
        pasarDatosUsuario(intent);
        startActivity(intent);
        finish();
    }
}
