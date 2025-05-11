package com.dam.ecoclime_innovations;

import android.content.SharedPreferences;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.dam.ecoclime_innovations.ApiService;
import com.dam.ecoclime_innovations.RetrofitClient;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class perfil extends AppCompatActivity {
    private static final String TAG = "perfil";
    private TextView tvNombre, tvApellidos, tvEmail, tvTelefono, tvTipo;
    private ApiService apiService;
    private String userEmail;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_perfil);

        // Inicializar vistas
        inicializarVistas();
        
        // Inicializar Retrofit
        apiService = RetrofitClient.getRetrofitInstance().create(ApiService.class);

        // Obtener el email del usuario
        SharedPreferences sharedPreferences = getSharedPreferences("UserPrefs", MODE_PRIVATE);
        userEmail = sharedPreferences.getString("userEmail", "");

        if (userEmail.isEmpty()) {
            Toast.makeText(this, "Error: No se encontró el email del usuario", Toast.LENGTH_SHORT).show();
            finish();
            return;
        }

        // Cargar datos del usuario
        cargarDatosUsuario();

        // Configurar botón volver
        Button btnVolver = findViewById(R.id.botonAtrasPerfil);
        btnVolver.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                finish();
            }
        });
    }

    private void inicializarVistas() {
        tvNombre = findViewById(R.id.tvNombre);
        tvApellidos = findViewById(R.id.tvApellidos);
        tvEmail = findViewById(R.id.tvEmail);
        tvTelefono = findViewById(R.id.tvTelefono);
        tvTipo = findViewById(R.id.tvTipo);
    }

    private void cargarDatosUsuario() {
        apiService.obtenerUsuarioPorEmail(userEmail).enqueue(new Callback<Usuario>() {
            @Override
            public void onResponse(Call<Usuario> call, Response<Usuario> response) {
                if (response.isSuccessful() && response.body() != null) {
                    Usuario usuario = response.body();
                    mostrarDatosUsuario(usuario);
                } else {
                    Toast.makeText(perfil.this, "Error al cargar datos del usuario", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(Call<Usuario> call, Throwable t) {
                Toast.makeText(perfil.this, "Error de conexión", Toast.LENGTH_SHORT).show();
            }
        });
    }

    private void mostrarDatosUsuario(Usuario usuario) {
        tvNombre.setText("Nombre: " + usuario.getNombre());
        tvApellidos.setText("Apellidos: " + usuario.getApellidos());
        tvEmail.setText("Email: " + usuario.getEmail());
        tvTelefono.setText("Teléfono: " + usuario.getTelefono());
        tvTipo.setText("Tipo: " + usuario.getTipo());
    }
} 