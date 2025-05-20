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

        // Inicializar Retrofit
        Retrofit retrofit = new Retrofit.Builder()
                .baseUrl("http://10.0.2.2:8080/") // Cambia la URL base según tu backend
                .addConverterFactory(GsonConverterFactory.create())
                .build();
        apiService = retrofit.create(ApiService.class);

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
            String nombre = editNombre.getText().toString();
            String apellidos = editApellidos.getText().toString();
            String email = editEmail.getText().toString();
            String telefono = editTelefono.getText().toString();
            String password = editPassword.getText().toString();

            Usuario usuario = new Usuario();
            usuario.setNombre(nombre);
            usuario.setApellidos(apellidos);
            usuario.setEmail(email);
            usuario.setTelefono(telefono);
            usuario.setPassword(password);
            if (usuarioId != -1) usuario.setId(usuarioId);

            Call<Void> call = apiService.actualizarUsuario(usuarioId, usuario);
            call.enqueue(new Callback<Void>() {
                @Override
                public void onResponse(Call<Void> call, Response<Void> response) {
                    if (response.isSuccessful()) {
                        Toast.makeText(EditarPerfilActivity.this, "Datos actualizados correctamente", Toast.LENGTH_SHORT).show();
                        finish();
                    } else {
                        Toast.makeText(EditarPerfilActivity.this, "Error al actualizar los datos", Toast.LENGTH_SHORT).show();
                    }
                }

                @Override
                public void onFailure(Call<Void> call, Throwable t) {
                    Toast.makeText(EditarPerfilActivity.this, "Error de conexión: " + t.getMessage(), Toast.LENGTH_SHORT).show();
                }
            });
        });
    }
}
