package com.dam.ecoclime_innovations;

import android.content.Intent;
import android.os.Bundle;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import java.io.IOException;

public class CrearTrabajadorActivity extends BaseActivity {
    private EditText etNombre;
    private EditText etApellidos;
    private EditText etNumeroTrabajador;
    private EditText etTelefono;
    private EditText etPassword;
    private EditText etConfirmPassword;
    private Button btnRegistrar;
    private Button btnAtras;
    private ApiService apiService;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_crear_trabajador);

        initializeComponents();
        setupButton();
        initRetrofit();
    }

    private void initializeComponents() {
        etNombre = findViewById(R.id.etNombre);
        etApellidos = findViewById(R.id.etApellidos);
        etNumeroTrabajador = findViewById(R.id.etNumeroTrabajador);
        etTelefono = findViewById(R.id.etTelefono);
        etPassword = findViewById(R.id.etPassword);
        etConfirmPassword = findViewById(R.id.etConfirmPassword);
        btnRegistrar = findViewById(R.id.btnRegistrar);
        btnAtras = findViewById(R.id.btnAtras);
    }

    private void initRetrofit() {
        apiService = RetrofitClient.getRetrofitInstance().create(ApiService.class);
    }

    private void setupButton() {
        btnRegistrar.setOnClickListener(v -> registrarTrabajador());
        
        btnAtras.setOnClickListener(v -> {
            Intent intent = new Intent(CrearTrabajadorActivity.this, AdminActivity.class);
            startActivity(intent);
            finish();
        });
    }

    private void registrarTrabajador() {
        String nombre = etNombre.getText().toString().trim();
        String apellidos = etApellidos.getText().toString().trim();
        String numeroTrabajador = etNumeroTrabajador.getText().toString().trim();
        String telefono = etTelefono.getText().toString().trim();
        String password = etPassword.getText().toString().trim();
        String confirmPassword = etConfirmPassword.getText().toString().trim();

        // Validaciones
        if (nombre.isEmpty() || apellidos.isEmpty() || numeroTrabajador.isEmpty() ||
                telefono.isEmpty() || password.isEmpty() || confirmPassword.isEmpty()) {
            Toast.makeText(this, "Por favor, complete todos los campos", Toast.LENGTH_SHORT).show();
            return;
        }

        if (!password.equals(confirmPassword)) {
            Toast.makeText(this, "Las contraseñas no coinciden", Toast.LENGTH_SHORT).show();
            return;
        }

        // Crear el email del trabajador
        String email = "ecotrabajador_" + numeroTrabajador + "@clime.es";

        // Crear objeto Usuario
        Usuario usuario = new Usuario(nombre, apellidos, email, telefono, password, "trabajador");

        // Registrar trabajador usando el endpoint específico
        apiService.registrarTrabajador(usuario).enqueue(new Callback<Usuario>() {
            @Override
            public void onResponse(Call<Usuario> call, Response<Usuario> response) {
                if (response.isSuccessful()) {
                    Toast.makeText(CrearTrabajadorActivity.this,
                            "Trabajador registrado exitosamente", Toast.LENGTH_SHORT).show();
                    finish();
                } else {
                    String error = "Error al registrar trabajador";
                    try {
                        if (response.errorBody() != null) {
                            error = response.errorBody().string();
                        }
                    } catch (IOException e) {
                        e.printStackTrace();
                    }
                    Toast.makeText(CrearTrabajadorActivity.this, error, Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(Call<Usuario> call, Throwable t) {
                Toast.makeText(CrearTrabajadorActivity.this,
                        "Error de conexión: " + t.getMessage(), Toast.LENGTH_SHORT).show();
            }
        });
    }

    @Override
    protected int getSelectedNavigationItemId() {
        return R.id.navigation_home;
    }
}