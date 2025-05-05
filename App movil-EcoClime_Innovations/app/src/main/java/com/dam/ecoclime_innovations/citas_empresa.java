package com.dam.ecoclime_innovations;

import android.app.DatePickerDialog;
import android.app.TimePickerDialog;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.text.TextUtils;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;


import java.util.Calendar;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class citas_empresa extends AppCompatActivity {
    private EditText etNombre, etTelefono, etEmail;
    private EditText etCiudad, etCodigoPostal, etCalle, etNumeroCasa, etFechaHora;
    private Button btnGuardar, btnCancelar;
    private ApiService apiService;
    private String userEmail;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_citas_empresa);

        userEmail = getIntent().getStringExtra("userEmail");
        if (userEmail == null || userEmail.isEmpty()) {
            Toast.makeText(this, "Error: Usuario no válido", Toast.LENGTH_SHORT).show();
            finish();
            return;
        }

        inicializarVistas();
        configurarListeners();
    }

    private void inicializarVistas() {
        etNombre = findViewById(R.id.etNombre);
        etTelefono = findViewById(R.id.etTelefono);
        etEmail = findViewById(R.id.etEmail);
        etCiudad = findViewById(R.id.etCiudad);
        etCodigoPostal = findViewById(R.id.etCodigoPostal);
        etCalle = findViewById(R.id.etCalle);
        etNumeroCasa = findViewById(R.id.etNumeroCasa);
        etFechaHora = findViewById(R.id.etFechaHora);
        btnGuardar = findViewById(R.id.btnGuardar);
        btnCancelar = findViewById(R.id.btnCancelar);

        apiService = RetrofitClient.getRetrofitInstance().create(ApiService.class);

        // Prellenar el email del usuario
        etEmail.setText(userEmail);
    }

    private void configurarListeners() {
        etFechaHora.setOnClickListener(v -> mostrarSelectorFechaHora());
        btnGuardar.setOnClickListener(v -> guardarCita());
        btnCancelar.setOnClickListener(v -> finish());
    }

    private void mostrarSelectorFechaHora() {
        Calendar calendario = Calendar.getInstance();

        DatePickerDialog datePickerDialog = new DatePickerDialog(
                this,
                (view, year, month, dayOfMonth) -> {
                    // Validar que la fecha no sea anterior a hoy
                    Calendar selectedCalendar = Calendar.getInstance();
                    selectedCalendar.set(year, month, dayOfMonth);
                    Calendar today = Calendar.getInstance();
                    today.set(Calendar.HOUR_OF_DAY, 0);
                    today.set(Calendar.MINUTE, 0);
                    today.set(Calendar.SECOND, 0);
                    today.set(Calendar.MILLISECOND, 0);

                    if (selectedCalendar.before(today)) {
                        Toast.makeText(this, "No se pueden agendar citas en fechas pasadas", Toast.LENGTH_SHORT).show();
                        return;
                    }

                    // Validar que sea día laborable
                    int dayOfWeek = selectedCalendar.get(Calendar.DAY_OF_WEEK);
                    if (dayOfWeek == Calendar.SATURDAY || dayOfWeek == Calendar.SUNDAY) {
                        Toast.makeText(this, "Solo se permiten citas de lunes a viernes", Toast.LENGTH_SHORT).show();
                        return;
                    }

                    TimePickerDialog timePickerDialog = new TimePickerDialog(
                            this,
                            (view1, hourOfDay, minute) -> {
                                // Validar horario laboral (7:00 - 19:00)
                                if (hourOfDay < 7 || hourOfDay >= 19) {
                                    Toast.makeText(this, "El horario de citas es de 7:00 a 19:00", Toast.LENGTH_SHORT).show();
                                    return;
                                }

                                String fecha = String.format("%02d/%02d/%d", dayOfMonth, month + 1, year);
                                String hora = String.format("%02d:%02d", hourOfDay, minute);
                                etFechaHora.setText(fecha + " " + hora);
                            },
                            calendario.get(Calendar.HOUR_OF_DAY),
                            calendario.get(Calendar.MINUTE),
                            true
                    );
                    timePickerDialog.show();
                },
                calendario.get(Calendar.YEAR),
                calendario.get(Calendar.MONTH),
                calendario.get(Calendar.DAY_OF_MONTH)
        );
        datePickerDialog.show();
    }

    private void guardarCita() {
        if (!validarCampos()) {
            Toast.makeText(this, "Por favor, complete todos los campos", Toast.LENGTH_SHORT).show();
            return;
        }

        String[] fechaHora = etFechaHora.getText().toString().split(" ");
        if (fechaHora.length != 2) {
            Toast.makeText(this, "Formato de fecha y hora inválido", Toast.LENGTH_SHORT).show();
            return;
        }

        // Obtener datos del usuario
        apiService.obtenerUsuarioPorEmail(userEmail).enqueue(new Callback<Usuario>() {
            @Override
            public void onResponse(Call<Usuario> call, Response<Usuario> response) {
                if (response.isSuccessful() && response.body() != null) {
                    Usuario usuario = response.body();
                    agendarCitaConUsuario(usuario.getId(), fechaHora);
                } else {
                    String errorMsg = "Error al obtener datos del usuario: " + response.code();
                    Log.e("citas_empresa", errorMsg);
                    Toast.makeText(citas_empresa.this, "Error al obtener datos del usuario", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(Call<Usuario> call, Throwable t) {
                Log.e("citas_empresa", "Error de conexión: " + t.getMessage());
                Toast.makeText(citas_empresa.this, "Error de conexión", Toast.LENGTH_SHORT).show();
            }
        });
    }

    private void agendarCitaConUsuario(int usuarioId, String[] fechaHora) {
        // Crear objeto Cita con todos los datos necesarios
        Cita cita = new Cita();
        cita.setUsuarioId(usuarioId);
        cita.setNombre(etNombre.getText().toString().trim());
        cita.setApellidos(""); // No hay apellidos para empresas
        cita.setTelefono(etTelefono.getText().toString().trim());
        cita.setEmail(etEmail.getText().toString().trim());
        cita.setTipo("Empresa");
        cita.setCiudad(etCiudad.getText().toString().trim());
        cita.setCodigoPostal(etCodigoPostal.getText().toString().trim());
        cita.setCalle(etCalle.getText().toString().trim());
        cita.setNumeroCasa(etNumeroCasa.getText().toString().trim());
        cita.setFecha(fechaHora[0]);
        cita.setHora(fechaHora[1]);
        cita.setEstado("pendiente");

        // Enviar la cita al servidor
        apiService.agendarCita(usuarioId, cita).enqueue(new Callback<Cita>() {
            @Override
            public void onResponse(Call<Cita> call, Response<Cita> response) {
                if (response.isSuccessful() && response.body() != null) {
                    Toast.makeText(citas_empresa.this, "Cita agendada con éxito", Toast.LENGTH_SHORT).show();
                    // Actualizar el contador de citas en SharedPreferences
                    actualizarContadorCitas();
                    finish();
                } else {
                    String errorMsg = "Error al agendar la cita: ";
                    if (response.errorBody() != null) {
                        try {
                            errorMsg += response.errorBody().string();
                        } catch (Exception e) {
                            errorMsg += response.code();
                        }
                    }
                    Log.e("citas_empresa", errorMsg);
                    Toast.makeText(citas_empresa.this, "Error al agendar la cita", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(Call<Cita> call, Throwable t) {
                Log.e("citas_empresa", "Error de conexión: " + t.getMessage());
                Toast.makeText(citas_empresa.this, "Error de conexión", Toast.LENGTH_SHORT).show();
            }
        });
    }

    private void actualizarContadorCitas() {
        SharedPreferences sharedPreferences = getSharedPreferences("UserPrefs", MODE_PRIVATE);
        int citasActuales = sharedPreferences.getInt("citasCount", 0);
        SharedPreferences.Editor editor = sharedPreferences.edit();
        editor.putInt("citasCount", citasActuales + 1);
        editor.apply();
    }

    private boolean validarCampos() {
        return !TextUtils.isEmpty(etNombre.getText()) &&
                !TextUtils.isEmpty(etTelefono.getText()) &&
                !TextUtils.isEmpty(etEmail.getText()) &&
                !TextUtils.isEmpty(etCiudad.getText()) &&
                !TextUtils.isEmpty(etCodigoPostal.getText()) &&
                !TextUtils.isEmpty(etCalle.getText()) &&
                !TextUtils.isEmpty(etNumeroCasa.getText()) &&
                !TextUtils.isEmpty(etFechaHora.getText());
    }
}
