package com.dam.ecoclime_innovations;

import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class RecuContraActivity extends AppCompatActivity {
    private EditText emailEditText;
    private Button verificarButton;
    private TextView errorTextView;
    private ApiService apiService;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_recu_contra);

        // Inicializar vistas
        emailEditText = findViewById(R.id.emailEditText);
        verificarButton = findViewById(R.id.verificarButton);
        errorTextView = findViewById(R.id.errorTextView);

        // Inicializar Retrofit
        apiService = RetrofitClient.getInstance().create(ApiService.class);

        // Configurar click listener
        verificarButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                String email = emailEditText.getText().toString().trim();
                if (email.isEmpty()) {
                    errorTextView.setText("Por favor, ingrese su email");
                    errorTextView.setVisibility(View.VISIBLE);
                    return;
                }

                // Verificar email en la API
                Call<Usuario> call = apiService.obtenerUsuarioPorEmail(email);
                call.enqueue(new Callback<Usuario>() {
                    @Override
                    public void onResponse(Call<Usuario> call, Response<Usuario> response) {
                        if (response.isSuccessful() && response.body() != null) {
                            // Email encontrado, ir a pantalla de cambio de contraseña
                            CambioContraActivity.start(RecuContraActivity.this, email);
                        } else {
                            errorTextView.setText("No se encontró ningún usuario con este email");
                            errorTextView.setVisibility(View.VISIBLE);
                        }
                    }

                    @Override
                    public void onFailure(Call<Usuario> call, Throwable t) {
                        errorTextView.setText("Error al verificar el email");
                        errorTextView.setVisibility(View.VISIBLE);
                        Toast.makeText(RecuContraActivity.this, "Error: " + t.getMessage(), Toast.LENGTH_SHORT).show();
                    }
                });
            }
        });
    }
}
