package com.dam.ecoclime_innovations;

import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.util.Log;
import android.view.MenuItem;
import android.view.View;
import android.widget.ImageButton;
import android.widget.PopupMenu;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.cardview.widget.CardView;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;

import com.google.android.material.bottomnavigation.BottomNavigationView;

public class pantalla_principal extends BaseActivity {

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
        CardView cardElegirCita = findViewById(R.id.cardElegirCita);
        CardView cardHistorialCitas = findViewById(R.id.cardHistorialCitas);

        // Configurar el botón de inicio



        // Configurar listeners de tarjetas
        cardElegirCita.setOnClickListener(v -> {
            Intent intent = new Intent(pantalla_principal.this, ElegirCita.class);
            pasarDatosUsuario(intent);
            startActivity(intent);
        });

        cardHistorialCitas.setOnClickListener(v -> {
            Intent intent = new Intent(pantalla_principal.this, historial_citas.class);
            pasarDatosUsuario(intent);
            startActivity(intent);
        });

        // Configurar el botón de menú
        ImageButton menuButton = findViewById(R.id.menuButton);
        menuButton.setOnClickListener(v -> showPopupMenu(v));

        // Configurar navegación inferior
        setupBottomNavigation();
    }

    @Override
    protected void setupBottomNavigation() {
        // Configurar los botones de navegación personalizados
        View navHome = findViewById(R.id.nav_home);
        View navWeb = findViewById(R.id.nav_web);
        View navAccount = findViewById(R.id.nav_account);

        // Botón Inicio (ya estamos en la pantalla principal)
        navHome.setOnClickListener(v -> {
            // Ya estamos en home, no hacemos nada
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
        // Como ya no usamos BottomNavigationView, devolvemos un valor por defecto
        // Este método es requerido por la clase base pero ya no lo necesitamos
        return 0;
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

    @Override
    protected void pasarDatosUsuario(Intent intent) {
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

    private void showPopupMenu(View view) {
        PopupMenu popup = new PopupMenu(this, view);
        popup.getMenuInflater().inflate(R.menu.menu_principal, popup.getMenu());
        popup.setOnMenuItemClickListener(item -> {
            int itemId = item.getItemId();
            if (itemId == R.id.menu_ayuda) {
                // Redirigir a Atención al Cliente
                Intent intent = new Intent(this, AtencionClienteActivity.class);
                pasarDatosUsuario(intent);
                startActivity(intent);
                return true;
            } else if (itemId == R.id.menu_politica_privacidad) {
                // Redirigir a Política de Privacidad
                Intent intent = new Intent(this, PoliticaPrivacidadActivity.class);
                pasarDatosUsuario(intent);
                startActivity(intent);
                return true;
            } else if (itemId == R.id.menu_aviso_legal) {
                // Redirigir a Aviso Legal
                Intent intent = new Intent(this, AvisoLegalActivity.class);
                pasarDatosUsuario(intent);
                startActivity(intent);
                return true;
            } else if (itemId == R.id.menu_terminos_condiciones) {
                // Redirigir a Términos y Condiciones
                Intent intent = new Intent(this, TerminosCondicionesActivity.class);
                pasarDatosUsuario(intent);
                startActivity(intent);
                return true;
            } else if (itemId == R.id.menu_salir) {
                // Redirigir a Login
                Intent intent = new Intent(this, LoginActivity.class);
                intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                startActivity(intent);
                finish();
                return true;
            }
            return false;
        });
        popup.show();
    }
}