package com.dam.ecoclime_innovations;

import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.Toast;

import androidx.appcompat.app.AlertDialog;
import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import java.util.ArrayList;
import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class historial_citas extends AppCompatActivity implements CitaAdapter.OnCitaActionListener {
    private static final String TAG = "historial_citas";
    private RecyclerView recyclerView;
    private CitaAdapter citaAdapter;
    private List<Cita> listaCitas = new ArrayList<>();
    private ApiService apiService;
    private String userEmail;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_historial_citas);

        // Inicialización de Retrofit
        apiService = RetrofitClient.getRetrofitInstance().create(ApiService.class);

        // Obtener el email del intent
        userEmail = getIntent().getStringExtra("userEmail");
        if (userEmail == null || userEmail.isEmpty()) {
            Toast.makeText(this, "No se pudo obtener el email del usuario", Toast.LENGTH_SHORT).show();
            finish();
            return;
        }

        Log.d(TAG, "userEmail obtenido: " + userEmail);

        // Configuración del RecyclerView
        recyclerView = findViewById(R.id.recyclerViewCitas);
        recyclerView.setLayoutManager(new LinearLayoutManager(this));
        citaAdapter = new CitaAdapter(listaCitas, this);
        recyclerView.setAdapter(citaAdapter);

        // Configurar botón volver
        Button btnVolver = findViewById(R.id.botonAtrasHistorial);
        btnVolver.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                finish();
            }
        });

        // Obtener el historial de citas
        obtenerHistorialCitas();
    }

    private void obtenerHistorialCitas() {
        Log.d(TAG, "Obteniendo historial de citas para: " + userEmail);

        // Mostrar un mensaje de carga
        Toast.makeText(this, "Cargando citas...", Toast.LENGTH_SHORT).show();

        Call<List<Cita>> call = apiService.obtenerHistorialCitasPorEmail(userEmail);
        call.enqueue(new Callback<List<Cita>>() {
            @Override
            public void onResponse(Call<List<Cita>> call, Response<List<Cita>> response) {
                if (response.isSuccessful() && response.body() != null) {
                    listaCitas.clear();
                    listaCitas.addAll(response.body());
                    citaAdapter.notifyDataSetChanged();

                    Log.d(TAG, "Citas obtenidas: " + listaCitas.size());

                    // Actualizar el contador en SharedPreferences
                    SharedPreferences sharedPreferences = getSharedPreferences("UserPrefs", MODE_PRIVATE);
                    SharedPreferences.Editor editor = sharedPreferences.edit();
                    editor.putInt("citasCount", listaCitas.size());
                    editor.apply();

                    if (listaCitas.isEmpty()) {
                        Toast.makeText(historial_citas.this, "No tienes citas programadas", Toast.LENGTH_SHORT).show();
                    }
                } else {
                    Log.e(TAG, "Error en la respuesta: " + response.code());
                    String error = "Error al obtener las citas: ";
                    if (response.errorBody() != null) {
                        try {
                            error += response.errorBody().string();
                        } catch (Exception e) {
                            error += response.code();
                        }
                    }
                    Log.e(TAG, error);
                    Toast.makeText(historial_citas.this, "Error al obtener las citas", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(Call<List<Cita>> call, Throwable t) {
                Log.e(TAG, "Error en la llamada: " + t.getMessage());
                Toast.makeText(historial_citas.this, "Error de conexión", Toast.LENGTH_SHORT).show();
            }
        });
    }

    @Override
    public void onModificarClick(Cita cita) {
        // Iniciar actividad para modificar la cita
        Intent intent = new Intent(this, ModificarCitaActivity.class);
        intent.putExtra("citaId", cita.getId());
        intent.putExtra("userEmail", userEmail);
        startActivity(intent);
    }

    @Override
    public void onEliminarClick(final Cita cita) {
        // Mostrar diálogo de confirmación
        new AlertDialog.Builder(this)
                .setTitle("Confirmar eliminación")
                .setMessage("¿Estás seguro de que deseas eliminar esta cita?")
                .setPositiveButton("Sí", new DialogInterface.OnClickListener() {
                    @Override
                    public void onClick(DialogInterface dialog, int which) {
                        eliminarCita(cita.getId());
                    }
                })
                .setNegativeButton("No", null)
                .show();
    }

    private void eliminarCita(int citaId) {
        Call<Void> call = apiService.eliminarCita(citaId);
        call.enqueue(new Callback<Void>() {
            @Override
            public void onResponse(Call<Void> call, Response<Void> response) {
                if (response.isSuccessful()) {
                    Toast.makeText(historial_citas.this, "Cita eliminada correctamente", Toast.LENGTH_SHORT).show();
                    // Actualizar la lista de citas
                    obtenerHistorialCitas();
                } else {
                    Log.e(TAG, "Error al eliminar la cita: " + response.code());
                    Toast.makeText(historial_citas.this, "Error al eliminar la cita", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(Call<Void> call, Throwable t) {
                Log.e(TAG, "Error en la llamada: " + t.getMessage());
                Toast.makeText(historial_citas.this, "Error de conexión", Toast.LENGTH_SHORT).show();
            }
        });
    }

    @Override
    protected void onResume() {
        super.onResume();
        // Actualizar la lista de citas al volver a la actividad
        obtenerHistorialCitas();
        Log.d(TAG, "onResume - Actualizando historial de citas");
    }
}
