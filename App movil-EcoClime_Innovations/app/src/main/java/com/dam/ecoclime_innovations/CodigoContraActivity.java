package com.dam.ecoclime_innovations;

import android.content.Intent;
import android.os.Bundle;
import android.os.CountDownTimer;
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

public class CodigoContraActivity extends AppCompatActivity {
    private EditText codigoEditText;
    private Button verificarCodigoButton;
    private TextView contadorTextView, errorTextView;
    private String email;
    private CountDownTimer countDownTimer;
    private long tiempoRestante = 10 * 60 * 1000; // 10 minutos en ms
    private RecuperacionApi recuperacionApi;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_codigo_contra);

        email = getIntent().getStringExtra("email");
        codigoEditText = findViewById(R.id.codigoEditText);
        verificarCodigoButton = findViewById(R.id.verificarCodigoButton);
        contadorTextView = findViewById(R.id.contadorTextView);
        errorTextView = findViewById(R.id.errorTextView);
        recuperacionApi = RetrofitRecuperacionClient.getApi();

        iniciarContador();

        verificarCodigoButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                String codigo = codigoEditText.getText().toString().trim();
                if (codigo.isEmpty()) {
                    errorTextView.setText("Por favor, introduce el código");
                    errorTextView.setVisibility(View.VISIBLE);
                    return;
                }
                Map<String, String> body = new HashMap<>();
                body.put("email", email);
                body.put("codigo", codigo);
                recuperacionApi.verificarCodigo(body).enqueue(new Callback<Void>() {
                    @Override
                    public void onResponse(Call<Void> call, Response<Void> response) {
                        if (response.isSuccessful()) {
                            // Código válido, ir a pantalla de cambiar contraseña
                            Intent intent = new Intent(CodigoContraActivity.this, CambioContraActivity.class);
                            intent.putExtra("email", email);
                            intent.putExtra("codigo", codigo);
                            startActivity(intent);
                            finish();
                        } else {
                            errorTextView.setText("Código inválido o expirado");
                            errorTextView.setVisibility(View.VISIBLE);
                        }
                    }
                    @Override
                    public void onFailure(Call<Void> call, Throwable t) {
                        errorTextView.setText("Error de red");
                        errorTextView.setVisibility(View.VISIBLE);
                    }
                });
            }
        });
    }

    private void iniciarContador() {
        countDownTimer = new CountDownTimer(tiempoRestante, 1000) {
            public void onTick(long millisUntilFinished) {
                int minutos = (int) (millisUntilFinished / 60000);
                int segundos = (int) ((millisUntilFinished % 60000) / 1000);
                contadorTextView.setText(String.format("%02d:%02d", minutos, segundos));
            }
            public void onFinish() {
                contadorTextView.setText("00:00");
                verificarCodigoButton.setEnabled(false);
                errorTextView.setText("El código ha expirado. Solicita uno nuevo.");
                errorTextView.setVisibility(View.VISIBLE);
            }
        };
        countDownTimer.start();
    }

    @Override
    protected void onDestroy() {
        super.onDestroy();
        if (countDownTimer != null) countDownTimer.cancel();
    }
}
