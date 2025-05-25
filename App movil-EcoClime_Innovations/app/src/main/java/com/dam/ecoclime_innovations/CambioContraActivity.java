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
    private String codigo;
    private RecuperacionApi recuperacionApi;

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
        codigo = getIntent().getStringExtra("codigo");

        // Inicializar vistas
        nuevaPasswordEditText = findViewById(R.id.nuevaPasswordEditText);
        confirmarPasswordEditText = findViewById(R.id.confirmarPasswordEditText);
        cambiarContraButton = findViewById(R.id.cambiarContraButton);
        errorTextView = findViewById(R.id.errorTextView);

        recuperacionApi = RetrofitRecuperacionClient.getApi();

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

                // Llamar al endpoint restablecer-contrasena
                Map<String, String> body = new HashMap<>();
                body.put("email", email);
                body.put("codigo", codigo);
                body.put("nuevaContrasena", nuevaPassword);
                cambiarContraButton.setEnabled(false);
                recuperacionApi.restablecerContrasena(body).enqueue(new Callback<Void>() {
                    @Override
                    public void onResponse(Call<Void> call, Response<Void> response) {
                        cambiarContraButton.setEnabled(true);
                        if (response.isSuccessful()) {
                            Toast.makeText(CambioContraActivity.this, "Contraseña cambiada exitosamente", Toast.LENGTH_SHORT).show();
                            startActivity(new Intent(CambioContraActivity.this, LoginActivity.class));
                            finish();
                        } else {
                            errorTextView.setText("Error al cambiar la contraseña. Código inválido o expirado.");
                            errorTextView.setVisibility(View.VISIBLE);
                        }
                    }

                    @Override
                    public void onFailure(Call<Void> call, Throwable t) {
                        cambiarContraButton.setEnabled(true);
                        errorTextView.setText("Error de red");
                        errorTextView.setVisibility(View.VISIBLE);
                    }
                });
            }
        });
    }
}
