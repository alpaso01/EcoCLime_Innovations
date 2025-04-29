package com.dam.ecoclime_innovations;

import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
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
    
    private TextView tvNombre, tvApellidos, tvEmail, tvTelefono, tvTipo;
    private Button btnVolver;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_mi_perfil);
        
        // Obtener email del usuario
        userEmail = getIntent().getStringExtra("userEmail");
        if (userEmail == null || userEmail.isEmpty()) {
            Toast.makeText(this, "No se pudo obtener el email del usuario", Toast.LENGTH_SHORT).show();
            finish();
            return;
        }
        
        // Inicializar vistas
        tvNombre = findViewById(R.id.tvNombre);
        tvApellidos = findViewById(R.id.tvApellidos);
        tvEmail = findViewById(R.id.tvEmail);
        tvTelefono = findViewById(R.id.tvTelefono);
        tvTipo = findViewById(R.id.tvTipo);
        btnVolver = findViewById(R.id.btnVolver);
        
        // Inicializar Retrofit
        apiService = RetrofitClient.getRetrofitInstance().create(ApiService.class);
        
        // Cargar datos del usuario
        cargarDatosUsuario();
        
        // Configurar botón volver
        btnVolver.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent intent = new Intent(MiPerfilActivity.this, pantalla_principal.class);
                intent.putExtra("userEmail", userEmail);
                startActivity(intent);
                finish();
            }
        });
    }
    
    private void cargarDatosUsuario() {
        Call<Usuario> call = apiService.obtenerUsuarioPorEmail(userEmail);
        call.enqueue(new Callback<Usuario>() {
            @Override
            public void onResponse(Call<Usuario> call, Response<Usuario> response) {
                if (response.isSuccessful() && response.body() != null) {
                    Usuario usuario = response.body();
                    // Mostrar datos del usuario
                    tvNombre.setText("Nombre: " + usuario.getNombre());
                    tvApellidos.setText("Apellidos: " + usuario.getApellidos());
                    tvEmail.setText("Email: " + usuario.getEmail());
                    tvTelefono.setText("Teléfono: " + usuario.getTelefono());
                    tvTipo.setText("Tipo de cuenta: " + 
                            (usuario.getTipo().equalsIgnoreCase("particular") ? "Cliente Particular" : "Empresa"));
                } else {
                    Log.e(TAG, "Error al obtener datos del usuario: " + response.code());
                    Toast.makeText(MiPerfilActivity.this, "Error al cargar los datos del perfil", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(Call<Usuario> call, Throwable t) {
                Log.e(TAG, "Error de conexión: " + t.getMessage());
                Toast.makeText(MiPerfilActivity.this, "Error de conexión al servidor", Toast.LENGTH_SHORT).show();
            }
        });
    }
} 