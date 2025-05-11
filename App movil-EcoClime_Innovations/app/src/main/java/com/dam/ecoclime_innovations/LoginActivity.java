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
    private static final int MAX_RETRIES = 3;
    private int retryCount = 0;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        
        // Configurar la ventana para el fondo transparente
        getWindow().setStatusBarColor(android.graphics.Color.TRANSPARENT);
        getWindow().setNavigationBarColor(android.graphics.Color.TRANSPARENT);
        getWindow().getDecorView().setSystemUiVisibility(
            View.SYSTEM_UI_FLAG_LAYOUT_STABLE |
            View.SYSTEM_UI_FLAG_LAYOUT_FULLSCREEN |
            View.SYSTEM_UI_FLAG_LIGHT_STATUS_BAR
        );
        
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
            Toast.makeText(this, "Por favor, ingrese los datos", Toast.LENGTH_LONG).show();
            return;
        }

        // Verificar conectividad
        if (!RetrofitClient.isNetworkAvailable(this)) {
            Toast.makeText(this, "No hay conexión a Internet. Por favor, verifica tu conexión.", Toast.LENGTH_LONG).show();
            return;
        }

        // Crear objeto Usuario para login
        Usuario usuario = new Usuario(email, password);

        // Mostrar mensaje de conexión por más tiempo
        Toast.makeText(this, "Intentando conectar al servidor...", Toast.LENGTH_LONG).show();
        Log.d("LoginActivity", "Intentando conectar a: " + RetrofitClient.getRetrofitInstance().baseUrl());

        // Realizar la llamada a la API para hacer login
        apiService.loginUser(usuario).enqueue(new Callback<Usuario>() {
            @Override
            public void onResponse(Call<Usuario> call, Response<Usuario> response) {
                if (response.isSuccessful() && response.body() != null) {
                    retryCount = 0; // Resetear contador de reintentos
                    Usuario usuarioCompleto = response.body();
                    Log.d("LoginActivity", "Respuesta del servidor: " + usuarioCompleto.toString());

                    Toast.makeText(LoginActivity.this, "Inicio de sesión exitoso", Toast.LENGTH_LONG).show();

                    // Guardar datos mínimos necesarios en SharedPreferences
                    SharedPreferences.Editor editor = sharedPreferences.edit();
                    editor.putBoolean("isLoggedIn", true);
                    editor.putString("userEmail", email);
                    editor.apply();

                    // Obtener todos los datos del usuario después del login exitoso
                    obtenerDatosUsuarioCompletos(email);
                } else {
                    String errorMessage = "Error en el inicio de sesión";
                    try {
                        if (response.errorBody() != null) {
                            errorMessage = "Error: " + response.errorBody().string();
                        } else {
                            errorMessage = "Error: Código " + response.code();
                        }
                    } catch (IOException e) {
                        errorMessage = "Error al leer respuesta del servidor";
                    }
                    
                    // Mostrar el error por más tiempo y en el log
                    Toast.makeText(LoginActivity.this, errorMessage, Toast.LENGTH_LONG).show();
                    Log.e("LoginError", errorMessage);
                    Log.e("LoginError", "Código de error: " + response.code());
                    Log.e("LoginError", "URL: " + call.request().url());
                    Log.e("LoginError", "Headers: " + call.request().headers());
                    Log.e("LoginError", "Método: " + call.request().method());

                    // Vaciar los campos de email y contraseña
                    ((EditText) findViewById(R.id.loginEmail)).setText("");
                    ((EditText) findViewById(R.id.loginPassword)).setText("");

                    // Reiniciar Retrofit si hay errores de HTTP
                    if (response.code() >= 500) {
                        RetrofitClient.resetInstance();
                        initRetrofit();
                    }
                }
            }

            @Override
            public void onFailure(Call<Usuario> call, Throwable t) {
                String errorMessage = "Error de conexión: " + t.getMessage();
                Log.e("LoginError", errorMessage);
                Log.e("LoginError", "URL: " + call.request().url());
                Log.e("LoginError", "Causa: " + (t.getCause() != null ? t.getCause().getMessage() : "Desconocida"));
                Log.e("LoginError", "Stack trace completo:");
                t.printStackTrace();

                // Intentar reintentar la conexión
                if (retryCount < MAX_RETRIES) {
                    retryCount++;
                    Log.d("LoginActivity", "Reintentando conexión (" + retryCount + "/" + MAX_RETRIES + ")");
                    Toast.makeText(LoginActivity.this, "Reintentando conexión...", Toast.LENGTH_LONG).show();
                    
                    // Reiniciar Retrofit y volver a intentar
                    RetrofitClient.resetInstance();
                    initRetrofit();
                    loginUser();
                } else {
                    retryCount = 0;
                    String finalErrorMessage = "No se pudo conectar al servidor después de varios intentos.\n" +
                            "Por favor, verifica que:\n" +
                            "1. El servidor esté en ejecución\n" +
                            "2. Estés conectado a la misma red que el servidor\n" +
                            "3. El puerto 8085 esté abierto\n" +
                            "4. La IP del servidor sea correcta";
                    Toast.makeText(LoginActivity.this, finalErrorMessage, Toast.LENGTH_LONG).show();
                }
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
        // Cambiar el estilo del botón de login
        switchToLogin.setBackground(getResources().getDrawable(R.drawable.bg_tab_inactive));
        switchToRegister.setBackground(null);

        // Aplicar animación de entrada desde la izquierda al formulario de login
        loginForm.startAnimation(android.view.animation.AnimationUtils.loadAnimation(this, R.anim.slide_in_right));
        registerForm.startAnimation(android.view.animation.AnimationUtils.loadAnimation(this, R.anim.slide_out_left));

        loginForm.setVisibility(View.VISIBLE);
        registerForm.setVisibility(View.GONE);
    }

    private void showRegisterForm() {
        // Cambiar el estilo del botón de registro
        switchToRegister.setBackground(getResources().getDrawable(R.drawable.bg_tab_inactive));
        switchToLogin.setBackground(null);

        // Aplicar animación de entrada desde la derecha al formulario de registro
        registerForm.startAnimation(android.view.animation.AnimationUtils.loadAnimation(this, R.anim.slide_in_right));
        loginForm.startAnimation(android.view.animation.AnimationUtils.loadAnimation(this, R.anim.slide_out_left));

        registerForm.setVisibility(View.VISIBLE);
        loginForm.setVisibility(View.GONE);
    }
}
