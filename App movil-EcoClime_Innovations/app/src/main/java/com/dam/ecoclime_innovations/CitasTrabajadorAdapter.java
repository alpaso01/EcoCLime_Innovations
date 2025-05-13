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
                .inflate(R.layout.item_cita_trabajador, parent, false);
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
        private TextView tvFecha;
        private TextView tvHora;
        private TextView tvTipo;
        private TextView tvCliente;
        private TextView tvDireccion;
        private TextView tvTelefono;

        public CitaViewHolder(@NonNull View itemView) {
            super(itemView);
            tvFecha = itemView.findViewById(R.id.tvFecha);
            tvHora = itemView.findViewById(R.id.tvHora);
            tvTipo = itemView.findViewById(R.id.tvTipo);
            tvCliente = itemView.findViewById(R.id.tvCliente);
            tvDireccion = itemView.findViewById(R.id.tvDireccion);
            tvTelefono = itemView.findViewById(R.id.tvTelefono);
        }

        public void bind(Cita cita) {
            tvFecha.setText(cita.getFecha());
            tvHora.setText(cita.getHora());
            tvTipo.setText(cita.getTipo());
            tvCliente.setText(cita.getNombre());
            String direccionCompleta = cita.getCalle() + " " + cita.getNumeroCasa() + ", " + cita.getCiudad() + " (" + cita.getCodigoPostal() + ")";
            tvDireccion.setText(direccionCompleta);
            tvTelefono.setText(cita.getTelefono());
        }
    }
} 