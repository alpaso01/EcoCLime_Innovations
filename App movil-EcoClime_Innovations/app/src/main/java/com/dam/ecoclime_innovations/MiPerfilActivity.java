package com.dam.ecoclime_innovations;

import android.content.Intent;
import android.content.SharedPreferences;
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
    private int userId = -1; // Inicializar con valor inválido
    private ApiService apiService;
    
    private TextView tvId, tvNombre, tvApellidos, tvEmail, tvTelefono, tvTipo, tvCiudad, tvCodigoPostal, tvDireccion;
    private Button btnVolver;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_mi_perfil);
        
        // Obtener datos del usuario de la Intent y SharedPreferences
        obtenerDatosUsuario();
        
        // Inicializar vistas
        tvId = findViewById(R.id.tvId);
        tvNombre = findViewById(R.id.tvNombre);
        tvApellidos = findViewById(R.id.tvApellidos);
        tvEmail = findViewById(R.id.tvEmail);
        tvTelefono = findViewById(R.id.tvTelefono);
        tvTipo = findViewById(R.id.tvTipo);
        tvCiudad = findViewById(R.id.tvCiudad);
        tvCodigoPostal = findViewById(R.id.tvCodigoPostal);
        tvDireccion = findViewById(R.id.tvDireccion);
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
                if (userId > 0) {
                    intent.putExtra("userId", userId);
                }
                startActivity(intent);
                finish();
            }
        });
    }
    
    private void obtenerDatosUsuario() {
        // 1. Intentar obtener todos los datos del Intent
        userEmail = getIntent().getStringExtra("userEmail");
        userId = getIntent().getIntExtra("userId", -1);
        String userNombre = getIntent().getStringExtra("userNombre");
        String userApellidos = getIntent().getStringExtra("userApellidos");
        String userTelefono = getIntent().getStringExtra("userTelefono");
        String userTipo = getIntent().getStringExtra("userTipo");
        String userCiudad = getIntent().getStringExtra("userCiudad");
        String userCodigoPostal = getIntent().getStringExtra("userCodigoPostal");
        String userDireccion = getIntent().getStringExtra("userDireccion");
        
        // Imprimir todos los extras del intent para debug
        Bundle extras = getIntent().getExtras();
        if (extras != null) {
            for (String key : extras.keySet()) {
                Log.d(TAG, "Extra - " + key + ": " + extras.get(key));
            }
        }
        
        // 2. Si no hay suficientes datos en intent, intentar de SharedPreferences
        SharedPreferences sharedPreferences = getSharedPreferences("UserPrefs", MODE_PRIVATE);
        
        if (userEmail == null || userEmail.isEmpty()) {
            userEmail = sharedPreferences.getString("userEmail", "");
        }
        
        if (userId <= 0) {
            userId = sharedPreferences.getInt("userId", -1);
        }
        
        // Si faltan datos en el Intent, los obtenemos de SharedPreferences
        if (userNombre == null) userNombre = sharedPreferences.getString("userNombre", "");
        if (userApellidos == null) userApellidos = sharedPreferences.getString("userApellidos", "");
        if (userTelefono == null) userTelefono = sharedPreferences.getString("userTelefono", "");
        if (userTipo == null) userTipo = sharedPreferences.getString("userTipo", "");
        if (userCiudad == null) userCiudad = sharedPreferences.getString("userCiudad", "");
        if (userCodigoPostal == null) userCodigoPostal = sharedPreferences.getString("userCodigoPostal", "");
        if (userDireccion == null) userDireccion = sharedPreferences.getString("userDireccion", "");
        
        // Si tenemos todos los datos necesarios, los mostramos directamente
        if (userEmail != null && !userEmail.isEmpty() && 
            userNombre != null && !userNombre.isEmpty() && 
            userId > 0) {
            
            // Crear un objeto Usuario con los datos que tenemos
            Usuario usuario = new Usuario();
            usuario.setId(userId);
            usuario.setNombre(userNombre);
            usuario.setApellidos(userApellidos);
            usuario.setEmail(userEmail);
            usuario.setTelefono(userTelefono);
            usuario.setTipo(userTipo);
            usuario.setCiudad(userCiudad);
            usuario.setCodigoPostal(userCodigoPostal);
            usuario.setDireccion(userDireccion);
            
            // Mostrar los datos directamente
            mostrarDatosUsuario(usuario);
            return;
        }
        
        // 3. Si aún no tenemos datos suficientes, verificamos que al menos tenemos el email
        if (userEmail == null || userEmail.isEmpty()) {
            Toast.makeText(this, "No se pudo obtener el email del usuario", Toast.LENGTH_SHORT).show();
            Log.e(TAG, "Email de usuario no encontrado en Intent ni SharedPreferences");
            startActivity(new Intent(this, LoginActivity.class));
            finish();
            return;
        }
        
        // Registrar para depuración
        Log.d(TAG, "Datos obtenidos - Email: " + userEmail + ", ID: " + userId);
        
        // 4. Si solo tenemos el email o faltan datos, hacer la petición a la API
        cargarDatosUsuario();
    }
    
    private void cargarDatosUsuario() {
        // Mostrar un mensaje de carga
        Toast.makeText(this, "Cargando datos del perfil...", Toast.LENGTH_SHORT).show();
        
        // Registrar el email que estamos usando para depuración
        Log.d(TAG, "Intentando cargar datos para el email: " + userEmail);
        
        // Si tenemos un ID válido, intentamos primero por ID
        if (userId > 0) {
            Log.d(TAG, "Intentando obtener usuario por ID: " + userId);
            cargarPorId();
        } else {
            // Intentar con endpoint de email
            cargarPorEmail();
        }
    }
    
    private void cargarPorId() {
        Call<Usuario> call = apiService.obtenerUsuarioPorEmail(userEmail);
        call.enqueue(new Callback<Usuario>() {
            @Override
            public void onResponse(Call<Usuario> call, Response<Usuario> response) {
                Log.d(TAG, "Respuesta obtenerUsuario - Código: " + response.code());
                if (response.isSuccessful() && response.body() != null) {
                    Usuario usuario = response.body();
                    // Verificar que el email coincide
                    if (usuario.getEmail() != null && usuario.getEmail().equals(userEmail)) {
                        mostrarDatosUsuario(usuario);
                    } else {
                        Log.e(TAG, "El email del usuario obtenido no coincide con el esperado");
                        cargarPorEmail();
                    }
                } else {
                    Log.d(TAG, "Intento por ID fallido, probando por email...");
                    cargarPorEmail();
                }
            }

            @Override
            public void onFailure(Call<Usuario> call, Throwable t) {
                Log.e(TAG, "Error al obtener usuario por ID: " + t.getMessage());
                cargarPorEmail();
            }
        });
    }
    
    private void cargarPorEmail() {
        // Intentar con el primer endpoint (byEmail)
        Call<Usuario> call = apiService.obtenerUsuarioPorEmail(userEmail);
        
        // Mostrar la URL completa para diagnóstico
        Toast.makeText(this, "Intentando con: " + call.request().url(), Toast.LENGTH_LONG).show();
        Log.d(TAG, "URL de solicitud: " + call.request().url());
        
        call.enqueue(new Callback<Usuario>() {
            @Override
            public void onResponse(Call<Usuario> call, Response<Usuario> response) {
                Log.d(TAG, "Respuesta obtenerUsuarioPorEmail - Código: " + response.code());
                if (response.isSuccessful() && response.body() != null) {
                    mostrarDatosUsuario(response.body());
                } else {
                    // Si el primer intento falla, intentar con el endpoint alternativo
                    Log.d(TAG, "Primer intento fallido (byEmail), probando con query parameter...");
                    intentarAlternativa();
                }
            }

            @Override
            public void onFailure(Call<Usuario> call, Throwable t) {
                Log.e(TAG, "Error de conexión en primer intento: " + t.getMessage());
                Log.e(TAG, "URL de la solicitud: " + call.request().url());
                
                // Intentar con el método alternativo
                intentarAlternativa();
            }
        });
    }
    
    private void intentarAlternativa() {
        // Intentar con el segundo endpoint (query parameter)
        Call<Usuario> call = apiService.obtenerUsuarioPorEmailQuery(userEmail);
        
        // Mostrar la URL completa para diagnóstico
        Toast.makeText(this, "Intentando con: " + call.request().url(), Toast.LENGTH_LONG).show();
        Log.d(TAG, "URL de solicitud alternativa: " + call.request().url());
        
        call.enqueue(new Callback<Usuario>() {
            @Override
            public void onResponse(Call<Usuario> call, Response<Usuario> response) {
                Log.d(TAG, "Respuesta obtenerUsuarioPorEmailQuery - Código: " + response.code());
                if (response.isSuccessful() && response.body() != null) {
                    mostrarDatosUsuario(response.body());
                } else {
                    try {
                        String errorBody = response.errorBody() != null ? response.errorBody().string() : "Cuerpo de error vacío";
                        Log.e(TAG, "Error al obtener datos del usuario (segundo intento). Código: " + response.code() + 
                                ", Mensaje: " + response.message() + 
                                ", Cuerpo de error: " + errorBody);
                        
                        Toast.makeText(MiPerfilActivity.this, "Error al cargar los datos del perfil (Código: " + 
                                response.code() + ")", Toast.LENGTH_SHORT).show();
                        
                        // Mostrar información técnica relevante para diagnóstico
                        Log.d(TAG, "URL de la solicitud: " + call.request().url());
                        Log.d(TAG, "Método HTTP: " + call.request().method());
                        Log.d(TAG, "Headers: " + call.request().headers());
                        
                        // Último recurso: crear un usuario mínimo para mostrar al menos el email
                        Usuario usuarioMinimo = new Usuario();
                        usuarioMinimo.setEmail(userEmail);
                        if (userId > 0) {
                            usuarioMinimo.setId(userId);
                        }
                        mostrarDatosUsuario(usuarioMinimo);
                    } catch (Exception e) {
                        Log.e(TAG, "Error al procesar respuesta de error: " + e.getMessage());
                    }
                }
            }

            @Override
            public void onFailure(Call<Usuario> call, Throwable t) {
                Log.e(TAG, "Error de conexión en segundo intento: " + t.getMessage());
                Log.e(TAG, "URL de la solicitud: " + call.request().url());
                
                // Mostrar detalles del error al usuario
                Toast.makeText(MiPerfilActivity.this, "Error de conexión: " + t.getMessage(), Toast.LENGTH_LONG).show();
                
                // Último recurso: crear un usuario mínimo para mostrar al menos el email
                Usuario usuarioMinimo = new Usuario();
                usuarioMinimo.setEmail(userEmail);
                if (userId > 0) {
                    usuarioMinimo.setId(userId);
                }
                mostrarDatosUsuario(usuarioMinimo);
                
                // Intentar reiniciar Retrofit

                apiService = RetrofitClient.getRetrofitInstance().create(ApiService.class);
            }
        });
    }
    
    private void mostrarDatosUsuario(Usuario usuario) {
        try {
            // Registrar que obtuvimos respuesta exitosa
            Log.d(TAG, "Respuesta exitosa de la API, usuario ID: " + usuario.getId());
            
            // Guardar el ID para futuros usos si no lo teníamos
            if (userId <= 0 && usuario.getId() > 0) {
                userId = usuario.getId();
                // También guardar en SharedPreferences
                SharedPreferences sharedPreferences = getSharedPreferences("UserPrefs", MODE_PRIVATE);
                SharedPreferences.Editor editor = sharedPreferences.edit();
                editor.putInt("userId", userId);
                editor.apply();
                Log.d(TAG, "ID de usuario guardado: " + userId);
            }
            
            // Mostrar todos los datos del usuario
            tvId.setText("ID: " + (usuario.getId() > 0 ? usuario.getId() : "No disponible"));
            
            // Para el resto de campos, verificar que no sean nulos
            tvNombre.setText("Nombre: " + (usuario.getNombre() != null ? usuario.getNombre() : "No disponible"));
            tvApellidos.setText("Apellidos: " + (usuario.getApellidos() != null ? usuario.getApellidos() : "No disponible"));
            tvEmail.setText("Email: " + (usuario.getEmail() != null ? usuario.getEmail() : userEmail));
            tvTelefono.setText("Teléfono: " + (usuario.getTelefono() != null ? usuario.getTelefono() : "No disponible"));
            
            if (usuario.getTipo() != null) {
                tvTipo.setText("Tipo de cuenta: " + 
                        (usuario.getTipo().equalsIgnoreCase("particular") ? "Cliente Particular" : "Empresa"));
            } else {
                tvTipo.setText("Tipo de cuenta: No disponible");
            }
            
            // Mostrar los datos adicionales
            if (usuario.getCiudad() != null && !usuario.getCiudad().isEmpty()) {
                tvCiudad.setText("Ciudad: " + usuario.getCiudad());
            } else {
                tvCiudad.setText("Ciudad: No especificada");
            }
            
            if (usuario.getCodigoPostal() != null && !usuario.getCodigoPostal().isEmpty()) {
                tvCodigoPostal.setText("Código Postal: " + usuario.getCodigoPostal());
            } else {
                tvCodigoPostal.setText("Código Postal: No especificado");
            }
            
            if (usuario.getDireccion() != null && !usuario.getDireccion().isEmpty()) {
                tvDireccion.setText("Dirección: " + usuario.getDireccion());
            } else {
                tvDireccion.setText("Dirección: No especificada");
            }
            
            // Mostrar mensaje de éxito solo si tenemos los datos completos
            if (usuario.getNombre() != null) {
                Log.d(TAG, "Datos del usuario cargados con éxito: " + usuario.getNombre());
                Toast.makeText(MiPerfilActivity.this, "Datos del perfil cargados correctamente", Toast.LENGTH_SHORT).show();
            } else {
                Log.d(TAG, "Datos parciales del usuario mostrados");
                Toast.makeText(MiPerfilActivity.this, "Datos del perfil parcialmente disponibles", Toast.LENGTH_SHORT).show();
            }
        } catch (Exception e) {
            // Capturar cualquier excepción al procesar los datos
            Log.e(TAG, "Error al procesar datos del usuario: " + e.getMessage());
            Toast.makeText(MiPerfilActivity.this, "Error al procesar los datos del perfil", Toast.LENGTH_SHORT).show();
        }
    }
} 