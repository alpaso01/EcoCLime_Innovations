package com.example.demo;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import java.util.Optional;

@Service
public class UsuarioService {

    @Autowired
    private UsuarioRepository usuarioRepository;

    // Método para registrar un usuario
    public UsuarioDTO registrarUsuario(UsuarioDTO usuarioDTO) {
        Usuario usuario = usuarioDTO.toUsuario();
        Usuario usuarioGuardado = usuarioRepository.save(usuario);
        return new UsuarioDTO(usuarioGuardado);
    }

    // Método para realizar el login
    public Optional<UsuarioDTO> login(String email, String contraseña) {
        Optional<Usuario> usuario = usuarioRepository.findByEmail(email);
        
        if (usuario.isPresent() && usuario.get().getContraseña().equals(contraseña)) {
            return Optional.of(new UsuarioDTO(usuario.get()));
        }
        
        return Optional.empty();
    }

    // Método para buscar usuario por email
    public Optional<UsuarioDTO> buscarPorEmail(String email) {
        Optional<Usuario> usuario = usuarioRepository.findByEmail(email);
        return usuario.map(UsuarioDTO::new);
    }
}