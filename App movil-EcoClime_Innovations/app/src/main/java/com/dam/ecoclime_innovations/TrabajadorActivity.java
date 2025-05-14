package com.dam.ecoclime_innovations;

import android.app.DatePickerDialog;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.TextView;
import android.widget.Toast;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import androidx.swiperefreshlayout.widget.SwipeRefreshLayout;
import java.util.Calendar;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.List;
import java.util.Locale;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class TrabajadorActivity extends BaseActivity {
    private TextView welcomeText;
    private Button btnCalendario;
    private Button btnTodos;
    private Button btnEmpresa;
    private Button btnParticulares;
    private RecyclerView citasRecyclerView;
    private SwipeRefreshLayout swipeRefreshLayout;
    private Date selectedDate;
    private SimpleDateFormat dateFormat;
    private CitasTrabajadorAdapter adapter;
    private ApiService apiService;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_trabajador);

        initializeComponents();
        setupRecyclerView();
        setupButtons();
        setupSwipeRefresh();
        initRetrofit();

        // Establecer la fecha actual como fecha seleccionada
        selectedDate = new Date();
        dateFormat = new SimpleDateFormat("dd/MM/yyyy", Locale.getDefault());

        // Cargar citas del día actual
        loadAppointments();
    }

    private void initializeComponents() {
        welcomeText = findViewById(R.id.welcomeText);
        btnCalendario = findViewById(R.id.btnCalendario);
        btnTodos = findViewById(R.id.btnTodos);
        btnEmpresa = findViewById(R.id.btnEmpresa);
        btnParticulares = findViewById(R.id.btnParticulares);
        citasRecyclerView = findViewById(R.id.citasRecyclerView);
        swipeRefreshLayout = findViewById(R.id.swipeRefreshLayout);

        // Configurar texto de bienvenida
        String email = getIntent().getStringExtra("userEmail");
        welcomeText.setText("Bienvenid@ " + email);
    }

    private void initRetrofit() {
        apiService = RetrofitClient.getRetrofitInstance().create(ApiService.class);
    }

    private void setupRecyclerView() {
        adapter = new CitasTrabajadorAdapter();
        citasRecyclerView.setLayoutManager(new LinearLayoutManager(this));
        citasRecyclerView.setAdapter(adapter);
    }

    private void setupButtons() {
        btnCalendario.setOnClickListener(v -> showDatePicker());

        btnTodos.setOnClickListener(v -> {
            btnTodos.setSelected(true);
            btnEmpresa.setSelected(false);
            btnParticulares.setSelected(false);
            loadAppointments();
        });

        btnEmpresa.setOnClickListener(v -> {
            btnTodos.setSelected(false);
            btnEmpresa.setSelected(true);
            btnParticulares.setSelected(false);
            loadAppointments("empresa");
        });

        btnParticulares.setOnClickListener(v -> {
            btnTodos.setSelected(false);
            btnEmpresa.setSelected(false);
            btnParticulares.setSelected(true);
            loadAppointments("particular");
        });
    }

    private void setupSwipeRefresh() {
        swipeRefreshLayout.setOnRefreshListener(this::loadAppointments);
        swipeRefreshLayout.setColorSchemeResources(
                android.R.color.holo_blue_bright,
                android.R.color.holo_green_light,
                android.R.color.holo_orange_light,
                android.R.color.holo_red_light
        );
    }

    private void showDatePicker() {
        Calendar calendar = Calendar.getInstance();
        calendar.setTime(selectedDate);

        DatePickerDialog datePickerDialog = new DatePickerDialog(
                this,
                (view, year, month, dayOfMonth) -> {
                    Calendar newDate = Calendar.getInstance();
                    newDate.set(year, month, dayOfMonth);
                    selectedDate = newDate.getTime();
                    loadAppointments();
                },
                calendar.get(Calendar.YEAR),
                calendar.get(Calendar.MONTH),
                calendar.get(Calendar.DAY_OF_MONTH)
        );

        datePickerDialog.show();
    }

    private void loadAppointments() {
        loadAppointments(null);
    }

    private void loadAppointments(String tipo) {
        swipeRefreshLayout.setRefreshing(true);
        String fecha = dateFormat.format(selectedDate);

        Call<List<Cita>> call;
        if (tipo != null) {
            call = apiService.obtenerCitasPorFechaYTipo(fecha, tipo);
        } else {
            call = apiService.obtenerCitasPorFecha(fecha);
        }

        call.enqueue(new Callback<List<Cita>>() {
            @Override
            public void onResponse(Call<List<Cita>> call, Response<List<Cita>> response) {
                swipeRefreshLayout.setRefreshing(false);
                if (response.isSuccessful() && response.body() != null) {
                    adapter.setCitas(response.body());
                } else {
                    Toast.makeText(TrabajadorActivity.this,
                            "Error al cargar las citas", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(Call<List<Cita>> call, Throwable t) {
                swipeRefreshLayout.setRefreshing(false);
                Toast.makeText(TrabajadorActivity.this,
                        "Error de conexión", Toast.LENGTH_SHORT).show();
            }
        });
    }

    @Override
    protected int getSelectedNavigationItemId() {
        return R.id.navigation_home;
    }
}