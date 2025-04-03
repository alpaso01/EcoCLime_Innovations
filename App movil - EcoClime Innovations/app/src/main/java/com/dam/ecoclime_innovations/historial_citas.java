package com.dam.ecoclime_innovations;

import android.os.Bundle;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Toast;
import android.widget.TextView;
import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import java.util.List;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class historial_citas extends AppCompatActivity {
    private RecyclerView recyclerView;
    private ApiService apiService;
    private int usuarioId; // ID del usuario logueado

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_historial_citas);

        recyclerView = findViewById(R.id.recyclerViewCitas);
        recyclerView.setLayoutManager(new LinearLayoutManager(this));

        apiService = RetrofitClient.getClient().create(ApiService.class);  // Usamos getClient()
        usuarioId = getIntent().getIntExtra("usuarioId", -1);

        if (usuarioId != -1) {
            obtenerHistorialCitas(usuarioId);
        } else {
            Toast.makeText(this, "Error: Usuario no identificado", Toast.LENGTH_SHORT).show();
        }
    }

    private void obtenerHistorialCitas(int usuarioId) {
        apiService.obtenerHistorialCitas(usuarioId).enqueue(new Callback<List<Cita>>() {
            @Override
            public void onResponse(Call<List<Cita>> call, Response<List<Cita>> response) {
                if (response.isSuccessful() && response.body() != null) {
                    CitaAdapter citaAdapter = new CitaAdapter(response.body());
                    recyclerView.setAdapter(citaAdapter);
                } else {
                    Toast.makeText(historial_citas.this, "No hay citas registradas", Toast.LENGTH_SHORT).show();
                }
            }

            @Override
            public void onFailure(Call<List<Cita>> call, Throwable t) {
                Toast.makeText(historial_citas.this, "Error al obtener citas", Toast.LENGTH_SHORT).show();
            }
        });
    }

    // Clase interna que actúa como el Adapter del RecyclerView
    public class CitaAdapter extends RecyclerView.Adapter<CitaAdapter.CitaViewHolder> {
        private List<Cita> citas;

        public CitaAdapter(List<Cita> citas) {
            this.citas = citas;
        }

        @Override
        public CitaViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
            // Infla el layout para cada elemento de la lista
            View view = getLayoutInflater().inflate(R.layout.item_cita, parent, false);
            return new CitaViewHolder(view);
        }

        @Override
        public void onBindViewHolder(CitaViewHolder holder, int position) {
            Cita cita = citas.get(position);
            holder.nombreTextView.setText(cita.getNombre() + " " + cita.getApellidos());
            holder.telefonoTextView.setText(cita.getTelefono());
            holder.emailTextView.setText(cita.getEmail());
            holder.tipoTextView.setText(cita.getTipo());
            holder.ciudadTextView.setText(cita.getCiudad());
            holder.codigoPostalTextView.setText(cita.getCodigoPostal());
            holder.calleTextView.setText(cita.getCalle());
            holder.numeroCasaTextView.setText(String.valueOf(cita.getNumeroCasa()));
            holder.fechaHoraTextView.setText(cita.getFechaHora());
        }

        @Override
        public int getItemCount() {
            return citas.size();
        }

        // Clase ViewHolder para mantener las vistas de cada item
        public class CitaViewHolder extends RecyclerView.ViewHolder {
            TextView nombreTextView, telefonoTextView, emailTextView, tipoTextView, ciudadTextView, codigoPostalTextView,
                    calleTextView, numeroCasaTextView, fechaHoraTextView;

            public CitaViewHolder(View itemView) {
                super(itemView);
                nombreTextView = itemView.findViewById(R.id.nombreTextView);
                telefonoTextView = itemView.findViewById(R.id.telefonoTextView);
                emailTextView = itemView.findViewById(R.id.emailTextView);
                tipoTextView = itemView.findViewById(R.id.tipoTextView);
                ciudadTextView = itemView.findViewById(R.id.ciudadTextView);
                codigoPostalTextView = itemView.findViewById(R.id.codigoPostalTextView);
                calleTextView = itemView.findViewById(R.id.calleTextView);
                numeroCasaTextView = itemView.findViewById(R.id.numeroCasaTextView);
                fechaHoraTextView = itemView.findViewById(R.id.fechaHoraTextView);
            }
        }
    }
}
