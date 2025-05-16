package com.dam.ecoclime_innovations;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.ImageButton;
import android.widget.TextView;

import androidx.appcompat.app.AppCompatActivity;

public class TerminosCondicionesActivity extends AppCompatActivity {
    
    private ImageButton btnVolver;
    private TextView tvTerminos;
    private String email;
    private String password;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_terminos_condiciones);

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

    private void pasarDatosUsuario(Intent intent) {
        Bundle bundle = new Bundle();
        bundle.putString("email", email);
        bundle.putString("password", password);
        intent.putExtras(bundle);
    }

    private void configurarListeners() {
        btnVolver.setOnClickListener(v -> {
            Intent intent = new Intent(this, pantalla_principal.class);
            pasarDatosUsuario(intent);
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
