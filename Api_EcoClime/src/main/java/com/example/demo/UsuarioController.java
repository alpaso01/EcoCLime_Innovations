package com.example.demo;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import java.util.Optional;

@RestController
@RequestMapping("api/usuarios")
public class UsuarioController {

	 @Autowired
	    private UsuarioService usuarioService;

	    @PostMapping("/registro")
	    public ResponseEntity<?> registrarUsuario(@RequestBody Usuario usuario) {
	        try {
	            Usuario nuevoUsuario = usuarioService.registrarUsuario(usuario);
	            return ResponseEntity.ok(nuevoUsuario);
	        } catch (IllegalArgumentException e) {
	            return ResponseEntity.badRequest().body(e.getMessage());
	        }
	    }
	    
	    
	    
}