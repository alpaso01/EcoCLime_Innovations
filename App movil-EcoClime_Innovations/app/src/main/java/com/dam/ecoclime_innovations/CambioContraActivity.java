package com.dam.ecoclime_innovations;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;
import java.util.HashMap;
import java.util.Map;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class CambioContraActivity extends AppCompatActivity {
    private EditText nuevaPasswordEditText;
    private EditText confirmarPasswordEditText;
    private Button cambiarContraButton;
    private TextView errorTextView;
    private String email;
    private ApiService apiService;

    public static void start(AppCompatActivity activity, String email) {
        Intent intent = new Intent(activity, CambioContraActivity.class);
        intent.putExtra("email", email);
        activity.startActivity(intent);
    }

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_cambio_contra);

        // Obtener email del Intent
        email = getIntent().getStringExtra("email");

        // Inicializar vistas
        nuevaPasswordEditText = findViewById(R.id.nuevaPasswordEditText);
        confirmarPasswordEditText = findViewById(R.id.confirmarPasswordEditText);
        cambiarContraButton = findViewById(R.id.cambiarContraButton);
        errorTextView = findViewById(R.id.errorTextView);

        // Inicializar Retrofit
        apiService = RetrofitClient.getInstance().create(ApiService.class);

        // Configurar click listener
        cambiarContraButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                String nuevaPassword = nuevaPasswordEditText.getText().toString().trim();
                String confirmarPassword = confirmarPasswordEditText.getText().toString().trim();

                if (nuevaPassword.isEmpty() || confirmarPassword.isEmpty()) {
                    errorTextView.setText("Por favor, complete todos los campos");
                    errorTextView.setVisibility(View.VISIBLE);
                    return;
                }

                if (!nuevaPassword.equals(confirmarPassword)) {
                    errorTextView.setText("Las contraseñas no coinciden");
                    errorTextView.setVisibility(View.VISIBLE);
                    return;
                }

                // Crear Map con los datos
                Map<String, String> request = new HashMap<>();
                request.put("email", email);
                request.put("nuevaPassword", nuevaPassword);

                // Llamar a la API para cambiar la contraseña
                Call<Void> call = apiService.cambiarPassword(request);
                call.enqueue(new Callback<Void>() {
                    @Override
                    public void onResponse(Call<Void> call, Response<Void> response) {
                        if (response.isSuccessful()) {
                            Toast.makeText(CambioContraActivity.this, "Contraseña cambiada exitosamente", Toast.LENGTH_SHORT).show();
                            finish(); // Volver a la pantalla anterior
                        } else {
                            errorTextView.setText("Error al cambiar la contraseña");
                            errorTextView.setVisibility(View.VISIBLE);
                        }
                    }

                    @Override
                    public void onFailure(Call<Void> call, Throwable t) {
                        errorTextView.setText("Error al cambiar la contraseña");
                        errorTextView.setVisibility(View.VISIBLE);
                        Toast.makeText(CambioContraActivity.this, "Error: " + t.getMessage(), Toast.LENGTH_SHORT).show();
                    }
                });
            }
        });
    }
}
