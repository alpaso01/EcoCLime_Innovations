package com.dam.ecoclime_innovations;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.TextView;

import androidx.appcompat.app.AppCompatActivity;

public class AtencionClienteActivity extends AppCompatActivity {
    
    private String userEmail;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_atencion_cliente);
        
        userEmail = getIntent().getStringExtra("userEmail");
        
        TextView textContacto = findViewById(R.id.textContacto);
        textContacto.setText("Nuestro horario de atención es de 9:00 a 18:00 de lunes a viernes.\n\n" +
                "Teléfono: +34 91 123 45 67\n" +
                "Email: soporte@ecoclime.es");
        
        Button btnVolver = findViewById(R.id.btnVolver);
        btnVolver.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent intent = new Intent(AtencionClienteActivity.this, pantalla_principal.class);
                intent.putExtra("userEmail", userEmail);
                startActivity(intent);
                finish();
            }
        });
    }
} 