package com.dam.ecoclime_innovations;

import android.app.AlertDialog;
import android.content.Intent;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.*;
import androidx.appcompat.app.AppCompatActivity;

import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Locale;

public class citas_particulares extends AppCompatActivity {

    private CalendarView calendarView;
    private String selectedDate;
    private String selectedTime;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_citas_particulares);

        calendarView = findViewById(R.id.calendarioCitas);
        Button botonAtrasParticulares = findViewById(R.id.botonAtrasParticulares);

        calendarView.setOnDateChangeListener((view, year, month, dayOfMonth) -> {
            Calendar selectedCalendar = Calendar.getInstance();
            selectedCalendar.set(year, month, dayOfMonth);
            int dayOfWeek = selectedCalendar.get(Calendar.DAY_OF_WEEK);

            if (dayOfWeek == Calendar.SATURDAY || dayOfWeek == Calendar.SUNDAY) {
                Toast.makeText(this, "Solo se permiten citas de lunes a viernes", Toast.LENGTH_SHORT).show();
            } else {
                selectedDate = new SimpleDateFormat("dd/MM/yyyy", Locale.getDefault()).format(selectedCalendar.getTime());
                mostrarSeleccionHoras();
            }
        });

        botonAtrasParticulares.setOnClickListener(view -> {
            Intent intent = new Intent(citas_particulares.this, pantalla_principal.class);
            startActivity(intent);
        });
    }

    private void mostrarSeleccionHoras() {
        String[] horasDisponibles = {"07:00 - 09:00", "09:00 - 11:00", "11:00 - 13:00", "13:00 - 15:00", "15:00 - 17:00", "17:00 - 19:00"};
        AlertDialog.Builder builder = new AlertDialog.Builder(this);
        builder.setTitle("Selecciona una hora")
                .setItems(horasDisponibles, (dialog, which) -> {
                    selectedTime = horasDisponibles[which];
                    mostrarFormulario();
                })
                .setNegativeButton("Cancelar", null)
                .show();
    }

    private void mostrarFormulario() {
        AlertDialog.Builder builder = new AlertDialog.Builder(this);
        LayoutInflater inflater = getLayoutInflater();
        View dialogView = inflater.inflate(R.layout.activity_citas_particulares, null);
        builder.setView(dialogView);

        EditText email = dialogView.findViewById(R.id.email);
        EditText telefono = dialogView.findViewById(R.id.telefono);
        EditText ciudad = dialogView.findViewById(R.id.ciudad);
        EditText codigoPostal = dialogView.findViewById(R.id.codigoPostal);
        EditText calle = dialogView.findViewById(R.id.calle);
        EditText numero = dialogView.findViewById(R.id.numero);
        EditText mensaje = dialogView.findViewById(R.id.mensaje);
        Button confirmar = dialogView.findViewById(R.id.confirmar);

        AlertDialog dialog = builder.create();
        confirmar.setOnClickListener(v -> {
            String infoCita = "Cita confirmada para " + selectedDate + " a las " + selectedTime + "\n" +
                    "Email: " + email.getText().toString() + "\n" +
                    "Teléfono: " + telefono.getText().toString() + "\n" +
                    "Ciudad: " + ciudad.getText().toString() + "\n" +
                    "C.P.: " + codigoPostal.getText().toString() + "\n" +
                    "Dirección: " + calle.getText().toString() + " Nº: " + numero.getText().toString() + "\n" +
                    "Mensaje: " + mensaje.getText().toString();

            enviarConfirmacion(email.getText().toString(), infoCita);
            Toast.makeText(this, "Cita confirmada", Toast.LENGTH_LONG).show();
            dialog.dismiss();
        });

        dialog.show();
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