package com.dam.ecoclime_innovations;

import android.content.Intent;
import android.content.SharedPreferences;
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
    private int userId;
    private String userNombre;
    private String userApellidos;
    private String userTelefono;
    private String userTipo;
    private String userCiudad;
    private String userCodigoPostal;
    private String userDireccion;
    private Button menuButton;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_pantalla_principal);
        
        // Obtener todos los datos del usuario del Intent
        obtenerDatosUsuario();
        
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
            pasarDatosUsuario(intent);
            startActivity(intent);
        });

        botonHistorialCitas.setOnClickListener(v -> {
            Intent intent = new Intent(pantalla_principal.this, historial_citas.class);
            pasarDatosUsuario(intent);
            startActivity(intent);
        });

        // Configurar menú desplegable
        menuButton.setOnClickListener(v -> mostrarMenu());
    }
    
    private void obtenerDatosUsuario() {
        Intent intent = getIntent();
        
        // Obtener email (obligatorio)
        userEmail = intent.getStringExtra("userEmail");
        if (userEmail == null || userEmail.isEmpty()) {
            Toast.makeText(this, "Error: Usuario no identificado", Toast.LENGTH_SHORT).show();
            startActivity(new Intent(this, LoginActivity.class));
            finish();
            return;
        }
        
        // Obtener el resto de datos
        userId = intent.getIntExtra("userId", -1);
        userNombre = intent.getStringExtra("userNombre");
        userApellidos = intent.getStringExtra("userApellidos");
        userTelefono = intent.getStringExtra("userTelefono");
        userTipo = intent.getStringExtra("userTipo");
        userCiudad = intent.getStringExtra("userCiudad");
        userCodigoPostal = intent.getStringExtra("userCodigoPostal");
        userDireccion = intent.getStringExtra("userDireccion");
        
        // Guardar todos los datos en SharedPreferences para persistencia
        guardarDatosEnSharedPreferences();
        
        Log.d(TAG, "Datos del usuario cargados - ID: " + userId + ", Email: " + userEmail + 
              ", Nombre: " + userNombre + ", Tipo: " + userTipo);
    }
    
    private void guardarDatosEnSharedPreferences() {
        SharedPreferences sharedPreferences = getSharedPreferences("UserPrefs", MODE_PRIVATE);
        SharedPreferences.Editor editor = sharedPreferences.edit();
        
        editor.putString("userEmail", userEmail);
        editor.putInt("userId", userId);
        
        if (userNombre != null) editor.putString("userNombre", userNombre);
        if (userApellidos != null) editor.putString("userApellidos", userApellidos);
        if (userTelefono != null) editor.putString("userTelefono", userTelefono);
        if (userTipo != null) editor.putString("userTipo", userTipo);
        if (userCiudad != null) editor.putString("userCiudad", userCiudad);
        if (userCodigoPostal != null) editor.putString("userCodigoPostal", userCodigoPostal);
        if (userDireccion != null) editor.putString("userDireccion", userDireccion);
        
        editor.apply();
    }
    
    private void pasarDatosUsuario(Intent intent) {
        // Pasar todos los datos a la siguiente actividad
        intent.putExtra("userEmail", userEmail);
        intent.putExtra("userId", userId);
        intent.putExtra("userNombre", userNombre);
        intent.putExtra("userApellidos", userApellidos);
        intent.putExtra("userTelefono", userTelefono);
        intent.putExtra("userTipo", userTipo);
        intent.putExtra("userCiudad", userCiudad);
        intent.putExtra("userCodigoPostal", userCodigoPostal);
        intent.putExtra("userDireccion", userDireccion);
    }

    private void mostrarMenu() {
        PopupMenu popup = new PopupMenu(this, menuButton);
        popup.getMenuInflater().inflate(R.menu.dropdown_menu, popup.getMenu());

        popup.setOnMenuItemClickListener(item -> {
            int itemId = item.getItemId();
            if (itemId == R.id.nav_atencion_cliente) {
                Intent intent = new Intent(pantalla_principal.this, AtencionClienteActivity.class);
                pasarDatosUsuario(intent);
                startActivity(intent);
                return true;
            } else if (itemId == R.id.nav_mi_perfil) {
                Intent intent = new Intent(pantalla_principal.this, MiPerfilActivity.class);
                pasarDatosUsuario(intent);
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
