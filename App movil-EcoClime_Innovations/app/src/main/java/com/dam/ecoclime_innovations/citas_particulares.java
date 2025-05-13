package com.dam.ecoclime_innovations;

import android.app.DatePickerDialog;
import android.app.TimePickerDialog;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.os.Handler;
import android.text.TextUtils;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.CalendarView;
import android.widget.EditText;
import android.widget.ImageButton;
import android.widget.ScrollView;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;

import java.util.Calendar;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class citas_particulares extends AppCompatActivity {
    private static final String TAG = "citas_particulares";
    private EditText etNombre, etApellidos, etTelefono, etEmail;
    private EditText etCiudad, etCodigoPostal, etCalle, etNumeroCasa, etMensajeOpcional;
    private Button btnGuardar, btnCancelar;
    private ScrollView layoutFormulario;
    private ApiService apiService;
    private String userEmail;
    private String fechaSeleccionada;
    private String horaSeleccionada;
    private static final int MAX_RETRIES = 3;
    private int retryCount = 0;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_citas_particulares);

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
        etNombre = findViewById(R.id.nombre);
        etApellidos = findViewById(R.id.apellidos);
        etTelefono = findViewById(R.id.telefono);
        etEmail = findViewById(R.id.email);
        etCiudad = findViewById(R.id.ciudad);
        etCodigoPostal = findViewById(R.id.codigoPostal);
        etCalle = findViewById(R.id.calle);
        etNumeroCasa = findViewById(R.id.numero);
        etMensajeOpcional = findViewById(R.id.mensaje);
        btnGuardar = findViewById(R.id.confirmar);
        btnCancelar = findViewById(R.id.btnVolverFecha);
        layoutFormulario = findViewById(R.id.layoutFormulario);

        // Configurar el botón atrás
        ImageButton botonAtras = findViewById(R.id.botonAtrasParticulares);
        botonAtras.setOnClickListener(v -> finish());

        // Configurar el CalendarView para la fecha
        CalendarView calendarView = findViewById(R.id.calendarioCitas);
        calendarView.setOnDateChangeListener((view, year, month, dayOfMonth) -> {
            // Formatear la fecha como YYYY-MM-DD
            fechaSeleccionada = String.format("%04d-%02d-%02d", year, month + 1, dayOfMonth);
            Log.d(TAG, "Fecha seleccionada: " + fechaSeleccionada);
            Toast.makeText(this, "Fecha seleccionada: " + fechaSeleccionada, Toast.LENGTH_SHORT).show();
        });

        // Configurar el botón para seleccionar hora
        Button btnSeleccionarHora = findViewById(R.id.seleccionarHora);
        btnSeleccionarHora.setOnClickListener(v -> mostrarSelectorHora());

        Button btnSiguiente = findViewById(R.id.btnSiguiente);
        btnSiguiente.setOnClickListener(v -> {
            if (fechaSeleccionada == null || fechaSeleccionada.isEmpty()) {
                Toast.makeText(this, "Por favor, seleccione una fecha", Toast.LENGTH_SHORT).show();
                return;
            }
            if (horaSeleccionada == null || horaSeleccionada.isEmpty()) {
                Toast.makeText(this, "Por favor, seleccione una hora", Toast.LENGTH_SHORT).show();
                return;
            }
            findViewById(R.id.layoutSeleccionFecha).setVisibility(View.GONE);
            findViewById(R.id.layoutFormulario).setVisibility(View.VISIBLE);
        });

        apiService = RetrofitClient.getRetrofitInstance().create(ApiService.class);

        // Prellenar el email del usuario
        etEmail.setText(userEmail);
    }

    private void configurarListeners() {
        btnGuardar.setOnClickListener(v -> guardarCita());
        btnCancelar.setOnClickListener(v -> finish());
    }

    private void mostrarSelectorHora() {
        Calendar calendario = Calendar.getInstance();
        TimePickerDialog timePickerDialog = new TimePickerDialog(
                this,
                (view1, hourOfDay, minute) -> {
                    // Validar horario laboral (7:00 - 19:00)
                    if (hourOfDay < 7 || hourOfDay >= 19) {
                        Toast.makeText(this, "El horario de citas es de 7:00 a 19:00", Toast.LENGTH_SHORT).show();
                        return;
                    }

                    String hora = String.format("%02d:%02d", hourOfDay, minute);
                    horaSeleccionada = hora;
                    Toast.makeText(this, "Hora seleccionada: " + hora, Toast.LENGTH_SHORT).show();
                },
                7, // Hora inicial: 7:00
                0, // Minuto inicial: 00
                true // Formato 24 horas
        );
        timePickerDialog.show();
    }

    private void guardarCita() {
        if (!validarCampos()) {
            Toast.makeText(this, "Por favor, complete todos los campos obligatorios", Toast.LENGTH_SHORT).show();
            return;
        }

        if (horaSeleccionada == null || horaSeleccionada.isEmpty()) {
            Toast.makeText(this, "Por favor, seleccione una hora", Toast.LENGTH_SHORT).show();
            return;
        }

        // Obtener datos del usuario
        apiService.obtenerUsuarioPorEmail(userEmail).enqueue(new Callback<Usuario>() {
            @Override
            public void onResponse(Call<Usuario> call, Response<Usuario> response) {
                if (response.isSuccessful() && response.body() != null) {
                    Usuario usuario = response.body();
                    agendarCitaConUsuario(usuario.getId());
                } else {
                    String errorMsg = "Error al obtener datos del usuario: " + response.code();
                    Log.e(TAG, errorMsg);
                    Toast.makeText(citas_particulares.this, "Error al obtener datos del usuario", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(Call<Usuario> call, Throwable t) {
                Log.e(TAG, "Error de conexión: " + t.getMessage());
                Toast.makeText(citas_particulares.this, "Error de conexión", Toast.LENGTH_SHORT).show();
            }
        });
    }

    private void agendarCitaConUsuario(int usuarioId) {
        Log.d(TAG, "Iniciando proceso de agendar cita para usuario ID: " + usuarioId);
        
        // Crear objeto Cita con todos los datos necesarios
        Cita cita = new Cita();
        cita.setUsuarioId(usuarioId);
        cita.setNombre(etNombre.getText().toString().trim());
        cita.setApellidos(etApellidos.getText().toString().trim());
        cita.setTelefono(etTelefono.getText().toString().trim());
        cita.setEmail(etEmail.getText().toString().trim());
        cita.setTipo("Particular");
        cita.setCiudad(etCiudad.getText().toString().trim());
        cita.setCodigoPostal(etCodigoPostal.getText().toString().trim());
        cita.setCalle(etCalle.getText().toString().trim());
        cita.setNumeroCasa(etNumeroCasa.getText().toString().trim());
        cita.setFecha(fechaSeleccionada);
        cita.setHora(horaSeleccionada);
        cita.setFechaHora(fechaSeleccionada + "T" + horaSeleccionada + ":00");
        cita.setMensaje(etMensajeOpcional.getText().toString().trim());
        cita.setEstado("pendiente");

        Log.d(TAG, "Datos de la cita preparados: " + cita.toString());
        Log.d(TAG, "Enviando cita al servidor... (Intento " + (retryCount + 1) + " de " + MAX_RETRIES + ")");

        // Enviar la cita al servidor
        apiService.crearCita(usuarioId, cita).enqueue(new Callback<Cita>() {
            @Override
            public void onResponse(Call<Cita> call, Response<Cita> response) {
                if (response.isSuccessful() && response.body() != null) {
                    Cita citaGuardada = response.body();
                    Log.d(TAG, "Cita agendada exitosamente. ID de la cita: " + citaGuardada.getId());
                    Log.d(TAG, "Detalles de la cita guardada: " + citaGuardada.toString());
                    Toast.makeText(citas_particulares.this, "Cita agendada con éxito", Toast.LENGTH_SHORT).show();
                    actualizarContadorCitas();
                    finish();
                } else {
                    String errorMsg = "Error al agendar la cita: ";
                    if (response.errorBody() != null) {
                        try {
                            errorMsg += response.errorBody().string();
                            Log.e(TAG, "Error detallado del servidor: " + errorMsg);
                            
                            if (errorMsg.contains("ObjectOptimisticLockingFailureException")) {
                                if (retryCount < MAX_RETRIES - 1) {
                                    retryCount++;
                                    Log.e(TAG, "Error de concurrencia detectado. Intentando nuevamente... (Intento " + (retryCount + 1) + " de " + MAX_RETRIES + ")");
                                    new Handler().postDelayed(() -> {
                                        Log.d(TAG, "Reintentando agendar cita...");
                                        agendarCitaConUsuario(usuarioId);
                                    }, 2000);
                                    return;
                                } else {
                                    Log.e(TAG, "Se alcanzó el número máximo de reintentos por error de concurrencia");
                                    Toast.makeText(citas_particulares.this, "No se pudo agendar la cita. Por favor, intente nuevamente en unos minutos.", Toast.LENGTH_LONG).show();
                                }
                            } else {
                                Log.e(TAG, "Error del servidor no manejado: " + errorMsg);
                                Toast.makeText(citas_particulares.this, "Error al agendar la cita. Por favor, intente nuevamente.", Toast.LENGTH_LONG).show();
                            }
                        } catch (Exception e) {
                            errorMsg += response.code();
                            Log.e(TAG, "Error al procesar respuesta del servidor: " + e.getMessage());
                            Log.e(TAG, "Stack trace del error: ", e);
                        }
                    }
                }
            }

            @Override
            public void onFailure(Call<Cita> call, Throwable t) {
                Log.e(TAG, "Error de conexión al intentar agendar la cita: " + t.getMessage());
                Log.e(TAG, "Stack trace completo: ", t);
                
                if (retryCount < MAX_RETRIES - 1) {
                    retryCount++;
                    Log.d(TAG, "Reintentando después de error de conexión... (Intento " + (retryCount + 1) + " de " + MAX_RETRIES + ")");
                    new Handler().postDelayed(() -> {
                        agendarCitaConUsuario(usuarioId);
                    }, 3000);
                } else {
                    Log.e(TAG, "Se alcanzó el número máximo de reintentos después de errores de conexión");
                    String mensajeError = "Error de conexión: ";
                    if (t instanceof java.net.UnknownHostException) {
                        mensajeError += "No se pudo conectar al servidor. Verifique su conexión a internet.";
                    } else if (t instanceof java.net.SocketTimeoutException) {
                        mensajeError += "El servidor tardó demasiado en responder.";
                    } else if (t instanceof java.io.EOFException) {
                        mensajeError += "Error en la comunicación con el servidor. Por favor, intente nuevamente.";
                    } else {
                        mensajeError += t.getMessage();
                    }
                    Toast.makeText(citas_particulares.this, mensajeError, Toast.LENGTH_LONG).show();
                }
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
                !TextUtils.isEmpty(etApellidos.getText()) &&
                !TextUtils.isEmpty(etTelefono.getText()) &&
                !TextUtils.isEmpty(etEmail.getText()) &&
                !TextUtils.isEmpty(etCiudad.getText()) &&
                !TextUtils.isEmpty(etCodigoPostal.getText()) &&
                !TextUtils.isEmpty(etCalle.getText()) &&
                !TextUtils.isEmpty(etNumeroCasa.getText());
    }
}
