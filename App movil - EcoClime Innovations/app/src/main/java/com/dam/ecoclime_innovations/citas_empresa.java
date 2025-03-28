package com.dam.ecoclime_innovations;

import android.app.AlertDialog;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.*;
import androidx.appcompat.app.AppCompatActivity;

import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Locale;

public class citas_empresa extends AppCompatActivity {

    private CalendarView calendarView;
    private Button seleccionarHora;
    private Button confirmar;
    private String selectedDate = null;
    private String selectedTime = null;

    private EditText nombreEmpresa, email, telefono, ciudad, codigoPostal, calle, numero, mensaje;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_citas_empresa);

        calendarView = findViewById(R.id.calendarioCitasEmpresa);
        seleccionarHora = findViewById(R.id.seleccionarHoraEmpresa);
        confirmar = findViewById(R.id.confirmarEmpresa);
        Button botonAtrasEmpresa = findViewById(R.id.botonAtrasEmpresa);

        nombreEmpresa = findViewById(R.id.nombreEmpresa);
        email = findViewById(R.id.emailEmpresa);
        telefono = findViewById(R.id.telefonoEmpresa);
        ciudad = findViewById(R.id.ciudadEmpresa);
        codigoPostal = findViewById(R.id.codigoPostalEmpresa);
        calle = findViewById(R.id.calleEmpresa);
        numero = findViewById(R.id.numeroEmpresa);
        mensaje = findViewById(R.id.mensajeEmpresa);

        seleccionarHora.setEnabled(false);
        confirmar.setEnabled(false);

        calendarView.setOnDateChangeListener((view, year, month, dayOfMonth) -> {
            Calendar selectedCalendar = Calendar.getInstance();
            selectedCalendar.set(year, month, dayOfMonth);
            int dayOfWeek = selectedCalendar.get(Calendar.DAY_OF_WEEK);

            if (dayOfWeek == Calendar.SATURDAY || dayOfWeek == Calendar.SUNDAY) {
                Toast.makeText(this, "Solo se permiten citas de lunes a viernes", Toast.LENGTH_SHORT).show();
                seleccionarHora.setEnabled(false);
            } else {
                selectedDate = new SimpleDateFormat("dd/MM/yyyy", Locale.getDefault()).format(selectedCalendar.getTime());
                seleccionarHora.setEnabled(true);
            }
        });

        seleccionarHora.setOnClickListener(v -> mostrarSeleccionHoras());

        confirmar.setOnClickListener(v -> {
            if (validarFormulario()) {
                String infoCita = "Cita confirmada para " + selectedDate + " a las " + selectedTime + "\n" +
                        "Nombre Empresa: " + nombreEmpresa.getText().toString() + "\n" +
                        "Email: " + email.getText().toString() + "\n" +
                        "Teléfono: " + telefono.getText().toString() + "\n" +
                        "Ciudad: " + ciudad.getText().toString() + "\n" +
                        "C.P.: " + codigoPostal.getText().toString() + "\n" +
                        "Dirección: " + calle.getText().toString() + " Nº: " + numero.getText().toString() + "\n" +
                        "Mensaje: " + mensaje.getText().toString();

                enviarConfirmacion(email.getText().toString(), infoCita);
                Toast.makeText(this, "Cita confirmada", Toast.LENGTH_LONG).show();
            } else {
                Toast.makeText(this, "Por favor, complete todos los campos", Toast.LENGTH_SHORT).show();
            }
        });

        botonAtrasEmpresa.setOnClickListener(view -> {
            Intent intent = new Intent(citas_empresa.this, pantalla_principal.class);
            startActivity(intent);
        });
    }

    private void mostrarSeleccionHoras() {
        String[] horasDisponibles = {"07:00 - 09:00", "09:00 - 11:00", "11:00 - 13:00", "13:00 - 15:00", "15:00 - 17:00", "17:00 - 19:00"};
        AlertDialog.Builder builder = new AlertDialog.Builder(this);
        builder.setTitle("Selecciona una hora")
                .setItems(horasDisponibles, (dialog, which) -> {
                    selectedTime = horasDisponibles[which];
                    confirmar.setEnabled(true);
                })
                .setNegativeButton("Cancelar", null)
                .show();
    }

    private boolean validarFormulario() {
        return !nombreEmpresa.getText().toString().trim().isEmpty() &&
                !email.getText().toString().trim().isEmpty() &&
                !telefono.getText().toString().trim().isEmpty() &&
                !ciudad.getText().toString().trim().isEmpty() &&
                !codigoPostal.getText().toString().trim().isEmpty() &&
                !calle.getText().toString().trim().isEmpty() &&
                !numero.getText().toString().trim().isEmpty();
    }

    private void enviarConfirmacion(String email, String mensaje) {
        Intent emailIntent = new Intent(Intent.ACTION_SEND);
        emailIntent.setType("message/rfc822");
        emailIntent.putExtra(Intent.EXTRA_EMAIL, new String[]{email});
        emailIntent.putExtra(Intent.EXTRA_SUBJECT, "Confirmación de Cita");
        emailIntent.putExtra(Intent.EXTRA_TEXT, mensaje);
        try {
            startActivity(Intent.createChooser(emailIntent, "Enviar correo..."));
        } catch (android.content.ActivityNotFoundException ex) {
            Toast.makeText(this, "No hay clientes de correo instalados.", Toast.LENGTH_SHORT).show();
        }
    }
}
