package com.dam.ecoclime_innovations;

import android.content.Intent;
import android.os.Bundle;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageButton;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;

import com.dam.ecoclime_innovations.ApiService;
import com.dam.ecoclime_innovations.Usuario;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class EditarPerfilActivity extends AppCompatActivity {
    private EditText editNombre, editApellidos, editEmail, editTelefono, editPassword;
    private Button btnGuardar;
    private ImageButton btnTogglePassword;
    private ImageButton btnBack;
    private boolean passwordVisible = false;
    private ApiService apiService;
    private int usuarioId = -1;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_editar_perfil);

        editNombre = findViewById(R.id.editNombre);
        editApellidos = findViewById(R.id.editApellidos);
        editEmail = findViewById(R.id.editEmail);
        editTelefono = findViewById(R.id.editTelefono);
        editPassword = findViewById(R.id.editPassword);
        btnTogglePassword = findViewById(R.id.btnTogglePassword);
        btnGuardar = findViewById(R.id.btnGuardar);
        btnBack = findViewById(R.id.btnBack);

        // Inicializar Retrofit usando la instancia centralizada
        apiService = RetrofitClient.getInstance().create(ApiService.class);

        // Cargar datos actuales del usuario (puedes recibirlos por Intent)
        Intent intent = getIntent();
        editNombre.setText(intent.getStringExtra("nombre"));
        editApellidos.setText(intent.getStringExtra("apellidos"));
        editEmail.setText(intent.getStringExtra("email"));
        editTelefono.setText(intent.getStringExtra("telefono"));
        editPassword.setText(intent.getStringExtra("password"));
        // Si recibes el ID del usuario por intent, úsalo aquí
        if (intent.hasExtra("id")) {
            usuarioId = intent.getIntExtra("id", -1);
        }

        btnBack.setOnClickListener(v -> {
            finish(); // Cierra esta Activity y vuelve a la anterior (MiPerfilActivity)
        });

        btnTogglePassword.setOnClickListener(v -> {
            passwordVisible = !passwordVisible;
            if (passwordVisible) {
                editPassword.setInputType(android.text.InputType.TYPE_CLASS_TEXT | android.text.InputType.TYPE_TEXT_VARIATION_VISIBLE_PASSWORD);
                btnTogglePassword.setImageResource(android.R.drawable.ic_menu_view);
            } else {
                editPassword.setInputType(android.text.InputType.TYPE_CLASS_TEXT | android.text.InputType.TYPE_TEXT_VARIATION_PASSWORD);
                btnTogglePassword.setImageResource(android.R.drawable.ic_menu_view);
            }
            // Mueve el cursor al final
            editPassword.setSelection(editPassword.getText().length());
        });

        btnGuardar.setOnClickListener(v -> {
            // Recoger datos actualizados
            String nombre = editNombre.getText().toString().trim();
            String apellidos = editApellidos.getText().toString().trim();
            String email = editEmail.getText().toString().trim();
            String telefono = editTelefono.getText().toString().trim();
            String password = editPassword.getText().toString().trim();

            // Recoger valores originales del intent
            String originalNombre = intent.getStringExtra("nombre");
            String originalApellidos = intent.getStringExtra("apellidos");
            String originalEmail = intent.getStringExtra("email");
            String originalTelefono = intent.getStringExtra("telefono");
            String originalPassword = intent.getStringExtra("password");

            // Validación básica
            if (nombre.isEmpty() || apellidos.isEmpty() || email.isEmpty() || telefono.isEmpty() || password.isEmpty()) {
                Toast.makeText(EditarPerfilActivity.this, "Por favor, complete todos los campos", Toast.LENGTH_SHORT).show();
                return;
            }

            // Comparar y construir Map solo con campos modificados
            java.util.Map<String, Object> campos = new java.util.HashMap<>();
            if (!nombre.equals(originalNombre)) campos.put("nombre", nombre);
            if (!apellidos.equals(originalApellidos)) campos.put("apellidos", apellidos);
            if (!email.equals(originalEmail)) campos.put("email", email);
            if (!telefono.equals(originalTelefono)) campos.put("telefono", telefono);
            if (!password.equals(originalPassword)) campos.put("password", password);

            if (campos.isEmpty()) {
                Toast.makeText(EditarPerfilActivity.this, "No hay cambios para guardar", Toast.LENGTH_SHORT).show();
                return;
            }

            if (usuarioId == -1) {
                Toast.makeText(EditarPerfilActivity.this, "Error: ID de usuario no válido", Toast.LENGTH_SHORT).show();
                return;
            }

            // Mostrar un diálogo de progreso
            android.app.ProgressDialog progressDialog = new android.app.ProgressDialog(EditarPerfilActivity.this);
            progressDialog.setMessage("Actualizando datos...");
            progressDialog.setCancelable(false);
            progressDialog.show();
            
            // Llamada PATCH para actualizar solo campos modificados
            apiService.actualizarUsuarioParcial(usuarioId, campos).enqueue(new retrofit2.Callback<Void>() {
                @Override
                public void onResponse(retrofit2.Call<Void> call, retrofit2.Response<Void> response) {
                    progressDialog.dismiss();
                    if (response.isSuccessful()) {
                        Toast.makeText(EditarPerfilActivity.this, "Datos actualizados correctamente", Toast.LENGTH_SHORT).show();
                        
                        // Actualizar los datos en la pantalla principal
                        android.content.Intent resultIntent = new android.content.Intent();
                        resultIntent.putExtra("nombre", nombre);
                        resultIntent.putExtra("apellidos", apellidos);
                        resultIntent.putExtra("email", email);
                        resultIntent.putExtra("telefono", telefono);
                        resultIntent.putExtra("password", password);
                        setResult(RESULT_OK, resultIntent);
                        
                        // Cerrar esta actividad y volver a la pantalla principal
                        finish();
                    } else {
                        String errorMsg = "Código: " + response.code();
                        try {
                            if (response.errorBody() != null)
                                errorMsg += ", Error: " + response.errorBody().string();
                        } catch (Exception e) {}
                        Toast.makeText(EditarPerfilActivity.this, "Error al actualizar: " + errorMsg, Toast.LENGTH_LONG).show();
                    }
                }
                @Override
                public void onFailure(retrofit2.Call<Void> call, Throwable t) {
                    progressDialog.dismiss();
                    Toast.makeText(EditarPerfilActivity.this, "Error de conexión: " + t.getMessage(), Toast.LENGTH_SHORT).show();
                }
            });
        });
    }
}
