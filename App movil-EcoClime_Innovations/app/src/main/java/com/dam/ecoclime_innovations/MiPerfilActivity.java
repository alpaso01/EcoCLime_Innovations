package com.dam.ecoclime_innovations;

import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.util.Log;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.View;
import android.widget.Button;
import android.widget.ImageButton;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class MiPerfilActivity extends AppCompatActivity {

    private static final String TAG = "MiPerfilActivity";
    private String userEmail;
    private ApiService apiService;
    private String passwordUsuario = "";

    private TextView tvNombre, tvApellidos, tvEmail, tvTelefono;
    private ImageButton btnVolver;
    private Button btnEditarPerfil; // Botón para editar perfil

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_mi_perfil);

        // Obtener el email del intent
        userEmail = getIntent().getStringExtra("userEmail");
        if (userEmail == null || userEmail.isEmpty()) {
            Toast.makeText(this, "Error: Usuario no identificado", Toast.LENGTH_SHORT).show();
            finish();
            return;
        }

        // Inicializar el servicio API
        apiService = RetrofitClient.getRetrofitInstance().create(ApiService.class);

        inicializarVistas();
        configurarListeners();
        cargarDatosUsuario();
    }

    private void inicializarVistas() {
        tvNombre = findViewById(R.id.tvNombre);
        tvApellidos = findViewById(R.id.tvApellidos);
        tvEmail = findViewById(R.id.tvEmail);
        tvTelefono = findViewById(R.id.tvTelefono);
        btnEditarPerfil = findViewById(R.id.btnEditarPerfil);
        btnVolver = findViewById(R.id.btnVolver);

        // Validar que todos los TextViews se hayan inicializado correctamente
        if (tvNombre == null || tvApellidos == null || tvEmail == null || tvTelefono == null) {
            Toast.makeText(this, "Error: Algunos campos del perfil no se han inicializado correctamente", Toast.LENGTH_SHORT).show();
            finish();
            return;
        }

        // Ya inicializado en onCreate
    }

    private void configurarListeners() {
        // Listener para el botón de editar perfil
        if (btnEditarPerfil != null) {
            btnEditarPerfil.setOnClickListener(v -> {
                Intent intent = new Intent(MiPerfilActivity.this, EditarPerfilActivity.class);
                intent.putExtra("nombre", tvNombre.getText().toString());
                intent.putExtra("apellidos", tvApellidos.getText().toString());
                intent.putExtra("email", tvEmail.getText().toString());
                intent.putExtra("telefono", tvTelefono.getText().toString());
                intent.putExtra("password", passwordUsuario);
                startActivity(intent);
            });
        }
        btnVolver.setOnClickListener(v -> finish());
        
        // Configurar los botones de navegación personalizados
        View navHome = findViewById(R.id.nav_home);
        View navWeb = findViewById(R.id.nav_web);
        View navAccount = findViewById(R.id.nav_account);
        
        // Botón Inicio
        navHome.setOnClickListener(v -> {
            Intent intent = new Intent(this, pantalla_principal.class);
            intent.putExtra("userEmail", userEmail);
            startActivity(intent);
            finish();
        });
        
        // Botón Web
        navWeb.setOnClickListener(v -> {
            Intent intent = new Intent(this, VistaWebActivity.class);
            intent.putExtra("userEmail", userEmail);
            startActivity(intent);
        });
        
        // Botón Cuenta (ya estamos en la cuenta)
        navAccount.setOnClickListener(v -> {
            // Ya estamos en la cuenta, no hacemos nada
        });
    }

    private void cargarDatosUsuario() {
        apiService.obtenerUsuarioPorEmail(userEmail).enqueue(new Callback<Usuario>() {
            @Override
            public void onResponse(Call<Usuario> call, Response<Usuario> response) {
                if (response.isSuccessful() && response.body() != null) {
                    Usuario usuario = response.body();
                    mostrarDatosUsuario(usuario);
                } else {
                    Log.e(TAG, "Error al obtener datos: " + response.code());
                    Toast.makeText(MiPerfilActivity.this, "Error al cargar los datos", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(Call<Usuario> call, Throwable t) {
                Log.e(TAG, "Error de conexión: " + t.getMessage());
                Toast.makeText(MiPerfilActivity.this, "Error de conexión", Toast.LENGTH_SHORT).show();
            }
        });
    }

    private void mostrarDatosUsuario(Usuario usuario) {
        if (usuario == null) {
            Toast.makeText(this, "Error: Usuario nulo", Toast.LENGTH_SHORT).show();
            return;
        }

        tvNombre.setText(usuario.getNombre());
        tvApellidos.setText(usuario.getApellidos());
        tvEmail.setText(usuario.getEmail());
        tvTelefono.setText(usuario.getTelefono());
        passwordUsuario = usuario.getPassword();
    }
}