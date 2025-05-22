package com.dam.ecoclime_innovations;

import android.app.DatePickerDialog;
import android.app.TimePickerDialog;
import android.os.Bundle;
import android.text.TextUtils;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;

import java.util.Calendar;
import java.util.HashMap;
import java.util.Map;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class ModificarCitaActivity extends AppCompatActivity {
    private EditText etNombre, etTelefono, etEmail, etTipo;
    private EditText etCiudad, etCodigoPostal, etCalle, etNumeroCasa, etFechaHora;
    private Button btnGuardar, btnCancelar;
    private ApiService apiService;
    private int citaId;
    private String userEmail;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_modificar_cita);

        citaId = getIntent().getIntExtra("citaId", -1);
        userEmail = getIntent().getStringExtra("userEmail");

        if (citaId == -1 || userEmail == null || userEmail.isEmpty()) {
            Toast.makeText(this, "Error: Datos de cita no válidos", Toast.LENGTH_SHORT).show();
            finish();
            return;
        }

        inicializarVistas();
        configurarListeners();
        cargarDatosCita();
    }

    private void inicializarVistas() {
        etNombre = findViewById(R.id.etNombre);
        
        etTelefono = findViewById(R.id.etTelefono);
        etEmail = findViewById(R.id.etEmail);
        etTipo = findViewById(R.id.etTipo);
        etCiudad = findViewById(R.id.etCiudad);
        etCodigoPostal = findViewById(R.id.etCodigoPostal);
        etCalle = findViewById(R.id.etCalle);
        etNumeroCasa = findViewById(R.id.etNumeroCasa);
        etFechaHora = findViewById(R.id.etFechaHora);
        btnGuardar = findViewById(R.id.btnGuardar);
        btnCancelar = findViewById(R.id.btnCancelar);

        apiService = RetrofitClient.getRetrofitInstance().create(ApiService.class);
    }

    private void configurarListeners() {
        btnGuardar.setOnClickListener(v -> guardarCambios());
        btnCancelar.setOnClickListener(v -> finish());
        etFechaHora.setOnClickListener(v -> mostrarSelectorFechaHora());
    }

    private String fechaOriginal = "";
private String horaOriginal = "";

