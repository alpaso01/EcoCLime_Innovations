package com.dam.ecoclime_innovations;

import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.TextView;
import androidx.recyclerview.widget.RecyclerView;

import java.util.List;

public class CitaAdapter extends RecyclerView.Adapter<CitaAdapter.CitaViewHolder> {
    private List<Cita> citas;
    private OnCitaActionListener listener;

    public interface OnCitaActionListener {
        void onModificarClick(Cita cita);
        void onEliminarClick(Cita cita);
    }

    public CitaAdapter(List<Cita> citas, OnCitaActionListener listener) {
        this.citas = citas;
        this.listener = listener;
    }

    public void actualizarCitas(List<Cita> nuevasCitas) {
        this.citas.clear();
        this.citas.addAll(nuevasCitas);
        notifyDataSetChanged();
    }

    @Override
    public CitaViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.item_cita, parent, false);
        return new CitaViewHolder(view);
    }

    @Override
    public void onBindViewHolder(CitaViewHolder holder, int position) {
        Cita cita = citas.get(position);
        
        String nombreCompleto = cita.getTipo().equals("Particular") ? 
            cita.getNombre() + " " + cita.getApellidos() :
            "Empresa: " + cita.getNombre();
        
        holder.nombreTextView.setText(nombreCompleto);
        holder.telefonoTextView.setText("Tel: " + cita.getTelefono());
        holder.emailTextView.setText("Email: " + cita.getEmail());
        holder.tipoTextView.setText("Tipo: " + cita.getTipo());
        holder.ciudadTextView.setText("Ciudad: " + cita.getCiudad());
        holder.codigoPostalTextView.setText("C.P.: " + (cita.getCodigoPostal() != null ? cita.getCodigoPostal() : ""));
        holder.calleTextView.setText("Calle: " + cita.getCalle());
        holder.numeroCasaTextView.setText("Nº: " + (cita.getNumeroCasa() != null ? cita.getNumeroCasa() : ""));
        
        // Mostrar fecha y hora desde el campo fechaHora
        String fechaHora = cita.getFechaHora();
        if (fechaHora != null && fechaHora.contains("T")) {
            String[] partes = fechaHora.split("T");
            String fecha = partes[0];
            String hora = partes[1].substring(0, 5); // Tomar solo HH:mm
            holder.fechaHoraTextView.setText("Fecha y hora: " + fecha + " " + hora);
        } else {
            holder.fechaHoraTextView.setText("Fecha y hora: No disponible");
        }

        holder.btnModificar.setOnClickListener(v -> listener.onModificarClick(cita));
        holder.btnEliminar.setOnClickListener(v -> listener.onEliminarClick(cita));
    }

    @Override
    public int getItemCount() {
        return citas.size();
    }

    public static class CitaViewHolder extends RecyclerView.ViewHolder {
        TextView nombreTextView, telefonoTextView, emailTextView, tipoTextView;
        TextView ciudadTextView, codigoPostalTextView, calleTextView, numeroCasaTextView, fechaHoraTextView;
        Button btnModificar, btnEliminar;

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
            btnModificar = itemView.findViewById(R.id.btnModificar);
            btnEliminar = itemView.findViewById(R.id.btnEliminar);
        }
    }
} 