package com.dam.ecoclime_innovations;

import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;
import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;
import java.util.List;

public class UsuarioAdapter extends RecyclerView.Adapter<UsuarioAdapter.UsuarioViewHolder> {
    private List<Usuario> usuarios;

    public UsuarioAdapter(List<Usuario> usuarios) {
        this.usuarios = usuarios;
    }

    public void actualizarUsuarios(List<Usuario> nuevosUsuarios) {
        this.usuarios = nuevosUsuarios;
        notifyDataSetChanged();
    }

    @NonNull
    @Override
    public UsuarioViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.item_usuario, parent, false);
        return new UsuarioViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull UsuarioViewHolder holder, int position) {
        Usuario usuario = usuarios.get(position);
        holder.tvNombreUsuario.setText(usuario.getNombre() + " " + usuario.getApellidos());
        holder.tvEmailUsuario.setText(usuario.getEmail());
        
        // Mostrar el rol del usuario (cliente, admin o trabajador)
        String rol = usuario.getRol() != null ? 
                   usuario.getRol().substring(0, 1).toUpperCase() + usuario.getRol().substring(1) : "";
        
        // Si es cliente, mostrar también el tipo (particular/empresa)
        if ("cliente".equals(usuario.getRol()) && usuario.getTipo() != null) {
            String tipo = usuario.getTipo().substring(0, 1).toUpperCase() + 
                        usuario.getTipo().substring(1);
            holder.tvTipoUsuario.setText(rol + " - " + tipo);
        } else {
            holder.tvTipoUsuario.setText(rol);
        }
    }

    @Override
    public int getItemCount() {
        return usuarios != null ? usuarios.size() : 0;
    }

    static class UsuarioViewHolder extends RecyclerView.ViewHolder {
        TextView tvNombreUsuario, tvEmailUsuario, tvTipoUsuario;
        UsuarioViewHolder(@NonNull View itemView) {
            super(itemView);
            tvNombreUsuario = itemView.findViewById(R.id.tvNombreUsuario);
            tvEmailUsuario = itemView.findViewById(R.id.tvEmailUsuario);
            tvTipoUsuario = itemView.findViewById(R.id.tvTipoUsuario);
        }
    }
}
