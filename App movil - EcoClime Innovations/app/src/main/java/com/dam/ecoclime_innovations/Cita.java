package com.dam.ecoclime_innovations;

public class Cita {
    private String fecha;
    private String hora;
    private String email;
    private String telefono;
    private String ciudad;
    private String codigoPostal;
    private String calle;
    private String numero;
    private String mensaje;

    public Cita(String fecha, String hora, String email, String telefono, String ciudad,
                String codigoPostal, String calle, String numero, String mensaje) {
        this.fecha = fecha;
        this.hora = hora;
        this.email = email;
        this.telefono = telefono;
        this.ciudad = ciudad;
        this.codigoPostal = codigoPostal;
        this.calle = calle;
        this.numero = numero;
        this.mensaje = mensaje;
    }

    // Getters y setters

    public String getFecha() {
        return fecha;
    }

    public void setFecha(String fecha) {
        this.fecha = fecha;
    }

    public String getHora() {
        return hora;
    }

    public void setHora(String hora) {
        this.hora = hora;
    }

    public String getEmail() {
        return email;
    }

    public void setEmail(String email) {
        this.email = email;
    }

    public String getTelefono() {
        return telefono;
    }

    public void setTelefono(String telefono) {
        this.telefono = telefono;
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

    public String getNumero() {
        return numero;
    }

    public void setNumero(String numero) {
        this.numero = numero;
    }

    public String getMensaje() {
        return mensaje;
    }

    public void setMensaje(String mensaje) {
        this.mensaje = mensaje;
    }
}
