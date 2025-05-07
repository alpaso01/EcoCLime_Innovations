package com.dam.ecoclime_innovations;

import android.content.Intent;
import android.content.SharedPreferences;
import android.graphics.Color;
import android.nfc.Tag;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.RadioButton;
import android.widget.RadioGroup;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

import java.io.IOException;
import java.util.List;

public class LoginActivity extends AppCompatActivity {

    private LinearLayout loginForm, registerForm;
    private Button switchToRegister, switchToLogin, accessButton, registerButton;
    private RadioGroup userTypeGroup;
    private SharedPreferences sharedPreferences;
    private ApiService apiService;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login);

        sharedPreferences = getSharedPreferences("UserPrefs", MODE_PRIVATE);
        initRetrofit();

        loginForm = findViewById(R.id.loginForm);
        registerForm = findViewById(R.id.registerForm);
        switchToRegister = findViewById(R.id.switchToRegister);
        switchToLogin = findViewById(R.id.switchToLogin);
        accessButton = findViewById(R.id.accessButton);
        registerButton = findViewById(R.id.registerButton);
        userTypeGroup = findViewById(R.id.userTypeGroup);

        showLoginForm();

        switchToRegister.setOnClickListener(v -> showRegisterForm());
        switchToLogin.setOnClickListener(v -> showLoginForm());

        accessButton.setOnClickListener(v -> loginUser());
        registerButton.setOnClickListener(v -> registerUser());
    }

    private void initRetrofit() {
        apiService = RetrofitClient.getRetrofitInstance().create(ApiService.class);
    }

    private void loginUser() {
        String email = ((EditText) findViewById(R.id.loginEmail)).getText().toString().trim();
        String password = ((EditText) findViewById(R.id.loginPassword)).getText().toString().trim();

        if (email.isEmpty() || password.isEmpty()) {
            Toast.makeText(this, "Por favor, ingrese los datos", Toast.LENGTH_SHORT).show();
            return;
        }

        // Crear objeto Usuario para login
        Usuario usuario = new Usuario(email, password);

        Toast.makeText(this, "Intentando conectar...", Toast.LENGTH_SHORT).show();

        // Realizar la llamada a la API para hacer login
        apiService.loginUser(usuario).enqueue(new Callback<Usuario>() {
            @Override
            public void onResponse(Call<Usuario> call, Response<Usuario> response) {
                if (response.isSuccessful() && response.body() != null) {
                    Usuario usuarioCompleto = response.body();
                    Log.d("LoginActivity", "Respuesta del servidor: " + usuarioCompleto.toString());

                    Toast.makeText(LoginActivity.this, "Inicio de sesión exitoso", Toast.LENGTH_SHORT).show();

                    // Guardar datos mínimos necesarios en SharedPreferences
                    SharedPreferences.Editor editor = sharedPreferences.edit();
                    editor.putBoolean("isLoggedIn", true);
                    editor.putString("userEmail", email);
                    editor.apply();

                    // Obtener todos los datos del usuario después del login exitoso
                    obtenerDatosUsuarioCompletos(email);
                } else {
                    Toast.makeText(LoginActivity.this, "Credenciales incorrectas", Toast.LENGTH_SHORT).show();
                    Log.e("LoginError", "Código de error: " + response.code());
                    try {
                        String errorBody = response.errorBody() != null ? response.errorBody().string() : "Sin cuerpo de error";
                        Log.e("LoginError", "Mensaje de error: " + errorBody);
                    } catch (Exception e) {
                        e.printStackTrace();
                    }

                    // Vaciar los campos de email y contraseña
                    ((EditText) findViewById(R.id.loginEmail)).setText("");
                    ((EditText) findViewById(R.id.loginPassword)).setText("");

                    // Reiniciar Retrofit si hay errores de HTTP
                    if (response.code() >= 500) {
                        initRetrofit();
                    }
                }
            }

            @Override
            public void onFailure(Call<Usuario> call, Throwable t) {
                Toast.makeText(LoginActivity.this, "Error de conexión: " + t.getMessage(), Toast.LENGTH_SHORT).show();
                Log.e("LoginError", "Error de conexión: " + t.getMessage());
                t.printStackTrace();

                // Reiniciar Retrofit en caso de error de conexión
                initRetrofit();
            }
        });
    }

    // Nuevo método para obtener los datos completos del usuario
    private void obtenerDatosUsuarioCompletos(String email) {
        apiService.obtenerUsuarioPorEmail(email).enqueue(new Callback<Usuario>() {
            @Override
            public void onResponse(Call<Usuario> call, Response<Usuario> response) {
                if (response.isSuccessful() && response.body() != null) {
                    Usuario usuarioCompleto = response.body();
                    Log.d("LoginActivity", "Datos completos del usuario obtenidos: " + usuarioCompleto.getId());

                    // Guardar más datos en SharedPreferences
                    SharedPreferences.Editor editor = sharedPreferences.edit();
                    editor.putInt("userId", usuarioCompleto.getId());
                    editor.putString("userNombre", usuarioCompleto.getNombre());
                    editor.putString("userApellidos", usuarioCompleto.getApellidos());
                    editor.putString("userTelefono", usuarioCompleto.getTelefono());
                    editor.putString("userTipo", usuarioCompleto.getTipo());
                    editor.putString("userCiudad", usuarioCompleto.getCiudad());
                    editor.putString("userCodigoPostal", usuarioCompleto.getCodigoPostal());
                    editor.putString("userDireccion", usuarioCompleto.getDireccion());
                    editor.apply();

                    // Obtener las citas del usuario
                    obtenerCitasUsuario(email);

                    // Iniciar la pantalla principal con todos los datos del usuario
                    Intent intent = new Intent(LoginActivity.this, pantalla_principal.class);
                    intent.putExtra("userEmail", email);
                    intent.putExtra("userId", usuarioCompleto.getId());
                    intent.putExtra("userNombre", usuarioCompleto.getNombre());
                    intent.putExtra("userApellidos", usuarioCompleto.getApellidos());
                    intent.putExtra("userTelefono", usuarioCompleto.getTelefono());
                    intent.putExtra("userTipo", usuarioCompleto.getTipo());
                    intent.putExtra("userCiudad", usuarioCompleto.getCiudad());
                    intent.putExtra("userCodigoPostal", usuarioCompleto.getCodigoPostal());
                    intent.putExtra("userDireccion", usuarioCompleto.getDireccion());

                    startActivity(intent);
                    finish();
                } else {
                    // Si falla la obtención de datos completos, aún así continuamos pero solo con el email
                    Log.e("LoginActivity", "Error al obtener datos completos: " + response.code());
                    Intent intent = new Intent(LoginActivity.this, pantalla_principal.class);
                    intent.putExtra("userEmail", email);
                    startActivity(intent);
                    finish();
                }
            }

            @Override
            public void onFailure(Call<Usuario> call, Throwable t) {
                Log.e("LoginActivity", "Error al obtener datos completos: " + t.getMessage());
                Intent intent = new Intent(LoginActivity.this, pantalla_principal.class);
                intent.putExtra("userEmail", email);
                startActivity(intent);
                finish();
            }
        });
    }

    private void obtenerCitasUsuario(String email) {
        apiService.obtenerHistorialCitasPorEmail(email).enqueue(new Callback<List<Cita>>() {
            @Override
            public void onResponse(Call<List<Cita>> call, Response<List<Cita>> response) {
                if (response.isSuccessful() && response.body() != null) {
                    List<Cita> citas = response.body();
                    // Guardar las citas en SharedPreferences para acceso rápido
                    SharedPreferences.Editor editor = sharedPreferences.edit();
                    editor.putInt("citasCount", citas.size());
                    editor.apply();

                    Log.d("LoginActivity", "Citas obtenidas: " + citas.size());
                }
            }

            @Override
            public void onFailure(Call<List<Cita>> call, Throwable t) {
                Log.e("LoginActivity", "Error al obtener citas: " + t.getMessage());
            }
        });
    }

    private void registerUser() {
        String username = ((EditText) findViewById(R.id.registerUsername)).getText().toString().trim();
        String apellidos = ((EditText) findViewById(R.id.registerApellidos)).getText().toString().trim();
        String email = ((EditText) findViewById(R.id.registerEmail)).getText().toString().trim();
        String phone = ((EditText) findViewById(R.id.registerPhone)).getText().toString().trim();
        String password = ((EditText) findViewById(R.id.registerPassword)).getText().toString().trim();
        String confirmPassword = ((EditText) findViewById(R.id.registerConfirmPassword)).getText().toString().trim();
        int selectedTypeId = userTypeGroup.getCheckedRadioButtonId();

        if (username.isEmpty() || email.isEmpty() || phone.isEmpty() || password.isEmpty() || confirmPassword.isEmpty() || selectedTypeId == -1) {
            Toast.makeText(this, "Por favor, complete todos los campos", Toast.LENGTH_SHORT).show();
            return;
        }

        if (!password.equals(confirmPassword)) {
            Toast.makeText(this, "Las contraseñas no coinciden", Toast.LENGTH_SHORT).show();
            return;
        }

        RadioButton selectedRadioButton = findViewById(selectedTypeId);
        String userType = selectedRadioButton.getText().toString().toLowerCase();

        Usuario usuario = new Usuario(username, apellidos, email, phone, password, userType);
        Log.d("Tag",usuario.toString());
        ApiService apiService = RetrofitClient.getRetrofitInstance().create(ApiService.class);

        apiService.registerUser(usuario).enqueue(new Callback<Usuario>() {
            @Override
            public void onResponse(Call<Usuario> call, Response<Usuario> response) {
                if (response.isSuccessful()) {
                    Toast.makeText(getApplicationContext(), "Registro exitoso", Toast.LENGTH_SHORT).show();
                } else {
                    Log.e("Registro", "Código de error: " + response.code());
                    Log.e("Registro", "Mensaje: " + response.message());
                    try {
                        Log.e("Registro", "Cuerpo de error: " + response.errorBody().string());
                    } catch (IOException e) {
                        e.printStackTrace();
                    }
                    Toast.makeText(getApplicationContext(), "Error en el registro", Toast.LENGTH_SHORT).show();
                }
            }


            @Override
            public void onFailure(Call<Usuario> call, Throwable t) {
                Toast.makeText(getApplicationContext(), "Fallo de conexión", Toast.LENGTH_SHORT).show();
            }
        });
    }

    private void showLoginForm() {
        loginForm.setVisibility(View.VISIBLE);
        registerForm.setVisibility(View.GONE);

        switchToLogin.setBackgroundResource(R.drawable.bg_tab_active);
        switchToLogin.setTextColor(Color.WHITE);

        switchToRegister.setBackgroundResource(android.R.color.transparent);
        switchToRegister.setTextColor(Color.parseColor("#111F77"));
    }

    private void showRegisterForm() {
        loginForm.setVisibility(View.GONE);
        registerForm.setVisibility(View.VISIBLE);

        switchToRegister.setBackgroundResource(R.drawable.bg_tab_active);
        switchToRegister.setTextColor(Color.WHITE);

        switchToLogin.setBackgroundResource(android.R.color.transparent);
        switchToLogin.setTextColor(Color.parseColor("#111F77"));
    }
}
