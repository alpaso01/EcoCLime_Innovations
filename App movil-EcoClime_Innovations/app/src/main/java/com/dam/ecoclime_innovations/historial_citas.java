package com.dam.ecoclime_innovations;

import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.os.Handler;
import android.os.Looper;
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
    private int userId;
    private Button btnTodos, btnEmpresas, btnParticulares;
    private final Handler mainHandler = new Handler(Looper.getMainLooper());
    private boolean isLoading = false;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_historial_citas);

        // Inicializar vistas
        recyclerView = findViewById(R.id.recyclerViewCitas);
        btnTodos = findViewById(R.id.btnTodos);
        btnEmpresas = findViewById(R.id.btnEmpresas);
        btnParticulares = findViewById(R.id.btnParticulares);

        // Obtener el email del intent
        userEmail = getIntent().getStringExtra("userEmail");
        if (userEmail == null || userEmail.isEmpty()) {
            Toast.makeText(this, "No se pudo obtener el email del usuario", Toast.LENGTH_SHORT).show();
            finish();
            return;
        }

        Log.d(TAG, "userEmail obtenido: " + userEmail);

        // Configuración del RecyclerView
        recyclerView.setLayoutManager(new LinearLayoutManager(this));
        recyclerView.setHasFixedSize(true);
        citaAdapter = new CitaAdapter(listaCitas, this);
        recyclerView.setAdapter(citaAdapter);

        // Inicializar Retrofit
        apiService = RetrofitClient.getRetrofitInstance().create(ApiService.class);

        // Configurar listeners de los botones
        configurarBotones();

        // Cargar citas
        cargarCitas();

        // Configurar botón volver
        Button btnVolver = findViewById(R.id.botonAtrasHistorial);
        btnVolver.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                finish();
            }
        });
    }

    private void configurarBotones() {
        btnTodos.setOnClickListener(v -> {
            actualizarFiltro("Todos");
            mostrarCitasFiltradas("Todos");
        });

        btnEmpresas.setOnClickListener(v -> {
            actualizarFiltro("Empresa");
            mostrarCitasFiltradas("Empresa");
        });

        btnParticulares.setOnClickListener(v -> {
            actualizarFiltro("Particular");
            mostrarCitasFiltradas("Particular");
        });
    }

    private void actualizarFiltro(String tipo) {
        // Actualizar el estado visual de los botones
        btnTodos.setBackgroundTintList(getColorStateList(R.color.white));
        btnEmpresas.setBackgroundTintList(getColorStateList(R.color.white));
        btnParticulares.setBackgroundTintList(getColorStateList(R.color.white));

        switch (tipo) {
            case "Todos":
                btnTodos.setBackgroundTintList(getColorStateList(R.color.light_gray));
                break;
            case "Empresa":
                btnEmpresas.setBackgroundTintList(getColorStateList(R.color.light_gray));
                break;
            case "Particular":
                btnParticulares.setBackgroundTintList(getColorStateList(R.color.light_gray));
                break;
        }
    }

    private void mostrarCitasFiltradas(String tipo) {
        List<Cita> citasFiltradas = new ArrayList<>();
        
        if (tipo.equals("Todos")) {
            citasFiltradas.addAll(listaCitas);
        } else {
            for (Cita cita : listaCitas) {
                if (cita.getTipo().equals(tipo)) {
                    citasFiltradas.add(cita);
                }
            }
        }
        
        citaAdapter.actualizarCitas(citasFiltradas);
    }

    private void cargarCitas() {
        if (isLoading) return;
        isLoading = true;
        
        apiService.obtenerUsuarioPorEmail(userEmail).enqueue(new Callback<Usuario>() {
            @Override
            public void onResponse(Call<Usuario> call, Response<Usuario> response) {
                if (response.isSuccessful() && response.body() != null) {
                    Usuario usuario = response.body();
                    userId = usuario.getId();
                    obtenerCitasUsuario(userId);
                } else {
                    Log.e(TAG, "Error al obtener usuario: " + response.code());
                    Toast.makeText(historial_citas.this, "Error al cargar datos del usuario", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(Call<Usuario> call, Throwable t) {
                Log.e(TAG, "Error de conexión: " + t.getMessage());
                Toast.makeText(historial_citas.this, "Error de conexión", Toast.LENGTH_SHORT).show();
            }
        });
    }

    private void obtenerCitasUsuario(int usuarioId) {
        Log.d(TAG, "Obteniendo citas para usuario ID: " + usuarioId);
        apiService.obtenerHistorialCitas(usuarioId).enqueue(new Callback<List<Cita>>() {
            @Override
            public void onResponse(Call<List<Cita>> call, Response<List<Cita>> response) {
                isLoading = false;
                if (response.isSuccessful() && response.body() != null) {
                    List<Cita> nuevasCitas = response.body();
                    Log.d(TAG, "Respuesta del servidor - Número de citas: " + nuevasCitas.size());
                    
                    mainHandler.post(() -> {
                        listaCitas.clear();
                        listaCitas.addAll(nuevasCitas);
                        
                        // Procesar las citas para asegurar que fecha y hora estén correctamente establecidos
                        for (Cita cita : listaCitas) {
                            if (cita.getFechaHora() != null) {
                                String[] partes = cita.getFechaHora().split("T");
                                if (partes.length == 2) {
                                    cita.setFecha(partes[0]);
                                    cita.setHora(partes[1].substring(0, 5)); // Tomar solo HH:mm
                                }
                            }
                        }
                        
                        Log.d(TAG, "Citas procesadas - Tamaño final de la lista: " + listaCitas.size());
                        citaAdapter.actualizarCitas(listaCitas);
                        recyclerView.post(() -> {
                            recyclerView.scrollToPosition(0);
                            actualizarFiltro("Todos");
                        });

                        // Actualizar el contador en SharedPreferences
                        SharedPreferences sharedPreferences = getSharedPreferences("UserPrefs", MODE_PRIVATE);
                        SharedPreferences.Editor editor = sharedPreferences.edit();
                        editor.putInt("citasCount", listaCitas.size());
                        editor.apply();

                        // Verificar si hay citas después de procesar
                        if (listaCitas.isEmpty()) {
                            Log.d(TAG, "No hay citas programadas");
                            Toast.makeText(historial_citas.this, "No tienes citas programadas", Toast.LENGTH_SHORT).show();
                        } else {
                            Log.d(TAG, "Mostrando " + listaCitas.size() + " citas");
                        }
                    });
                } else {
                    Log.e(TAG, "Error al obtener citas: " + response.code());
                    mainHandler.post(() -> 
                        Toast.makeText(historial_citas.this, "Error al cargar las citas", Toast.LENGTH_SHORT).show()
                    );
                }
            }

            @Override
            public void onFailure(Call<List<Cita>> call, Throwable t) {
                isLoading = false;
                Log.e(TAG, "Error de conexión: " + t.getMessage());
                mainHandler.post(() -> 
                    Toast.makeText(historial_citas.this, "Error de conexión", Toast.LENGTH_SHORT).show()
                );
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
                    // Actualizar la lista de citas usando el ID del usuario actual
                    obtenerCitasUsuario(userId);
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
        if (!isLoading) {
            cargarCitas();
        }
    }
}
