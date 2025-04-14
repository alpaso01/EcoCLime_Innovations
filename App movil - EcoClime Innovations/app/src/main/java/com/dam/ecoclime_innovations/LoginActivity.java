package com.dam.ecoclime_innovations;

import android.content.Intent;
import android.content.SharedPreferences;
import android.graphics.Color;
import android.os.Bundle;
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
        apiService = RetrofitClient.getClient().create(ApiService.class);

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

    private void loginUser() {
        String email = ((EditText) findViewById(R.id.loginEmail)).getText().toString().trim();
        String password = ((EditText) findViewById(R.id.loginPassword)).getText().toString().trim();

        if (email.isEmpty() || password.isEmpty()) {
            Toast.makeText(this, "Por favor, ingrese los datos", Toast.LENGTH_SHORT).show();
            return;
        }

        LoginRequest loginRequest = new LoginRequest(email, password);

        apiService.loginUser(loginRequest).enqueue(new Callback<String>() {
            @Override
            public void onResponse(Call<String> call, Response<String> response) {
                if (response.isSuccessful()) {
                    String responseMessage = response.body();
                    if (responseMessage != null && responseMessage.equals("Inicio de sesión exitoso")) {
                        Toast.makeText(LoginActivity.this, "Inicio de sesión exitoso", Toast.LENGTH_SHORT).show();

                        // Guardar datos del usuario en SharedPreferences
                        SharedPreferences.Editor editor = sharedPreferences.edit();
                        editor.putString("email", email);
                        editor.putBoolean("isLoggedIn", true);
                        editor.apply();

                        startActivity(new Intent(LoginActivity.this, pantalla_principal.class));
                        finish();
                    } else {
                        Toast.makeText(LoginActivity.this, "Credenciales incorrectas", Toast.LENGTH_SHORT).show();
                    }
                } else {
                    Toast.makeText(LoginActivity.this, "Error en la API", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(Call<String> call, Throwable t) {
                Toast.makeText(LoginActivity.this, "Error de conexión: " + t.getMessage(), Toast.LENGTH_SHORT).show();
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

        String passwordEncriptada = Util.encriptarMD5(password);

        RadioButton selectedRadioButton = findViewById(selectedTypeId);
        String userType = selectedRadioButton.getText().toString();

        Usuario usuario = new Usuario(username, apellidos, email, phone, passwordEncriptada, userType);

        apiService.registerUser(usuario).enqueue(new Callback<Usuario>() {
            @Override
            public void onResponse(Call<Usuario> call, Response<Usuario> response) {
                if (response.isSuccessful()) {
                    Toast.makeText(LoginActivity.this, "Registro exitoso", Toast.LENGTH_SHORT).show();

                    // Guardar en SharedPreferences
                    SharedPreferences.Editor editor = sharedPreferences.edit();
                    editor.putString("username", username);
                    editor.putString("apellidos", apellidos);
                    editor.putString("email", email);
                    editor.putString("phone", phone);
                    editor.putString("userType", userType);
                    editor.putBoolean("isLoggedIn", true);
                    editor.apply();

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
