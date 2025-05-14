package com.dam.ecoclime_innovations;

import android.os.Bundle;
import android.widget.TextView;

public class AtencionClienteActivity extends BaseActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_atencion_cliente);

        TextView textContacto = findViewById(R.id.textContacto);
        textContacto.setText("Nuestro horario de atención es de 9:00 a 18:00 de lunes a viernes.\n\n" +
                "Teléfono: +34 91 123 45 67\n" +
                "Email: soporte@ecoclime.es");

        setupBottomNavigation();
    }

    @Override
    protected int getSelectedNavigationItemId() {
        return R.id.navigation_home;
    }
} 