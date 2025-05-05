package com.example.demo;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import java.util.Optional;

@Service
public class UsuarioService {

	  @Autowired
	    private UsuarioRepository usuarioRepository;

	  //REGISTRO
	    public Usuario registrarUsuario(Usuario usuario) {
	        // Validación simple: email no duplicado
	        if (usuarioRepository.existsByEmail(usuario.getEmail())) {
	            throw new IllegalArgumentException("El email ya está en uso.");
	        }
	        return usuarioRepository.save(usuario);
	    }
	    
	    
}