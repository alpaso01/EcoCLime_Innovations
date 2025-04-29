package com.dam.ecoclime_innovations;

import android.content.Intent;
import android.content.SharedPreferences;
import android.graphics.Color;
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
        // Reiniciar la instancia por si acaso
        RetrofitClient.clearInstance();
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
        apiService.loginUser(usuario).enqueue(new Callback<String>() {
            @Override
            public void onResponse(Call<String> call, Response<String> response) {
                if (response.isSuccessful() && response.body() != null) {
                    // Extraer el ID del usuario de la respuesta (si el servidor lo devuelve)
                    String responseBody = response.body();
                    Log.d("LoginActivity", "Respuesta del servidor: " + responseBody);

                    Toast.makeText(LoginActivity.this, "Inicio de sesión exitoso", Toast.LENGTH_SHORT).show();

                    // Guardar datos mínimos necesarios en SharedPreferences
                    SharedPreferences.Editor editor = sharedPreferences.edit();
                    editor.putBoolean("isLoggedIn", true);
                    editor.apply();

                    // Iniciar la actividad principal pasando el email del usuario
                    Intent intent = new Intent(LoginActivity.this, pantalla_principal.class);
                    intent.putExtra("userEmail", email);
                    startActivity(intent);
                    finish();
                } else {
                    Toast.makeText(LoginActivity.this, "Credenciales incorrectas", Toast.LENGTH_SHORT).show();
                    Log.e("LoginError", "Código de error: " + response.code());
                    try {
                        String errorBody = response.errorBody() != null ? response.errorBody().string() : "Sin cuerpo de error";
                        Log.e("LoginError", "Mensaje de error: " + errorBody);
                    } catch (Exception e) {
                        e.printStackTrace();
                    }
                    
                    // Reiniciar Retrofit si hay errores de HTTP
                    if (response.code() >= 500) {
                        initRetrofit();
                    }
                }
            }

            @Override
            public void onFailure(Call<String> call, Throwable t) {
                Toast.makeText(LoginActivity.this, "Error de conexión: " + t.getMessage(), Toast.LENGTH_SHORT).show();
                Log.e("LoginError", "Error de conexión: " + t.getMessage());
                t.printStackTrace();
                
                // Reiniciar Retrofit en caso de error de conexión
                initRetrofit();
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

        apiService.registerUser(usuario).enqueue(new Callback<Usuario>() {
            @Override
            public void onResponse(Call<Usuario> call, Response<Usuario> response) {
                if (response.isSuccessful()) {
                    Toast.makeText(LoginActivity.this, "Registro exitoso", Toast.LENGTH_SHORT).show();
                    showLoginForm();

                    // Limpiar los campos del formulario de registro
                    ((EditText) findViewById(R.id.registerUsername)).setText("");
                    ((EditText) findViewById(R.id.registerApellidos)).setText("");
                    ((EditText) findViewById(R.id.registerEmail)).setText("");
                    ((EditText) findViewById(R.id.registerPhone)).setText("");
                    ((EditText) findViewById(R.id.registerPassword)).setText("");
                    ((EditText) findViewById(R.id.registerConfirmPassword)).setText("");
                    userTypeGroup.clearCheck();
                } else {
                    Toast.makeText(LoginActivity.this, "Error en el registro", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(Call<Usuario> call, Throwable t) {
                Toast.makeText(LoginActivity.this, "Error de conexión", Toast.LENGTH_SHORT).show();
                // Reiniciar Retrofit en caso de error
                initRetrofit();
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
