package com.example.demo;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.security.crypto.bcrypt.BCryptPasswordEncoder;
import org.springframework.stereotype.Service;

import java.util.Optional;

@Service
public class UsuarioService {

    @Autowired
    private UsuarioRepository usuarioRepository; // Inyección de repositorio

    @Autowired
    private BCryptPasswordEncoder passwordEncoder; // Inyección de BCryptPasswordEncoder

    // Método para registrar un usuario
    public Usuario registrarUsuario(Usuario usuario) {
        // Encriptar la contraseña
        String contraseñaEncriptada = passwordEncoder.encode(usuario.getContraseña());
        usuario.setContraseña(contraseñaEncriptada);  // Establecer la contraseña encriptada

        // Guardar el usuario en la base de datos
        return usuarioRepository.save(usuario);
    }

    // Método para realizar el login
    public Optional<Usuario> login(String email, String contraseña) {
        // Buscar al usuario por email
        Optional<Usuario> usuario = usuarioRepository.findByEmail(email);
        
        if (usuario.isPresent()) {
            // Comparar la contraseña ingresada con la almacenada (encriptada)
            if (passwordEncoder.matches(contraseña, usuario.get().getContraseña())) {
                return usuario;  // Login exitoso
            }
        }

        // Retornar vacío si no se encuentra usuario o las contraseñas no coinciden
        return Optional.empty();
    }
}
