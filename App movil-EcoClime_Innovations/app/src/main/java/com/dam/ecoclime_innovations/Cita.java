package com.dam.ecoclime_innovations;

import com.google.gson.annotations.SerializedName;

public class Cita {
    @SerializedName("id")
    private int id;

    @SerializedName("usuario_id")
    private Usuario usuario;

    @SerializedName("nombre")
    private String nombre;

    @SerializedName("apellidos")
    private String apellidos;

    @SerializedName("telefono")
    private String telefono;

    @SerializedName("email")
    private String email;

    @SerializedName("tipo")
    private String tipo;

    @SerializedName("ciudad")
    private String ciudad;

    @SerializedName("codigo_postal")
    private String codigoPostal;

    @SerializedName("calle")
    private String calle;

    @SerializedName("numero_casa")
    private String numeroCasa;

    @SerializedName("fecha")
    private String fecha;

    @SerializedName("hora")
    private String hora;

    @SerializedName("fecha_hora")
    private String fechaHora;

    @SerializedName("estado")
    private String estado;

    @SerializedName("mensaje")
    private String mensaje;

    // Constructor vacío para Retrofit
    public Cita() {
        this.estado = "pendiente";
    }

    // Constructor completo
    public Cita(int id, Usuario usuario, String nombre, String apellidos, String telefono, String email,
                String tipo, String ciudad, String codigoPostal, String calle, String numeroCasa,
                String fecha, String hora, String estado) {
        this.id = id;
        this.usuario = usuario;
        this.nombre = nombre;
        this.apellidos = apellidos;
        this.telefono = telefono;
        this.email = email;
        this.tipo = tipo;
        this.ciudad = ciudad;
        this.codigoPostal = codigoPostal;
        this.calle = calle;
        this.numeroCasa = numeroCasa;
        this.fecha = fecha;
        this.hora = hora;
        this.fechaHora = fecha != null && hora != null ? fecha + "T" + hora + ":00" : null;
        this.estado = estado != null ? estado : "pendiente";
    }

    // Getters y Setters
    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public Usuario getUsuario() {
        return usuario;
    }

    public void setUsuario(Usuario usuario) {
        this.usuario = usuario;
    }

    // Métodos de conveniencia para compatibilidad
    public int getUsuarioId() {
        return usuario != null ? usuario.getId() : 0;
    }

    public void setUsuarioId(int usuarioId) {
        if (this.usuario == null) {
            this.usuario = new Usuario();
        }
        this.usuario.setId(usuarioId);
    }

    public String getNombre() {
        return nombre;
    }

    public void setNombre(String nombre) {
        this.nombre = nombre;
    }

    public String getApellidos() {
        return apellidos;
    }

    public void setApellidos(String apellidos) {
        this.apellidos = apellidos;
    }

    public String getTelefono() {
        return telefono;
    }

    public void setTelefono(String telefono) {
        this.telefono = telefono;
    }

    public String getEmail() {
        return email;
    }

    public void setEmail(String email) {
        this.email = email;
    }

    public String getTipo() {
        return tipo;
    }

    public void setTipo(String tipo) {
        this.tipo = tipo;
    }

    public String getCiudad() {
        return ciudad;
    }

    public void setCiudad(String ciudad) {
        this.ciudad = ciudad;
    }

    public String getCodigoPostal() {
        return codigoPostal;
    }

    public void setCodigoPostal(String codigoPostal) {
        this.codigoPostal = codigoPostal;
    }

    public String getCalle() {
        return calle;
    }

    public void setCalle(String calle) {
        this.calle = calle;
    }

    public String getNumeroCasa() {
        return numeroCasa;
    }

    public void setNumeroCasa(String numeroCasa) {
        this.numeroCasa = numeroCasa;
    }

    public String getFecha() {
        return fecha;
    }

    public void setFecha(String fecha) {
        this.fecha = fecha;
        actualizarFechaHora();
    }

    public String getHora() {
        return hora;
    }

    public void setHora(String hora) {
        this.hora = hora;
        actualizarFechaHora();
    }

    public String getFechaHora() {
        return fechaHora;
    }

    public void setFechaHora(String fechaHora) {
        this.fechaHora = fechaHora;
        if (fechaHora != null && fechaHora.contains("T")) {
            String[] partes = fechaHora.split("T");
            this.fecha = partes[0];
            this.hora = partes[1].substring(0, 5); // Tomar solo HH:mm
        }
    }

    private void actualizarFechaHora() {
        if (fecha != null && hora != null) {
            this.fechaHora = fecha + "T" + hora + ":00";
        }
    }

    public String getEstado() {
        return estado;
    }

    public void setEstado(String estado) {
        this.estado = estado;
    }

    public String getMensaje() {
        return mensaje;
    }

    public void setMensaje(String mensaje) {
        this.mensaje = mensaje;
    }

    @Override
    public String toString() {
        return "Cita{" +
                "id=" + id +
                ", usuario=" + (usuario != null ? usuario.getId() : "null") +
                ", nombre='" + nombre + '\'' +
                ", apellidos='" + apellidos + '\'' +
                ", telefono='" + telefono + '\'' +
                ", email='" + email + '\'' +
                ", tipo='" + tipo + '\'' +
                ", ciudad='" + ciudad + '\'' +
                ", codigoPostal='" + codigoPostal + '\'' +
                ", calle='" + calle + '\'' +
                ", numeroCasa='" + numeroCasa + '\'' +
                ", fecha='" + fecha + '\'' +
                ", hora='" + hora + '\'' +
                ", fechaHora='" + fechaHora + '\'' +
                ", estado='" + estado + '\'' +
                ", mensaje='" + mensaje + '\'' +
                '}';
    }
} 