private void cargarDatosCita() {
        // Llama a la API para obtener los datos de la cita
        apiService.obtenerCita(citaId).enqueue(new Callback<Cita>() {
            @Override
            public void onResponse(Call<Cita> call, Response<Cita> response) {
                if (response.isSuccessful() && response.body() != null) {
                    Cita cita = response.body();
                    etNombre.setText(cita.getNombre());
                    etTelefono.setText(cita.getTelefono());
                    etEmail.setText(cita.getEmail());
                    etTipo.setText(cita.getTipo());
                    etCiudad.setText(cita.getCiudad());
                    etCodigoPostal.setText(cita.getCodigoPostal());
                    etCalle.setText(cita.getCalle());
                    etNumeroCasa.setText(cita.getNumeroCasa());
                    String fecha = cita.getFecha();
                    String hora = cita.getHora();
                    if (fecha != null && hora != null && !fecha.equalsIgnoreCase("null") && !hora.equalsIgnoreCase("null") &&
                        !fecha.isEmpty() && !hora.isEmpty()) {
                        // Mostrar en formato dd/MM/yyyy HH:mm
                        String[] partes = fecha.split("-");
                        if (partes.length == 3) {
                            String fechaMostrar = partes[2] + "/" + partes[1] + "/" + partes[0];
                            etFechaHora.setText(fechaMostrar + " " + hora);
                        } else {
                            etFechaHora.setText(fecha + " " + hora);
                        }
                        fechaOriginal = fecha;
                        horaOriginal = hora;
                    } else {
                        etFechaHora.setText(""); // Obliga a seleccionar fecha/hora
                        fechaOriginal = "";
                        horaOriginal = "";
                    }
                } else {
                    // Mostrar mensaje de error detallado
                    Toast.makeText(ModificarCitaActivity.this, "Error al cargar los datos de la cita. Código: " + response.code(), Toast.LENGTH_LONG).show();
                    finish();
                }
            }

            @Override
            public void onFailure(Call<Cita> call, Throwable t) {
                Toast.makeText(ModificarCitaActivity.this, "Error de conexión: " + t.getMessage(), Toast.LENGTH_LONG).show();
                finish();
            }
        });
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

    private void guardarCambios() {
        if (!validarCampos()) {
            Toast.makeText(this, "Por favor, complete todos los campos", Toast.LENGTH_SHORT).show();
            return;
        }

        String[] fechaHora = etFechaHora.getText().toString().split(" ");
        if (fechaHora.length != 2 ||
            fechaHora[0].equalsIgnoreCase("null") || fechaHora[1].equalsIgnoreCase("null") ||
            fechaHora[0].isEmpty() || fechaHora[1].isEmpty()) {
            Toast.makeText(this, "Debes seleccionar una fecha y hora válidas", Toast.LENGTH_SHORT).show();
            return;
        }

        // Obtener datos del usuario
        apiService.obtenerUsuarioPorEmail(userEmail).enqueue(new Callback<Usuario>() {
            @Override
            public void onResponse(Call<Usuario> call, Response<Usuario> response) {
                if (response.isSuccessful() && response.body() != null) {
                    Usuario usuario = response.body();
                    actualizarCitaConUsuario(usuario.getId(), fechaHora);
                } else {
                    Toast.makeText(ModificarCitaActivity.this, "Error al obtener datos del usuario", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(Call<Usuario> call, Throwable t) {
                Toast.makeText(ModificarCitaActivity.this, "Error de conexión", Toast.LENGTH_SHORT).show();
            }
        });
    }

    private void actualizarCitaConUsuario(int usuarioId, String[] fechaHora) {
    // Crear un Map con solo los campos permitidos
    Map<String, Object> campos = new HashMap<>();
    campos.put("nombre", etNombre.getText().toString());
    campos.put("telefono", etTelefono.getText().toString());
    campos.put("email", etEmail.getText().toString());
    campos.put("tipo", etTipo.getText().toString());
    campos.put("ciudad", etCiudad.getText().toString());
    campos.put("codigo_postal", etCodigoPostal.getText().toString());
    campos.put("calle", etCalle.getText().toString());
    campos.put("numero_casa", etNumeroCasa.getText().toString());
    // Si el usuario no cambió fecha/hora, usa los originales
    String fechaInput = fechaHora[0];
    String horaInput = fechaHora[1];
    String fechaFormateada;
    if (fechaInput.contains("/")) {
        String[] partesFecha = fechaInput.split("/");
        fechaFormateada = partesFecha[2] + "-" + partesFecha[1] + "-" + partesFecha[0];
    } else {
        fechaFormateada = fechaInput; // Ya está en formato backend
    }
    if (fechaFormateada.isEmpty()) fechaFormateada = fechaOriginal;
    if (horaInput.isEmpty()) horaInput = horaOriginal;
    campos.put("fecha", fechaFormateada);
    campos.put("hora", horaInput);
    campos.put("usuario_id", usuarioId); // Si el backend espera un objeto, adaptar aquí

    apiService.actualizarCita(citaId, campos).enqueue(new Callback<Void>() {
            @Override
            public void onResponse(Call<Void> call, Response<Void> response) {
                if (response.isSuccessful()) {
                    Toast.makeText(ModificarCitaActivity.this, "Cita actualizada con éxito", Toast.LENGTH_SHORT).show();
                    finish();
                } else {
                    Toast.makeText(ModificarCitaActivity.this, "Error al actualizar la cita", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(Call<Void> call, Throwable t) {
                Toast.makeText(ModificarCitaActivity.this, "Error de conexión", Toast.LENGTH_SHORT).show();
            }
        });
    }

    private boolean validarCampos() {
        return !TextUtils.isEmpty(etNombre.getText()) &&
                !TextUtils.isEmpty(etTelefono.getText()) &&
                !TextUtils.isEmpty(etEmail.getText()) &&
                !TextUtils.isEmpty(etTipo.getText()) &&
                !TextUtils.isEmpty(etCiudad.getText()) &&
                !TextUtils.isEmpty(etCodigoPostal.getText()) &&
                !TextUtils.isEmpty(etCalle.getText()) &&
                !TextUtils.isEmpty(etNumeroCasa.getText()) &&
                !TextUtils.isEmpty(etFechaHora.getText());
    }
}