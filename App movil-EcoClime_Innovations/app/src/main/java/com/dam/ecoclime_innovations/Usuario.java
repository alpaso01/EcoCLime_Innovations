package com.dam.ecoclime_innovations;

import com.google.gson.annotations.SerializedName;

public class Usuario {
    @SerializedName("id")
    private int id;

    @SerializedName("nombre")
    private String nombre;

    @SerializedName("apellidos")
    private String apellidos;

    @SerializedName("email")
    private String email;

    @SerializedName("telefono")
    private String telefono;

    @SerializedName("password")
    private String password;

    @SerializedName("tipo")
    private String tipo;  // "particular" o "empresa" (solo para clientes)

    @SerializedName("rol")
    private String rol;  // "cliente", "admin" o "trabajador"

    @SerializedName("ciudad")
    private String ciudad;

    @SerializedName("codigo_postal")
    private String codigoPostal;

    @SerializedName("direccion")
    private String direccion;

    @Override
    public String toString() {
        return "Usuario{" +
                "id=" + id +
                ", nombre='" + nombre + '\'' +
                ", apellidos='" + apellidos + '\'' +
                ", email='" + email + '\'' +
                ", telefono='" + telefono + '\'' +
                ", password='" + password + '\'' +
                ", tipo='" + tipo + '\'' +
                ", rol='" + rol + '\'' +
                ", ciudad='" + ciudad + '\'' +
                ", codigoPostal='" + codigoPostal + '\'' +
                ", direccion='" + direccion + '\'' +
                '}';
    }

    // Constructor vacío necesario para Retrofit
    public Usuario() {
    }

    // Constructor para login
    public Usuario(String email, String password) {
        this.email = email;
        this.password = password;
    }

    // Constructor completo para registro
    public Usuario(String nombre, String apellidos, String email, String telefono, String password, String tipo) {
        this.nombre = nombre;
        this.apellidos = apellidos;
        this.email = email;
        this.telefono = telefono;
        this.password = password;
        this.tipo = tipo;
    }

    // Constructor con todos los campos
    public Usuario(String nombre, String apellidos, String email, String telefono, String password, String tipo,
                   String rol, String ciudad, String codigoPostal, String direccion) {
        this.nombre = nombre;
        this.apellidos = apellidos;
        this.email = email;
        this.telefono = telefono;
        this.password = password;
        this.tipo = tipo;
        this.rol = rol;
        this.ciudad = ciudad;
        this.codigoPostal = codigoPostal;
        this.direccion = direccion;
    }

    // Getters y Setters
    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
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

    public String getPassword() {
        return password;
    }

    public void setPassword(String password) {
        this.password = password;
    }

    public String getTipo() {
        return tipo;
    }

    public void setTipo(String tipo) {
        this.tipo = tipo;
    }

    public String getRol() {
        return rol;
    }

    public void setRol(String rol) {
        this.rol = rol;
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

    public String getDireccion() {
        return direccion;
    }

    public void setDireccion(String direccion) {
        this.direccion = direccion;
    }
}