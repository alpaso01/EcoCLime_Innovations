package com.dam.ecoclime_innovations;

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

        // Mostrar formulario de login al inicio
        loginForm.setVisibility(View.VISIBLE);
        registerForm.setVisibility(View.GONE);

        // Cambiar a formulario de registro
        switchToRegister.setOnClickListener(v -> {
            loginForm.setVisibility(View.GONE);
            registerForm.setVisibility(View.VISIBLE);
        });

        // Cambiar a formulario de login
        switchToLogin.setOnClickListener(v -> {
            registerForm.setVisibility(View.GONE);
            loginForm.setVisibility(View.VISIBLE);
        });

        // Botón de login (validación básica)
        accessButton.setOnClickListener(v -> {
            String email = ((EditText) findViewById(R.id.loginEmail)).getText().toString().trim();
            String password = ((EditText) findViewById(R.id.loginPassword)).getText().toString().trim();

            if (email.isEmpty() || password.isEmpty()) {
                Toast.makeText(LoginActivity.this, "Por favor, ingrese los datos", Toast.LENGTH_SHORT).show();
            } else {
                // Si está en el formulario de login, acceder a la pantalla principal
                // Intent intent = new Intent(LoginActivity.this, PantallaPrincipal.class);
                // startActivity(intent);
                // finish(); // Cierra la pantalla de login
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

            // Validación de los campos
            if (username.isEmpty() || email.isEmpty() || phone.isEmpty() || password.isEmpty() || confirmPassword.isEmpty()) {
                Toast.makeText(LoginActivity.this, "Por favor, complete todos los campos", Toast.LENGTH_SHORT).show();
            } else if (!password.equals(confirmPassword)) {
                Toast.makeText(LoginActivity.this, "Las contraseñas no coinciden", Toast.LENGTH_SHORT).show();
            } else {
                // Aquí puedes agregar la lógica para registrar al usuario en la base de datos

                // Si el registro es exitoso, ir al formulario de login
                Toast.makeText(LoginActivity.this, "Registro exitoso. Ahora puede iniciar sesión.", Toast.LENGTH_SHORT).show();
                registerForm.setVisibility(View.GONE);
                loginForm.setVisibility(View.VISIBLE);
            }
        });
    }
}
