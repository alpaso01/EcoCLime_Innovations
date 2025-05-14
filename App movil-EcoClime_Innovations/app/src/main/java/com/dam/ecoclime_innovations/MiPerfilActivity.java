package com.dam.ecoclime_innovations;

import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.util.Log;
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

    private TextView tvNombre, tvApellidos, tvEmail, tvTelefono;
    private TextView tvTipo, tvCiudad, tvCodigoPostal, tvDireccion;
    private ImageButton btnVolver;

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

        inicializarVistas();
        configurarListeners();
        cargarDatosUsuario();
    }

    private void inicializarVistas() {
        tvNombre = findViewById(R.id.tvNombre);
        tvApellidos = findViewById(R.id.tvApellidos);
        tvEmail = findViewById(R.id.tvEmail);
        tvTelefono = findViewById(R.id.tvTelefono);
        tvTipo = findViewById(R.id.tvTipo);
        tvCiudad = findViewById(R.id.tvCiudad);
        tvCodigoPostal = findViewById(R.id.tvCodigoPostal);
        tvDireccion = findViewById(R.id.tvDireccion);
        btnVolver = findViewById(R.id.btnVolver);

        apiService = RetrofitClient.getRetrofitInstance().create(ApiService.class);
    }

    private void configurarListeners() {
        btnVolver.setOnClickListener(v -> finish());
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
        tvNombre.setText(usuario.getNombre());
        tvApellidos.setText(usuario.getApellidos());
        tvEmail.setText(usuario.getEmail());
        tvTelefono.setText(usuario.getTelefono());
        tvTipo.setText(usuario.getTipo());
        tvCiudad.setText(usuario.getCiudad());
        tvCodigoPostal.setText(usuario.getCodigoPostal());
        tvDireccion.setText(usuario.getDireccion());
    }
}