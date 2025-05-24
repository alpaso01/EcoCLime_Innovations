package com.dam.ecoclime_innovations;

import android.app.DatePickerDialog;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.TextView;
import android.util.Log;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Calendar;
import java.util.Date;
import java.util.HashMap;
import java.util.List;
import java.util.Locale;
import java.util.Map;

import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class TrabajadorActivity extends AppCompatActivity implements CitaTrabajadorAdapter.OnCitaActionListener {
    private static final String TAG = "TrabajadorActivity";
    private TextView welcomeText, tvFechaSeleccionada;
    private Button btnCalendario, btnTodos, btnEmpresa, btnParticulares, btnAtras;
    private RecyclerView citasRecyclerView;
    private SwipeRefreshLayout swipeRefreshLayout;
    private Date selectedDate;
    private SimpleDateFormat dateFormat;
    private CitaTrabajadorAdapter adapter;
    private ApiService apiService;
    private String filtroActual = "todos";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_trabajador);

        initializeComponents();
        // Configurar RecyclerView
        Log.d(TAG, "Configurando RecyclerView...");
        setupRecyclerView();
        Log.d(TAG, "RecyclerView configurado");
        setupButtons();
        setupSwipeRefresh();
        initRetrofit();

        // Establecer la fecha actual como fecha seleccionada
        selectedDate = new Date();
        dateFormat = new SimpleDateFormat("yyyy-MM-dd", Locale.getDefault()); // Formato para la API
        
        // Actualizar la interfaz con la fecha actual
        actualizarFechaEnUI();

        // Cargar citas del día actual
        cargarCitas();
    }

    private void initializeComponents() {
        welcomeText = findViewById(R.id.welcomeText);
        tvFechaSeleccionada = findViewById(R.id.tvFechaSeleccionada);
        btnCalendario = findViewById(R.id.btnCalendario);
        btnTodos = findViewById(R.id.btnTodos);
        btnEmpresa = findViewById(R.id.btnEmpresa);
        btnParticulares = findViewById(R.id.btnParticulares);
        btnAtras = findViewById(R.id.btnAtras);
        citasRecyclerView = findViewById(R.id.citasRecyclerView);
        swipeRefreshLayout = findViewById(R.id.swipeRefreshLayout);

        // Configurar texto de bienvenida
        String email = getIntent().getStringExtra("userEmail");
        if (email != null && !email.isEmpty()) {
            welcomeText.setText("Bienvenid@ " + email);
        }
    }

    private void initRetrofit() {
        apiService = RetrofitClient.getRetrofitInstance().create(ApiService.class);
    }
    
    private void actualizarFechaEnUI() {
        SimpleDateFormat uiDateFormat = new SimpleDateFormat("EEEE, d 'de' MMMM 'de' yyyy", new Locale("es", "ES"));
        String fechaFormateada = uiDateFormat.format(selectedDate);
        // Capitalizar la primera letra
        if (!fechaFormateada.isEmpty()) {
            fechaFormateada = fechaFormateada.substring(0, 1).toUpperCase() + fechaFormateada.substring(1);
        }
        tvFechaSeleccionada.setText(fechaFormateada);
    }

    private void setupRecyclerView() {
        adapter = new CitaTrabajadorAdapter(new ArrayList<>(), this, this);
        citasRecyclerView.setLayoutManager(new LinearLayoutManager(this));
        citasRecyclerView.setAdapter(adapter);
    }

    private void setupButtons() {
        btnCalendario.setOnClickListener(v -> mostrarSelectorFecha());

        btnTodos.setOnClickListener(v -> {
            setFiltroActivo(btnTodos);
            filtroActual = "todos";
            cargarCitas();
        });

        btnEmpresa.setOnClickListener(v -> {
            setFiltroActivo(btnEmpresa);
            filtroActual = "empresa";
            cargarCitas();
        });

        btnParticulares.setOnClickListener(v -> {
            setFiltroActivo(btnParticulares);
            filtroActual = "particular";
            cargarCitas();
        });
        
        btnAtras.setOnClickListener(v -> {
            Intent intent = new Intent(TrabajadorActivity.this, LoginActivity.class);
            intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_NEW_TASK);
            startActivity(intent);
            finish();
        });
    }
    
    private void setFiltroActivo(Button botonActivo) {
        // Resetear todos los botones a estado inactivo
        btnTodos.setBackground(getResources().getDrawable(R.drawable.bg_filtro_pill_inactive));
        btnEmpresa.setBackground(getResources().getDrawable(R.drawable.bg_filtro_pill_inactive));
        btnParticulares.setBackground(getResources().getDrawable(R.drawable.bg_filtro_pill_inactive));

        // Mantener el texto en blanco para todos los botones
        btnTodos.setTextColor(getResources().getColor(android.R.color.white));
        btnEmpresa.setTextColor(getResources().getColor(android.R.color.white));
        btnParticulares.setTextColor(getResources().getColor(android.R.color.white));

        // Activar el botón seleccionado
        botonActivo.setBackground(getResources().getDrawable(R.drawable.bg_filtro_pill));
    }

    private void setupSwipeRefresh() {
        swipeRefreshLayout.setOnRefreshListener(this::cargarCitas);
        swipeRefreshLayout.setColorSchemeResources(
                android.R.color.holo_blue_bright,
                android.R.color.holo_green_light,
                android.R.color.holo_orange_light,
                android.R.color.holo_red_light
        );
    }

    private void mostrarSelectorFecha() {
        final Calendar calendar = Calendar.getInstance();
        calendar.setTime(selectedDate);
        
        int year = calendar.get(Calendar.YEAR);
        int month = calendar.get(Calendar.MONTH);
        int day = calendar.get(Calendar.DAY_OF_MONTH);

        DatePickerDialog datePickerDialog = new DatePickerDialog(
                this,
                (view, selectedYear, selectedMonth, selectedDay) -> {
                    Calendar newDate = Calendar.getInstance();
                    newDate.set(selectedYear, selectedMonth, selectedDay);
                    selectedDate = newDate.getTime();
                    actualizarFechaEnUI();
                    cargarCitas();
                },
                year, month, day);

        // Establecer la fecha mínima como hoy
        datePickerDialog.getDatePicker().setMinDate(System.currentTimeMillis() - 1000);
        datePickerDialog.show();
    }

    private void cargarCitas() {
        Log.d(TAG, "cargarCitas: Iniciando carga de citas");
        swipeRefreshLayout.setRefreshing(true);
        String fecha = dateFormat.format(selectedDate);
        Log.d(TAG, "cargarCitas: Fecha seleccionada: " + fecha + ", Filtro: " + filtroActual);
        
        Call<List<Cita>> call;

        if (filtroActual.equals("todos")) {
            Log.d(TAG, "cargarCitas: Obteniendo todas las citas para la fecha: " + fecha);
            call = apiService.obtenerCitasPorFecha(fecha);
        } else {
            Log.d(TAG, "cargarCitas: Obteniendo citas para fecha: " + fecha + " y tipo: " + filtroActual);
            call = apiService.obtenerCitasPorFechaYTipo(fecha, filtroActual);
        }

        call.enqueue(new Callback<List<Cita>>() {
            @Override
            public void onResponse(Call<List<Cita>> call, Response<List<Cita>> response) {
                swipeRefreshLayout.setRefreshing(false);
                Log.d(TAG, "onResponse: Respuesta recibida. Código: " + response.code());
                
                if (response.isSuccessful() && response.body() != null) {
                    List<Cita> citas = response.body();
                    Log.d(TAG, "onResponse: Número de citas recibidas: " + citas.size());
                    
                    if (!citas.isEmpty()) {
                        for (Cita cita : citas) {
                            Log.d(TAG, "Cita: " + cita.toString());
                        }
                    } else {
                        Log.d(TAG, "onResponse: La lista de citas está vacía");
                    }
                    
                    if (adapter != null) {
                        adapter.actualizarCitas(citas);
                        Log.d(TAG, "onResponse: Adaptador actualizado con " + citas.size() + " citas");
                    } else {
                        Log.e(TAG, "onResponse: El adaptador es nulo");
                    }
                    
                    if (citas.isEmpty()) {
                        Toast.makeText(TrabajadorActivity.this, 
                            "No hay citas para la fecha seleccionada", 
                            Toast.LENGTH_SHORT).show();
                    }
                } else {
                    String errorMsg = "Error al cargar las citas: " + response.code();
                    Log.e(TAG, errorMsg);
                    if (response.errorBody() != null) {
                        try {
                            errorMsg += " - " + response.errorBody().string();
                            Log.e(TAG, "Error body: " + errorMsg);
                        } catch (Exception e) {
                            Log.e(TAG, "Error al leer el cuerpo del error", e);
                        }
                    }
                    Toast.makeText(TrabajadorActivity.this, errorMsg, Toast.LENGTH_LONG).show();
                }
            }

            @Override
            public void onFailure(Call<List<Cita>> call, Throwable t) {
                swipeRefreshLayout.setRefreshing(false);
                Log.e(TAG, "onFailure: Error en la petición", t);
                String errorMsg = "Error de conexión: " + (t.getMessage() != null ? t.getMessage() : "Error desconocido");
                Log.e(TAG, errorMsg);
                Toast.makeText(TrabajadorActivity.this, errorMsg, Toast.LENGTH_LONG).show();
            }
        });
    }
    
    @Override
    public void onModificarClick(Cita cita) {
        // Implementar lógica de modificación si es necesario
        Toast.makeText(this, "Modificar cita: " + cita.getId(), Toast.LENGTH_SHORT).show();
    }

    @Override
    public void onEliminarClick(Cita cita) {
        // Implementar lógica de eliminación si es necesario
        Toast.makeText(this, "Eliminar cita: " + cita.getId(), Toast.LENGTH_SHORT).show();
    }
    
    @Override
    public void onEstadoCambiado(Cita cita, String nuevoEstado) {
        // Actualizar el estado en el servidor
        Map<String, String> estadoMap = new HashMap<>();
        estadoMap.put("estado", nuevoEstado.toLowerCase());
        
        apiService.actualizarEstadoCita(cita.getId(), estadoMap).enqueue(new Callback<Void>() {
            @Override
            public void onResponse(Call<Void> call, Response<Void> response) {
                if (response.isSuccessful()) {
                    // Actualizar la cita localmente
                    cita.setEstado(nuevoEstado.toLowerCase());
                    adapter.notifyDataSetChanged();
                    Toast.makeText(TrabajadorActivity.this, 
                        "Estado actualizado a: " + nuevoEstado, 
                        Toast.LENGTH_SHORT).show();
                } else {
                    Toast.makeText(TrabajadorActivity.this, 
                        "Error al actualizar el estado: " + response.code(), 
                        Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(Call<Void> call, Throwable t) {
                Toast.makeText(TrabajadorActivity.this, 
                    "Error de conexión: " + t.getMessage(), 
                    Toast.LENGTH_SHORT).show();
            }
        });
    }
}