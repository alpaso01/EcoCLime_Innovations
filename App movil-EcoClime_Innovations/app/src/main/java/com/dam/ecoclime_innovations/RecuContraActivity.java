package com.dam.ecoclime_innovations;

import android.content.Intent;
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
    private Button enviarCodigoButton;
    private TextView errorTextView;
    private android.widget.ImageButton btnBack;
    private RecuperacionApi recuperacionApi;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_recu_contra);

        // Inicializar vistas
        emailEditText = findViewById(R.id.emailEditText);
        enviarCodigoButton = findViewById(R.id.verificarButton);
        errorTextView = findViewById(R.id.errorTextView);
        btnBack = findViewById(R.id.btnBack);
        btnBack.setOnClickListener(v -> {
            // Redirigir a LoginActivity
            startActivity(new android.content.Intent(RecuContraActivity.this, LoginActivity.class));
            finish();
        });

        recuperacionApi = RetrofitRecuperacionClient.getApi();

        enviarCodigoButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                String email = emailEditText.getText().toString().trim();
                if (email.isEmpty()) {
                    errorTextView.setText("Por favor, ingrese su email");
                    errorTextView.setVisibility(View.VISIBLE);
                    return;
                }
                // Llamar al endpoint para enviar código
                java.util.Map<String, String> body = new java.util.HashMap<>();
                body.put("email", email);
                enviarCodigoButton.setEnabled(false);
                recuperacionApi.enviarCodigo(body).enqueue(new Callback<Void>() {
                    @Override
                    public void onResponse(Call<Void> call, Response<Void> response) {
                        enviarCodigoButton.setEnabled(true);
                        if (response.isSuccessful()) {
                            // Ir a la pantalla de introducir código
                            Intent intent = new Intent(RecuContraActivity.this, CodigoContraActivity.class);
                            intent.putExtra("email", email);
                            startActivity(intent);
                            finish();
                        } else {
                            errorTextView.setText("No se encontró ningún usuario con este email o error enviando código");
                            errorTextView.setVisibility(View.VISIBLE);
                        }
                    }

                    @Override
                    public void onFailure(Call<Void> call, Throwable t) {
                        enviarCodigoButton.setEnabled(true);
                        errorTextView.setText("Error de red: " + t.getMessage());
                        errorTextView.setVisibility(View.VISIBLE);
                        android.util.Log.e("Recuperacion", "Error enviando código", t);
                        Toast.makeText(RecuContraActivity.this, "Error: " + t.getMessage(), Toast.LENGTH_LONG).show();
                    }
                });
            }
        });
    }
}