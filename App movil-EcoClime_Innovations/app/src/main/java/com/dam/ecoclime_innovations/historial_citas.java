package com.dam.ecoclime_innovations;

import android.content.DialogInterface;
import android.content.Intent;
import android.os.Bundle;
import android.os.Handler;
import android.os.Looper;
import android.util.Log;
import android.widget.Button;
import android.widget.Toast;
import androidx.appcompat.app.AlertDialog;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import java.util.ArrayList;
import java.util.List;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class historial_citas extends BaseActivity implements CitaAdapter.OnCitaActionListener {
    private static final String TAG = "historial_citas";
    private RecyclerView recyclerView;
    private CitaAdapter citaAdapter;
    private List<Cita> listaCitas = new ArrayList<>();
    private ApiService apiService;
    private Button btnTodos, btnEmpresas, btnParticulares;
    private final Handler mainHandler = new Handler(Looper.getMainLooper());
    private boolean isLoading = false;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_historial_citas);

        if (userEmail == null || userEmail.isEmpty()) {
            Toast.makeText(this, "No se pudo obtener el email del usuario", Toast.LENGTH_SHORT).show();
            finish();
            return;
        }

        Log.d(TAG, "userEmail obtenido: " + userEmail);

        // Inicializar vistas y configuración
        recyclerView = findViewById(R.id.recyclerViewCitas);
        recyclerView.setLayoutManager(new LinearLayoutManager(this));
        recyclerView.setHasFixedSize(true);
        citaAdapter = new CitaAdapter(listaCitas, this);
        recyclerView.setAdapter(citaAdapter);

        // Configurar filtros
        setupFiltros();

        // Inicializar Retrofit
        apiService = RetrofitClient.getRetrofitInstance().create(ApiService.class);

        // Cargar citas
        cargarCitas();

        // Configurar navegación inferior
        setupBottomNavigation();
    }

    @Override
    protected int getSelectedNavigationItemId() {
        return R.id.navigation_home;
    }

    private void setupFiltros() {
        btnTodos = findViewById(R.id.btnTodos);
        btnEmpresas = findViewById(R.id.btnEmpresas);
        btnParticulares = findViewById(R.id.btnParticulares);

        // Establecer colores iniciales
        btnTodos.setBackground(getResources().getDrawable(R.drawable.bg_filtro_pill));
        btnEmpresas.setBackground(getResources().getDrawable(R.drawable.bg_filtro_pill_inactive));
        btnParticulares.setBackground(getResources().getDrawable(R.drawable.bg_filtro_pill_inactive));
        
        btnTodos.setTextColor(getResources().getColor(android.R.color.white));
        btnEmpresas.setTextColor(getResources().getColor(android.R.color.white));
        btnParticulares.setTextColor(getResources().getColor(android.R.color.white));

        btnTodos.setOnClickListener(v -> {
            Log.d(TAG, "Botón TODOS clickeado");
            setFiltroActivo(btnTodos);
            filtrarCitas("todos");
        });

        btnEmpresas.setOnClickListener(v -> {
            Log.d(TAG, "Botón EMPRESAS clickeado");
            setFiltroActivo(btnEmpresas);
            filtrarCitas("Empresa");
        });

        btnParticulares.setOnClickListener(v -> {
            Log.d(TAG, "Botón PARTICULARES clickeado");
            setFiltroActivo(btnParticulares);
            filtrarCitas("Particular");
        });
    }

    private void setFiltroActivo(Button botonActivo) {
        // Resetear todos los botones a estado inactivo
        btnTodos.setBackground(getResources().getDrawable(R.drawable.bg_filtro_pill_inactive));
        btnEmpresas.setBackground(getResources().getDrawable(R.drawable.bg_filtro_pill_inactive));
        btnParticulares.setBackground(getResources().getDrawable(R.drawable.bg_filtro_pill_inactive));

        // Mantener el texto en blanco para todos los botones
        btnTodos.setTextColor(getResources().getColor(android.R.color.white));
        btnEmpresas.setTextColor(getResources().getColor(android.R.color.white));
        btnParticulares.setTextColor(getResources().getColor(android.R.color.white));

        // Activar el botón seleccionado
        botonActivo.setBackground(getResources().getDrawable(R.drawable.bg_filtro_pill));
    }

    private void filtrarCitas(String tipo) {
        Log.d(TAG, "Filtrando citas por tipo: " + tipo);
        Log.d(TAG, "Total de citas antes del filtrado: " + listaCitas.size());
        
        List<Cita> citasFiltradas = new ArrayList<>();
        
        if (tipo.equalsIgnoreCase("todos")) {
            citasFiltradas.addAll(listaCitas);
            Log.d(TAG, "Mostrando todas las citas: " + listaCitas.size());
        } else {
            for (Cita cita : listaCitas) {
                Log.d(TAG, "Comparando tipo de cita: " + cita.getTipo() + " con filtro: " + tipo);
                if (cita.getTipo().equalsIgnoreCase(tipo)) {
                    citasFiltradas.add(cita);
                }
            }
            Log.d(TAG, "Citas filtradas encontradas: " + citasFiltradas.size());
        }
        
        citaAdapter.actualizarCitas(citasFiltradas);
        
        if (citasFiltradas.isEmpty()) {
            Toast.makeText(this, "No hay citas de tipo " + tipo, Toast.LENGTH_SHORT).show();
        }
    }

    private void cargarCitas() {
        if (isLoading) return;
        isLoading = true;
        
        Call<List<Cita>> call = apiService.obtenerHistorialCitasPorEmail(userEmail);
        call.enqueue(new Callback<List<Cita>>() {
            @Override
            public void onResponse(Call<List<Cita>> call, Response<List<Cita>> response) {
                isLoading = false;
                if (response.isSuccessful() && response.body() != null) {
                    List<Cita> nuevasCitas = response.body();
                    Log.d(TAG, "Citas obtenidas: " + nuevasCitas.size());
                    
                    listaCitas.clear();
                    listaCitas.addAll(nuevasCitas);
                    citaAdapter.actualizarCitas(listaCitas);
                    
                    if (listaCitas.isEmpty()) {
                        Toast.makeText(historial_citas.this, "No tienes citas programadas", Toast.LENGTH_SHORT).show();
                    }
                } else {
                    Toast.makeText(historial_citas.this, "Error al cargar las citas", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(Call<List<Cita>> call, Throwable t) {
                isLoading = false;
                Toast.makeText(historial_citas.this, "Error de conexión", Toast.LENGTH_SHORT).show();
                Log.e(TAG, "Error: " + t.getMessage());
            }
        });
    }

    @Override
    public void onModificarClick(Cita cita) {
        Log.d(TAG, "Iniciando modificación de cita ID: " + cita.getId());
        Intent intent = new Intent(this, ModificarCitaActivity.class);
        intent.putExtra("citaId", cita.getId());
        pasarDatosUsuario(intent);
        startActivity(intent);
    }

    @Override
    public void onEliminarClick(final Cita cita) {
        new AlertDialog.Builder(this)
                .setTitle("Confirmar eliminación")
                .setMessage("¿Estás seguro de que deseas eliminar esta cita?")
                .setPositiveButton("Sí", (dialog, which) -> eliminarCita(cita.getId()))
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
                    cargarCitas();
                } else {
                    Toast.makeText(historial_citas.this, "Error al eliminar la cita", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(Call<Void> call, Throwable t) {
                Toast.makeText(historial_citas.this, "Error de conexión", Toast.LENGTH_SHORT).show();
                Log.e(TAG, "Error: " + t.getMessage());
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
