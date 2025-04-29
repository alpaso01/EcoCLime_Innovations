package com.dam.ecoclime_innovations;

import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.MenuItem;
import android.view.View;
import android.widget.Button;
import android.widget.PopupMenu;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;

public class pantalla_principal extends AppCompatActivity {
    
    private static final String TAG = "pantalla_principal";
    private String userEmail;
    private Button menuButton;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_pantalla_principal);
        
        // Obtener el email del usuario
        userEmail = getIntent().getStringExtra("userEmail");
        if (userEmail == null || userEmail.isEmpty()) {
            Toast.makeText(this, "Error: Usuario no identificado", Toast.LENGTH_SHORT).show();
            startActivity(new Intent(this, LoginActivity.class));
            finish();
            return;
        }
        
        Log.d(TAG, "Email del usuario: " + userEmail);

        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main), (v, insets) -> {
            Insets systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars());
            v.setPadding(systemBars.left, systemBars.top, systemBars.right, systemBars.bottom);
            return insets;
        });

        // Inicializar vistas
        Button botonElegir = findViewById(R.id.botonElegir);

        Button botonHistorialCitas = findViewById(R.id.botonHistorialCitas);
        menuButton = findViewById(R.id.menuButton);

        // Configurar listeners de botones
        botonElegir.setOnClickListener(v -> {
            Intent intent = new Intent(pantalla_principal.this, ElegirCita.class);
            intent.putExtra("userEmail", userEmail);
            startActivity(intent);
        });


        botonHistorialCitas.setOnClickListener(v -> {
            Intent intent = new Intent(pantalla_principal.this, historial_citas.class);
            intent.putExtra("userEmail", userEmail);
            startActivity(intent);
        });

        // Configurar menú desplegable
        menuButton.setOnClickListener(v -> mostrarMenu());
    }

    private void mostrarMenu() {
        PopupMenu popup = new PopupMenu(this, menuButton);
        popup.getMenuInflater().inflate(R.menu.dropdown_menu, popup.getMenu());

        popup.setOnMenuItemClickListener(item -> {
            int itemId = item.getItemId();
            if (itemId == R.id.nav_atencion_cliente) {
                Intent intent = new Intent(pantalla_principal.this, AtencionClienteActivity.class);
                intent.putExtra("userEmail", userEmail);
                startActivity(intent);
                return true;
            } else if (itemId == R.id.nav_mi_perfil) {
                Intent intent = new Intent(pantalla_principal.this, MiPerfilActivity.class);
                intent.putExtra("userEmail", userEmail);
                startActivity(intent);
                return true;
            } else if (itemId == R.id.nav_salir) {
                startActivity(new Intent(pantalla_principal.this, LoginActivity.class));
                finish();
                return true;
            }
            return false;
        });

        popup.show();
    }
}
