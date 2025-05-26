package com.dam.ecoclime_innovations;

import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.Toast;
import androidx.annotation.Nullable;
import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import java.util.ArrayList;
import java.util.List;
import com.dam.ecoclime_innovations.UsuarioUnificadoDTO;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class HistorialUsuariosActivity extends AppCompatActivity {
    private RecyclerView recyclerUsuarios;
    private UsuarioAdapter usuarioAdapter;
    private ApiService apiService;
    private Button btnTrabajadores, btnAdmins, btnClientes, btnAtras;

    @Override
    protected void onCreate(@Nullable Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_historial_usuarios);

        recyclerUsuarios = findViewById(R.id.recyclerUsuarios);
        recyclerUsuarios.setLayoutManager(new LinearLayoutManager(this));
        usuarioAdapter = new UsuarioAdapter(new ArrayList<>());
        recyclerUsuarios.setAdapter(usuarioAdapter);

        apiService = RetrofitClient.getInstance().create(ApiService.class);

        btnTrabajadores = findViewById(R.id.btnTrabajadores);
        btnAdmins = findViewById(R.id.btnAdmins);
        btnClientes = findViewById(R.id.btnClientes);
        btnAtras = findViewById(R.id.btnAtras);


        btnTrabajadores.setOnClickListener(v -> cargarTrabajadores());
        btnAdmins.setOnClickListener(v -> cargarAdmins());
        btnClientes.setOnClickListener(v -> cargarClientes());
        
        btnAtras.setOnClickListener(v -> {
            Intent intent = new Intent(HistorialUsuariosActivity.this, AdminActivity.class);
            startActivity(intent);
            finish();
        });

        cargarTrabajadores();
        // Puedes seleccionar aquí si quieres cargar por defecto un rol, por ejemplo cargarAdmins();
        // cargarAdmins();
    }

    @Override
    protected void onResume() {
        super.onResume();
        // cargarTrabajadores();
        cargarTrabajadores();
        // Puedes seleccionar aquí si quieres cargar por defecto un rol, por ejemplo cargarAdmins();
        // cargarAdmins();
    }

    private List<Usuario> convertirADTOs(List<UsuarioUnificadoDTO> dtos) {
        List<Usuario> usuarios = new ArrayList<>();
        if (dtos != null) {
            for (UsuarioUnificadoDTO dto : dtos) {
                Usuario usuario = new Usuario();
                usuario.setId(dto.getId());
                usuario.setNombre(dto.getNombre());
                usuario.setApellidos(dto.getApellidos());
                usuario.setEmail(dto.getEmail());
                usuario.setTelefono(dto.getTelefono());
                usuario.setRol(dto.getRol());
                usuario.setTipo(dto.getTipo());
                usuarios.add(usuario);
            }
        }
        return usuarios;
    }

    private void mostrarError(String mensaje) {
        Log.e("HistorialUsuarios", mensaje);
        runOnUiThread(() -> 
            Toast.makeText(HistorialUsuariosActivity.this, mensaje, Toast.LENGTH_SHORT).show()
        );
    }

    private void cargarTodos() {
        apiService.obtenerTodosLosUsuarios().enqueue(new Callback<List<UsuarioUnificadoDTO>>() {
            @Override
            public void onResponse(Call<List<UsuarioUnificadoDTO>> call, Response<List<UsuarioUnificadoDTO>> response) {
                if (response.isSuccessful() && response.body() != null) {
                    Log.d("HistorialUsuarios", "Usuarios cargados: " + response.body().size());
                    usuarioAdapter.actualizarUsuarios(convertirADTOs(response.body()));
                } else {
                    mostrarError("Error al cargar usuarios: " + response.message());
                }
            }

            @Override
            public void onFailure(Call<List<UsuarioUnificadoDTO>> call, Throwable t) {
                mostrarError("Error de conexión: " + t.getMessage());
            }
        });
    }

    private void cargarTrabajadores() {
        apiService.obtenerTrabajadores().enqueue(new Callback<List<UsuarioUnificadoDTO>>() {
            @Override
            public void onResponse(Call<List<UsuarioUnificadoDTO>> call, Response<List<UsuarioUnificadoDTO>> response) {
                if (response.isSuccessful() && response.body() != null) {
                    Log.d("HistorialUsuarios", "Trabajadores cargados: " + response.body().size());
                    usuarioAdapter.actualizarUsuarios(convertirADTOs(response.body()));
                } else {
                    mostrarError("Error al cargar trabajadores: " + response.message());
                }
            }

            @Override
            public void onFailure(Call<List<UsuarioUnificadoDTO>> call, Throwable t) {
                mostrarError("Error de conexión: " + t.getMessage());
            }
        });
    }

    private void cargarAdmins() {
        apiService.obtenerAdmins().enqueue(new Callback<List<UsuarioUnificadoDTO>>() {
            @Override
            public void onResponse(Call<List<UsuarioUnificadoDTO>> call, Response<List<UsuarioUnificadoDTO>> response) {
                if (response.isSuccessful() && response.body() != null) {
                    Log.d("HistorialUsuarios", "Admins cargados: " + response.body().size());
                    usuarioAdapter.actualizarUsuarios(convertirADTOs(response.body()));
                } else {
                    mostrarError("Error al cargar admins: " + response.message());
                }
            }

            @Override
            public void onFailure(Call<List<UsuarioUnificadoDTO>> call, Throwable t) {
                mostrarError("Error de conexión: " + t.getMessage());
            }
        });
    }

    private void cargarClientes() {
        apiService.obtenerClientes().enqueue(new Callback<List<UsuarioUnificadoDTO>>() {
            @Override
            public void onResponse(Call<List<UsuarioUnificadoDTO>> call, Response<List<UsuarioUnificadoDTO>> response) {
                if (response.isSuccessful() && response.body() != null) {
                    Log.d("HistorialUsuarios", "Clientes cargados: " + response.body().size());
                    usuarioAdapter.actualizarUsuarios(convertirADTOs(response.body()));
                } else {
                    mostrarError("Error al cargar clientes: " + response.message());
                }
            }

            @Override
            public void onFailure(Call<List<UsuarioUnificadoDTO>> call, Throwable t) {
                mostrarError("Error de conexión: " + t.getMessage());
            }
        });
    }
}
