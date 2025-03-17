package com.example.demo;


import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;

import java.util.Optional;

@Service
public class UsuarioService {

    @Autowired
    private UsuarioRepository usuarioRepository;

    private final BCryptPasswordEncoder passwordEncoder;

    public UsuarioService(BCryptPasswordEncoder passwordEncoder) {
        this.passwordEncoder = passwordEncoder;
    }

    // Método para registrar un usuario
    public Usuario registrarUsuario(Usuario usuario) {
        // Encriptar la contraseña
        String contraseñaEncriptada = passwordEncoder.encode(usuario.getContraseña());
        usuario.setContraseña(contraseñaEncriptada);

        // Guardar el usuario en la base de datos
        return usuarioRepository.save(usuario);
    }

    // Método para realizar el login
    public Optional<Usuario> login(String email, String contraseña) {
        Optional<Usuario> usuario = usuarioRepository.findByEmail(email);
        
        if (usuario.isPresent()) {
            if (passwordEncoder.matches(contraseña, usuario.get().getContraseña())) {
                return usuario;
            }
        }
        
        return Optional.empty(); // Retorna un Optional vacío si no se encuentra el usuario o las contraseñas no coinciden
    }
}