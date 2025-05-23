package com.dam.ecoclime_innovations;

import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.Toast;
import androidx.annotation.Nullable;
import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import java.util.ArrayList;
import java.util.List;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class HistorialUsuariosActivity extends AppCompatActivity {
    private RecyclerView recyclerUsuarios;
    private UsuarioAdapter usuarioAdapter;
    private ApiService apiService;
    private Button btnTodos, btnTrabajadores, btnAdmins, btnClientes;

    @Override
    protected void onCreate(@Nullable Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_historial_usuarios);

        recyclerUsuarios = findViewById(R.id.recyclerUsuarios);
        recyclerUsuarios.setLayoutManager(new LinearLayoutManager(this));
        usuarioAdapter = new UsuarioAdapter(new ArrayList<>());
        recyclerUsuarios.setAdapter(usuarioAdapter);

        apiService = RetrofitClient.getInstance().create(ApiService.class);

        btnTodos = findViewById(R.id.btnTodos);
        btnTrabajadores = findViewById(R.id.btnTrabajadores);
        btnAdmins = findViewById(R.id.btnAdmins);
        btnClientes = findViewById(R.id.btnClientes);

        btnTodos.setOnClickListener(v -> cargarTodos());
        btnTrabajadores.setOnClickListener(v -> cargarTrabajadores());
        btnAdmins.setOnClickListener(v -> cargarAdmins());
        btnClientes.setOnClickListener(v -> cargarClientes());

        cargarTodos(); // Cargar todos por defecto
    }

    private void cargarTodos() {
        apiService.obtenerTodosLosUsuarios().enqueue(new Callback<List<Usuario>>() {
            @Override
            public void onResponse(Call<List<Usuario>> call, Response<List<Usuario>> response) {
                if (response.isSuccessful() && response.body() != null) {
                    usuarioAdapter.actualizarUsuarios(response.body());
                } else {
                    Toast.makeText(HistorialUsuariosActivity.this, "No se pudieron cargar los usuarios", Toast.LENGTH_SHORT).show();
                }
            }
            @Override
            public void onFailure(Call<List<Usuario>> call, Throwable t) {
                Toast.makeText(HistorialUsuariosActivity.this, "Error de conexión", Toast.LENGTH_SHORT).show();
            }
        });
    }

    private void cargarTrabajadores() {
        apiService.obtenerTrabajadores().enqueue(new Callback<List<Usuario>>() {
            @Override
            public void onResponse(Call<List<Usuario>> call, Response<List<Usuario>> response) {
                if (response.isSuccessful() && response.body() != null) {
                    usuarioAdapter.actualizarUsuarios(response.body());
                } else {
                    Toast.makeText(HistorialUsuariosActivity.this, "No se pudieron cargar los trabajadores", Toast.LENGTH_SHORT).show();
                }
            }
            @Override
            public void onFailure(Call<List<Usuario>> call, Throwable t) {
                Toast.makeText(HistorialUsuariosActivity.this, "Error de conexión", Toast.LENGTH_SHORT).show();
            }
        });
    }

    private void cargarAdmins() {
        apiService.obtenerAdmins().enqueue(new Callback<List<Usuario>>() {
            @Override
            public void onResponse(Call<List<Usuario>> call, Response<List<Usuario>> response) {
                if (response.isSuccessful() && response.body() != null) {
                    usuarioAdapter.actualizarUsuarios(response.body());
                } else {
                    Toast.makeText(HistorialUsuariosActivity.this, "No se pudieron cargar los admins", Toast.LENGTH_SHORT).show();
                }
            }
            @Override
            public void onFailure(Call<List<Usuario>> call, Throwable t) {
                Toast.makeText(HistorialUsuariosActivity.this, "Error de conexión", Toast.LENGTH_SHORT).show();
            }
        });
    }

    private void cargarClientes() {
        apiService.obtenerClientes().enqueue(new Callback<List<Usuario>>() {
            @Override
            public void onResponse(Call<List<Usuario>> call, Response<List<Usuario>> response) {
                if (response.isSuccessful() && response.body() != null) {
                    usuarioAdapter.actualizarUsuarios(response.body());
                } else {
                    Toast.makeText(HistorialUsuariosActivity.this, "No se pudieron cargar los clientes", Toast.LENGTH_SHORT).show();
                }
            }
            @Override
            public void onFailure(Call<List<Usuario>> call, Throwable t) {
                Toast.makeText(HistorialUsuariosActivity.this, "Error de conexión", Toast.LENGTH_SHORT).show();
            }
        });
    }
}
