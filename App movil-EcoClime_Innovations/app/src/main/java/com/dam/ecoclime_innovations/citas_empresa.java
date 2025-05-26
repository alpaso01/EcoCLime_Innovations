package com.dam.ecoclime_innovations;

import android.app.DatePickerDialog;
import android.app.TimePickerDialog;
import android.content.Intent;
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
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;

import java.util.Calendar;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class citas_empresa extends AppCompatActivity {
    private EditText etNombre, etTelefono, etEmail;
    private EditText etCiudad, etCodigoPostal, etCalle, etNumeroCasa, etMensajeOpcional;
    private Button btnGuardar, btnCancelar;
    private ApiService apiService;
    private String userEmail;
    private String fechaSeleccionada;
    private String horaSeleccionada;
    private static final int MAX_RETRIES = 3;
    private int retryCount = 0;

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
        etNombre = findViewById(R.id.nombreEmpresa);
        etTelefono = findViewById(R.id.telefonoEmpresa);
        etEmail = findViewById(R.id.emailEmpresa);
        etCiudad = findViewById(R.id.ciudadEmpresa);
        etCodigoPostal = findViewById(R.id.codigoPostalEmpresa);
        etCalle = findViewById(R.id.calleEmpresa);
        etNumeroCasa = findViewById(R.id.numeroEmpresa);
        etMensajeOpcional = findViewById(R.id.mensajeEmpresa);
        btnGuardar = findViewById(R.id.confirmarEmpresa);
        btnCancelar = findViewById(R.id.btnVolverFechaEmpresa);

        // Configurar el botón atrás
        ImageButton botonAtras = findViewById(R.id.botonAtrasEmpresa);
        botonAtras.setOnClickListener(v -> finish());

        // Configurar el CalendarView para la fecha
        CalendarView calendarView = findViewById(R.id.calendarioCitasEmpresa);
        calendarView.setOnDateChangeListener((view, year, month, dayOfMonth) -> {
            // Formatear la fecha como YYYY-MM-DD
            fechaSeleccionada = String.format("%04d-%02d-%02d", year, month + 1, dayOfMonth);
            Log.d("citas_empresa", "Fecha seleccionada: " + fechaSeleccionada);
            Toast.makeText(this, "Fecha seleccionada: " + fechaSeleccionada, Toast.LENGTH_SHORT).show();
        });

        // Configurar el botón para seleccionar hora
        Button btnSeleccionarHora = findViewById(R.id.seleccionarHoraEmpresa);
        btnSeleccionarHora.setOnClickListener(v -> mostrarSelectorHora());

        Button btnSiguiente = findViewById(R.id.btnSiguienteEmpresa);
        btnSiguiente.setOnClickListener(v -> {
            if (fechaSeleccionada == null || fechaSeleccionada.isEmpty()) {
                Toast.makeText(this, "Por favor, seleccione una fecha", Toast.LENGTH_SHORT).show();
                return;
            }
            if (horaSeleccionada == null || horaSeleccionada.isEmpty()) {
                Toast.makeText(this, "Por favor, seleccione una hora", Toast.LENGTH_SHORT).show();
                return;
            }
            findViewById(R.id.layoutSeleccionFechaEmpresa).setVisibility(View.GONE);
            findViewById(R.id.layoutFormularioEmpresa).setVisibility(View.VISIBLE);
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
                    // Guardar la hora seleccionada en una variable de clase
                    horaSeleccionada = hora;
                    // Mostrar la hora en un TextView o mantenerla en memoria
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

    private void agendarCitaConUsuario(int usuarioId) {
        Log.d("citas_empresa", "Iniciando proceso de agendar cita para usuario ID: " + usuarioId);

        // Crear objeto Cita con todos los datos necesarios
        Cita cita = new Cita();
        // No establecemos el ID, dejamos que el servidor lo asigne
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
        cita.setFecha(fechaSeleccionada);
        cita.setHora(horaSeleccionada);
        cita.setMensaje(etMensajeOpcional.getText().toString().trim());
        cita.setEstado("pendiente");

        Log.d("citas_empresa", "Datos de la cita preparados: " + cita.toString());
        Log.d("citas_empresa", "Enviando cita al servidor... (Intento " + (retryCount + 1) + " de " + MAX_RETRIES + ")");

        // Enviar la cita al servidor usando un nuevo endpoint
        apiService.crearCita(usuarioId, cita).enqueue(new Callback<Cita>() {
            @Override
            public void onResponse(Call<Cita> call, Response<Cita> response) {
                if (response.isSuccessful() && response.body() != null) {
                    Cita citaGuardada = response.body();
                    Log.d("citas_empresa", "Cita agendada exitosamente. ID de la cita: " + citaGuardada.getId());
                    Log.d("citas_empresa", "Detalles de la cita guardada: " + citaGuardada.toString());
                    Toast.makeText(citas_empresa.this, "Cita agendada con éxito", Toast.LENGTH_SHORT).show();
                    actualizarContadorCitas();
                    enviarCorreoConfirmacion(citaGuardada);
                } else if (response.isSuccessful()) {
                    Toast.makeText(citas_empresa.this, "Cita agendada con éxito", Toast.LENGTH_SHORT).show();
                    actualizarContadorCitas();
                } else {
                    String errorMsg = "Error al agendar la cita: ";
                    if (response.errorBody() != null) {
                        try {
                            errorMsg += response.errorBody().string();
                            Log.e("citas_empresa", "Error detallado del servidor: " + errorMsg);
                            if (errorMsg.contains("ObjectOptimisticLockingFailureException")) {
                                if (retryCount < MAX_RETRIES - 1) {
                                    retryCount++;
                                    Log.e("citas_empresa", "Error de concurrencia detectado. Intentando nuevamente... (Intento " + (retryCount + 1) + " de " + MAX_RETRIES + ")");
                                    new Handler().postDelayed(() -> {
                                        Log.d("citas_empresa", "Reintentando agendar cita...");
                                        agendarCitaConUsuario(usuarioId);
                                    }, 2000);
                                    return;
                                } else {
                                    Log.e("citas_empresa", "Se alcanzó el número máximo de reintentos por error de concurrencia");
                                    Toast.makeText(citas_empresa.this, "No se pudo agendar la cita. Por favor, intente nuevamente en unos minutos.", Toast.LENGTH_LONG).show();
                                }
                            } else {
                                Log.e("citas_empresa", "Error del servidor no manejado: " + errorMsg);
                                Toast.makeText(citas_empresa.this, "Error al agendar la cita. Por favor, intente nuevamente.", Toast.LENGTH_LONG).show();
                            }
                        } catch (Exception e) {
                            errorMsg += response.code();
                            Log.e("citas_empresa", "Error al procesar respuesta del servidor: " + e.getMessage());
                            Log.e("citas_empresa", "Stack trace del error: ", e);
                        }
                    }
                }
                Intent intent = new Intent(citas_empresa.this, pantalla_principal.class);
intent.putExtra("userEmail", userEmail);
                intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_NEW_TASK);
                startActivity(intent);
                finish();
            }

            @Override
            public void onFailure(Call<Cita> call, Throwable t) {
                Log.e("citas_empresa", "Error de conexión al intentar agendar la cita: " + t.getMessage());
                Log.e("citas_empresa", "Stack trace completo: ", t);

                if (retryCount < MAX_RETRIES - 1) {
                    retryCount++;
                    Log.d("citas_empresa", "Reintentando después de error de conexión... (Intento " + (retryCount + 1) + " de " + MAX_RETRIES + ")");
                    new Handler().postDelayed(() -> {
                        agendarCitaConUsuario(usuarioId);
                    }, 3000);
                } else {
                    Log.e("citas_empresa", "Se alcanzó el número máximo de reintentos después de errores de conexión");
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
                    Toast.makeText(citas_empresa.this, mensajeError, Toast.LENGTH_LONG).show();
                }
                Intent intent = new Intent(citas_empresa.this, pantalla_principal.class);
intent.putExtra("userEmail", userEmail);
                intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_NEW_TASK);
                startActivity(intent);
                finish();
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
                !TextUtils.isEmpty(etNumeroCasa.getText());
    }
    
    /**
     * Envía un correo electrónico de confirmación al usuario
     * @param cita Objeto Cita con la información de la cita agendada
     */
    private void enviarCorreoConfirmacion(Cita cita) {
        Log.d("citas_empresa", "Enviando correo de confirmación a: " + cita.getEmail());
        
        EmailSender.enviarConfirmacionCita(this, cita, new EmailSender.EmailCallback() {
            @Override
            public void onSuccess() {
                Log.d("citas_empresa", "Correo enviado con éxito");
                runOnUiThread(() -> {
    Toast.makeText(citas_empresa.this, "Se ha enviado un correo de confirmación", Toast.LENGTH_SHORT).show();
    Intent intent = new Intent(citas_empresa.this, pantalla_principal.class);
intent.putExtra("userEmail", userEmail);
    intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_NEW_TASK);
    startActivity(intent);
    finish();
});
            }

            @Override
            public void onError(String error) {
                Log.e("citas_empresa", "Error al enviar correo: " + error);
                runOnUiThread(() -> {
    Toast.makeText(citas_empresa.this, "La cita se ha registrado, pero no se pudo enviar el correo de confirmación", Toast.LENGTH_LONG).show();
    Intent intent = new Intent(citas_empresa.this, pantalla_principal.class);
intent.putExtra("userEmail", userEmail);
    intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_NEW_TASK);
    startActivity(intent);
    finish();
});
            }
        });
    }
}