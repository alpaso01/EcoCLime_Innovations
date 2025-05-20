package com.dam.ecoclime_innovations;

import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;
import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;
import java.util.ArrayList;
import java.util.List;

public class CitasTrabajadorAdapter extends RecyclerView.Adapter<CitasTrabajadorAdapter.CitaViewHolder> {
    private List<Cita> citas;

    public CitasTrabajadorAdapter() {
        this.citas = new ArrayList<>();
    }

    @NonNull
    @Override
    public CitaViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.item_trabajador, parent, false);
        return new CitaViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull CitaViewHolder holder, int position) {
        Cita cita = citas.get(position);
        holder.bind(cita);
    }

    @Override
    public int getItemCount() {
        return citas.size();
    }

    public void setCitas(List<Cita> citas) {
        this.citas = citas;
        notifyDataSetChanged();
    }

    static class CitaViewHolder extends RecyclerView.ViewHolder {
        private TextView nombreTextView, telefonoTextView, emailTextView, tipoTextView;
        private TextView ciudadTextView, codigoPostalTextView, calleTextView, numeroCasaTextView, fechaHoraTextView, estadoTextView;

        public CitaViewHolder(@NonNull View itemView) {
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
            estadoTextView = itemView.findViewById(R.id.estadoTextView);
        }

        public void bind(Cita cita) {
            nombreTextView.setText(cita.getNombre());
            telefonoTextView.setText("Tel: " + (cita.getTelefono() != null ? cita.getTelefono() : ""));
            emailTextView.setText("Email: " + (cita.getEmail() != null ? cita.getEmail() : ""));
            tipoTextView.setText("Tipo: " + cita.getTipo());
            ciudadTextView.setText("Ciudad: " + (cita.getCiudad() != null ? cita.getCiudad() : ""));
            codigoPostalTextView.setText("C.P.: " + (cita.getCodigoPostal() != null ? cita.getCodigoPostal() : ""));
            calleTextView.setText("Calle: " + (cita.getCalle() != null ? cita.getCalle() : ""));
            numeroCasaTextView.setText("Nº: " + (cita.getNumeroCasa() != null ? cita.getNumeroCasa() : ""));
            fechaHoraTextView.setText("Fecha y hora: " + (cita.getFechaHora() != null ? cita.getFechaHora() : "No disponible"));
            // Estado
            String estado = cita.getEstado() != null ? cita.getEstado() : "programada";
            estadoTextView.setText("Estado: " + estado.substring(0,1).toUpperCase() + estado.substring(1));
            switch (estado) {
                case "programada":
                    estadoTextView.setTextColor(itemView.getResources().getColor(android.R.color.holo_blue_dark));
                    break;
                case "confirmada":
                    estadoTextView.setTextColor(itemView.getResources().getColor(android.R.color.holo_green_dark));
                    break;
                case "en_curso":
                    estadoTextView.setTextColor(itemView.getResources().getColor(android.R.color.holo_orange_dark));
                    break;
                case "cancelada":
                    estadoTextView.setTextColor(itemView.getResources().getColor(android.R.color.holo_red_dark));
                    break;
                default:
                    estadoTextView.setTextColor(itemView.getResources().getColor(android.R.color.darker_gray));
            }
        }
    }
}