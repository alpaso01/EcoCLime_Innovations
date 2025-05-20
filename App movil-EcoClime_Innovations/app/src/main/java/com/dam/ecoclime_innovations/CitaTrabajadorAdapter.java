package com.dam.ecoclime_innovations;

import android.app.AlertDialog;
import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import java.util.ArrayList;
import java.util.List;

public class CitaTrabajadorAdapter extends RecyclerView.Adapter<CitaTrabajadorAdapter.CitaTrabajadorViewHolder> {
    private List<Cita> citas;
    private OnCitaActionListener listener;
    private Context context;

    public interface OnCitaActionListener {
        void onModificarClick(Cita cita);
        void onEliminarClick(Cita cita);
        void onEstadoCambiado(Cita cita, String nuevoEstado);
    }

    public CitaTrabajadorAdapter(List<Cita> citas, OnCitaActionListener listener, Context context) {
        this.citas = citas != null ? new ArrayList<>(citas) : new ArrayList<>();
        this.listener = listener;
        this.context = context;
    }

    public void actualizarCitas(List<Cita> nuevasCitas) {
        this.citas.clear();
        if (nuevasCitas != null) {
            this.citas.addAll(nuevasCitas);
        }
        notifyDataSetChanged();
    }

    @NonNull
    @Override
    public CitaTrabajadorViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext())
                .inflate(R.layout.item_trabajador, parent, false);
        return new CitaTrabajadorViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull CitaTrabajadorViewHolder holder, int position) {
        Cita cita = citas.get(position);
        if (cita == null) return;

        // Configurar los datos de la cita
        String nombreCompleto = cita.getTipo().equals("Particular") ?
                cita.getNombre() + " " + (cita.getApellidos() != null ? cita.getApellidos() : "") :
                "Empresa: " + cita.getNombre();

        holder.nombreTextView.setText(nombreCompleto);
        holder.telefonoTextView.setText("Tel: " + (cita.getTelefono() != null ? cita.getTelefono() : ""));
        holder.emailTextView.setText("Email: " + (cita.getEmail() != null ? cita.getEmail() : ""));
        holder.tipoTextView.setText("Tipo: " + cita.getTipo());
        holder.ciudadTextView.setText("Ciudad: " + (cita.getCiudad() != null ? cita.getCiudad() : ""));
        holder.codigoPostalTextView.setText("C.P.: " + (cita.getCodigoPostal() != null ? cita.getCodigoPostal() : ""));
        holder.calleTextView.setText("Calle: " + (cita.getCalle() != null ? cita.getCalle() : ""));
        holder.numeroCasaTextView.setText("Nº: " + (cita.getNumeroCasa() != null ? cita.getNumeroCasa() : ""));

        // Mostrar fecha y hora
        String fechaHora = cita.getFechaHora();
        if (fechaHora != null && fechaHora.contains("T")) {
            String[] partes = fechaHora.split("T");
            String fecha = partes[0];
            String hora = partes[1].length() >= 5 ? partes[1].substring(0, 5) : "";
            holder.fechaHoraTextView.setText("Fecha y hora: " + fecha + " " + hora);
        } else {
            holder.fechaHoraTextView.setText("Fecha y hora: " + (fechaHora != null ? fechaHora : "No disponible"));
        }

        // Configurar el estado actual
        String estadoActual = cita.getEstado() != null ? cita.getEstado() : "programada";
        holder.estadoTextView.setText("Estado: " + estadoActual.substring(0, 1).toUpperCase() + estadoActual.substring(1));
        actualizarColorEstado(holder.estadoTextView, estadoActual);

        // Configurar botones
        holder.btnModificar.setOnClickListener(v -> {
            if (listener != null) listener.onModificarClick(cita);
        });

        holder.btnEliminar.setOnClickListener(v -> {
            if (listener != null) listener.onEliminarClick(cita);
        });

        holder.btnEstado.setOnClickListener(v -> mostrarDialogoEstado(cita, holder));
    }

    private void mostrarDialogoEstado(Cita cita, CitaTrabajadorViewHolder holder) {
        AlertDialog.Builder builder = new AlertDialog.Builder(context);
        builder.setTitle("Cambiar estado de la cita");

        View dialogView = LayoutInflater.from(context).inflate(R.layout.dialog_cambiar_estado, null);
        Spinner spinnerEstados = dialogView.findViewById(R.id.spinnerEstados);

        // Configurar el spinner con los estados
        ArrayAdapter<CharSequence> adapter = ArrayAdapter.createFromResource(
                context, 
                R.array.estados_cita, 
                android.R.layout.simple_spinner_item);
        adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        spinnerEstados.setAdapter(adapter);

        // Establecer el estado actual
        String estadoActual = cita.getEstado() != null ? cita.getEstado() : "programada";
        int posicion = 0;
        switch (estadoActual) {
            case "confirmada": posicion = 1; break;
            case "en_curso": posicion = 2; break;
            case "cancelada": posicion = 3; break;
            default: posicion = 0; // programada
        }
        spinnerEstados.setSelection(posicion);

        builder.setView(dialogView);
        builder.setPositiveButton("Guardar", (dialog, which) -> {
            String nuevoEstado = spinnerEstados.getSelectedItem().toString().toLowerCase();
            if (listener != null) {
                listener.onEstadoCambiado(cita, nuevoEstado);
                // Actualizar la vista
                cita.setEstado(nuevoEstado);
                holder.estadoTextView.setText("Estado: " + 
                    nuevoEstado.substring(0, 1).toUpperCase() + nuevoEstado.substring(1));
                actualizarColorEstado(holder.estadoTextView, nuevoEstado);
            }
        });
        builder.setNegativeButton("Cancelar", null);
        builder.show();
    }

    private void actualizarColorEstado(TextView textView, String estado) {
        int color;
        switch (estado) {
            case "programada":
                color = 0xFF2196F3; // Azul
                break;
            case "confirmada":
                color = 0xFF4CAF50; // Verde
                break;
            case "en_curso":
                color = 0xFFFFC107; // Amarillo
                break;
            case "cancelada":
                color = 0xFFF44336; // Rojo
                break;
            default:
                color = 0xFF9E9E9E; // Gris
        }
        textView.setTextColor(color);
    }

    @Override
    public int getItemCount() {
        return citas.size();
    }

    public static class CitaTrabajadorViewHolder extends RecyclerView.ViewHolder {
        TextView nombreTextView, telefonoTextView, emailTextView, tipoTextView;
        TextView ciudadTextView, codigoPostalTextView, calleTextView, numeroCasaTextView, fechaHoraTextView, estadoTextView;
        Button btnModificar, btnEliminar, btnEstado;

        public CitaTrabajadorViewHolder(@NonNull View itemView) {
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
            btnModificar = itemView.findViewById(R.id.btnModificar);
            btnEliminar = itemView.findViewById(R.id.btnEliminar);
            btnEstado = itemView.findViewById(R.id.btnEstado);
        }
    }
}
