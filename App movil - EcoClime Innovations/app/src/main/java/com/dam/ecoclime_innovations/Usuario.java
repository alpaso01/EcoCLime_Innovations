package com.dam.ecoclime_innovations;

public class Usuario {
    private String nombre;
    private String apellidos;
    private String email;
    private String telefono;
    private String contraseña;
    private String tipo;

    public Usuario(String nombre, String apellidos, String email, String telefono, String contraseña, String tipo) {
        this.nombre = nombre;
        this.apellidos = apellidos;
        this.email = email;
        this.telefono = telefono;
        this.contraseña = contraseña;
        this.tipo = tipo;
    }

    public String getEmail() {
        return email;
    }

    public String getContraseña() {
        return contraseña;
    }
}