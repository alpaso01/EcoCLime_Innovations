package com.example.demo;

import java.time.LocalDateTime;
import java.time.LocalDate;
import java.time.LocalTime;
import com.fasterxml.jackson.annotation.JsonProperty;
import com.fasterxml.jackson.annotation.JsonIgnore;
import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;
import jakarta.persistence.JoinColumn;
import jakarta.persistence.ManyToOne;
import jakarta.persistence.Table;
import jakarta.persistence.Transient;
import jakarta.persistence.Column;

@Entity
@Table(name = "citas")
public class Cita {

    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @ManyToOne
    @JoinColumn(name = "usuario_id", nullable = false)
    @JsonProperty("usuario_id")
    private Usuario usuario; // Relación con la tabla usuarios

    private String nombre;
    private String apellidos;
    private String telefono;
    private String email;
    private String tipo; // "Empresa" o "Particular"
    private String ciudad;
    
    @Column(name = "codigo_postal")
    @JsonProperty("codigo_postal")
    private String codigoPostal;
    
    private String calle;
    
    @Column(name = "numero_casa")
    @JsonProperty("numero_casa")
    private String numeroCasa;
    
    @Column(name = "fecha_hora")
    @JsonProperty("fecha_hora")
    private LocalDateTime fechaHora; // Formato de fecha y hora

    @Transient
    @JsonProperty("fecha")
    private String fecha;

    @Transient
    @JsonProperty("hora")
    private String hora;

    private void actualizarFechaHora() {
        if (fecha != null && !fecha.isEmpty() && hora != null && !hora.isEmpty()) {
            try {
                LocalDate localDate = LocalDate.parse(fecha);
                LocalTime localTime = LocalTime.parse(hora);
                this.fechaHora = LocalDateTime.of(localDate, localTime);
            } catch (Exception e) {
                // Log error pero no lanzar excepción para permitir que la validación maneje esto
                System.err.println("Error al parsear fecha/hora: " + e.getMessage());
            }
        }
    }

    @Override
    public String toString() {
        return "Cita{" +
                "id=" + id +
                ", usuario=" + (usuario != null ? usuario.getId() : null) +
                ", nombre='" + nombre + '\'' +
                ", apellidos='" + apellidos + '\'' +
                ", telefono='" + telefono + '\'' +
                ", email='" + email + '\'' +
                ", tipo='" + tipo + '\'' +
                ", ciudad='" + ciudad + '\'' +
                ", codigoPostal='" + codigoPostal + '\'' +
                ", calle='" + calle + '\'' +
                ", numeroCasa='" + numeroCasa + '\'' +
                ", fechaHora=" + fechaHora +
                ", fecha='" + fecha + '\'' +
                ", hora='" + hora + '\'' +
                '}';
    }

    // Getters y Setters
    public Long getId() {
        return id;
    }

    public void setId(Long id) {
        this.id = id;
    }

    public Usuario getUsuario() {
        return usuario;
    }

    public void setUsuario(Usuario usuario) {
        this.usuario = usuario;
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

    public LocalDateTime getFechaHora() {
        return fechaHora;
    }

    public void setFechaHora(LocalDateTime fechaHora) {
        this.fechaHora = fechaHora;
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
}