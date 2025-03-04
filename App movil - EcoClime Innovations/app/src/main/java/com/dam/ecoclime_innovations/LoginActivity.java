package com.dam.ecoclime_innovations;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.LinearLayout;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

public class LoginActivity extends AppCompatActivity {

    private LinearLayout loginForm, registerForm;
    private Button switchToRegister, switchToLogin, accessButton, registerButton;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login);

        // Referencias a los elementos del layout
        loginForm = findViewById(R.id.loginForm);
        registerForm = findViewById(R.id.registerForm);
        switchToRegister = findViewById(R.id.switchToRegister);
        switchToLogin = findViewById(R.id.switchToLogin);
        accessButton = findViewById(R.id.accessButton);
        registerButton = findViewById(R.id.registerButton);

        // Mostrar el formulario de login al inicio
        showLoginForm();

        // Cambiar a formulario de registro
        switchToRegister.setOnClickListener(v -> showRegisterForm());

        // Cambiar a formulario de login
        switchToLogin.setOnClickListener(v -> showLoginForm());

        // Botón de login (validación básica)
        accessButton.setOnClickListener(v -> {
            String email = ((EditText) findViewById(R.id.loginEmail)).getText().toString().trim();
            String password = ((EditText) findViewById(R.id.loginPassword)).getText().toString().trim();

            if (email.isEmpty() || password.isEmpty()) {
                Toast.makeText(LoginActivity.this, "Por favor, ingrese los datos", Toast.LENGTH_SHORT).show();
            } else {
                Intent intent = new Intent(LoginActivity.this, pantalla_principal.class);
                startActivity(intent);
                finish(); // Cierra la pantalla de login
                Toast.makeText(LoginActivity.this, "Inicio de sesión exitoso", Toast.LENGTH_SHORT).show();
            }
        });

        // Botón de registro
        registerButton.setOnClickListener(v -> {
            String username = ((EditText) findViewById(R.id.registerUsername)).getText().toString().trim();
            String email = ((EditText) findViewById(R.id.registerEmail)).getText().toString().trim();
            String phone = ((EditText) findViewById(R.id.registerPhone)).getText().toString().trim();
            String password = ((EditText) findViewById(R.id.registerPassword)).getText().toString().trim();
            String confirmPassword = ((EditText) findViewById(R.id.registerConfirmPassword)).getText().toString().trim();

            if (username.isEmpty() || email.isEmpty() || phone.isEmpty() || password.isEmpty() || confirmPassword.isEmpty()) {
                Toast.makeText(LoginActivity.this, "Por favor, complete todos los campos", Toast.LENGTH_SHORT).show();
            } else if (!password.equals(confirmPassword)) {
                Toast.makeText(LoginActivity.this, "Las contraseñas no coinciden", Toast.LENGTH_SHORT).show();
            } else {
                Toast.makeText(LoginActivity.this, "Registro exitoso", Toast.LENGTH_SHORT).show();
                showLoginForm();
            }
        });
    }

    private void showLoginForm() {
        loginForm.setVisibility(View.VISIBLE);
        registerForm.setVisibility(View.GONE);
    }

    private void showRegisterForm() {
        loginForm.setVisibility(View.GONE);
        registerForm.setVisibility(View.VISIBLE);
    }
}
