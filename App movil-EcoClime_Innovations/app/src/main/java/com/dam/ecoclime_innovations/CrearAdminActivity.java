package com.dam.ecoclime_innovations;

import android.os.Bundle;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import java.io.IOException;

public class CrearAdminActivity extends BaseActivity {
    private EditText etNombre;
    private EditText etApellidos;
    private EditText etNumeroAdmin;
    private EditText etTelefono;
    private EditText etPassword;
    private EditText etConfirmPassword;
    private Button btnRegistrar;
    private ApiService apiService;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_crear_admin);

        initializeComponents();
        setupButton();
        initRetrofit();
    }

    private void initializeComponents() {
        etNombre = findViewById(R.id.etNombre);
        etApellidos = findViewById(R.id.etApellidos);
        etNumeroAdmin = findViewById(R.id.etNumeroAdmin);
        etTelefono = findViewById(R.id.etTelefono);
        etPassword = findViewById(R.id.etPassword);
        etConfirmPassword = findViewById(R.id.etConfirmPassword);
        btnRegistrar = findViewById(R.id.btnRegistrar);
    }

    private void initRetrofit() {
        apiService = RetrofitClient.getRetrofitInstance().create(ApiService.class);
    }

    private void setupButton() {
        btnRegistrar.setOnClickListener(v -> registrarAdmin());
    }

    private void registrarAdmin() {
        String nombre = etNombre.getText().toString().trim();
        String apellidos = etApellidos.getText().toString().trim();
        String numeroAdmin = etNumeroAdmin.getText().toString().trim();
        String telefono = etTelefono.getText().toString().trim();
        String password = etPassword.getText().toString().trim();
        String confirmPassword = etConfirmPassword.getText().toString().trim();

        // Validaciones
        if (nombre.isEmpty() || apellidos.isEmpty() || numeroAdmin.isEmpty() ||
                telefono.isEmpty() || password.isEmpty() || confirmPassword.isEmpty()) {
            Toast.makeText(this, "Por favor, complete todos los campos", Toast.LENGTH_SHORT).show();
            return;
        }

        if (!password.equals(confirmPassword)) {
            Toast.makeText(this, "Las contraseñas no coinciden", Toast.LENGTH_SHORT).show();
            return;
        }

        // Crear el email del administrador
        String email = "ecoadmin_" + numeroAdmin + "@clime.es";

        // Crear objeto Usuario
        Usuario usuario = new Usuario(nombre, apellidos, email, telefono, password, "admin");

        // Registrar administrador usando el endpoint específico
        apiService.registrarAdmin(usuario).enqueue(new Callback<Usuario>() {
            @Override
            public void onResponse(Call<Usuario> call, Response<Usuario> response) {
                if (response.isSuccessful()) {
                    Toast.makeText(CrearAdminActivity.this,
                            "Administrador registrado exitosamente", Toast.LENGTH_SHORT).show();
                    finish();
                } else {
                    String error = "Error al registrar administrador";
                    try {
                        if (response.errorBody() != null) {
                            error = response.errorBody().string();
                        }
                    } catch (IOException e) {
                        e.printStackTrace();
                    }
                    Toast.makeText(CrearAdminActivity.this, error, Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(Call<Usuario> call, Throwable t) {
                Toast.makeText(CrearAdminActivity.this,
                        "Error de conexión: " + t.getMessage(), Toast.LENGTH_SHORT).show();
            }
        });
    }

    @Override
    protected int getSelectedNavigationItemId() {
        return R.id.navigation_home;
    }
}