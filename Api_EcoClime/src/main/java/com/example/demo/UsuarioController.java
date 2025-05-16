package com.example.demo;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import java.util.Map;

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
	    
	    
	    
	    @PostMapping("/login")
	    public ResponseEntity<?> login(@RequestBody LoginRequest loginRequest) {
	        Object usuario = usuarioService.login(loginRequest.getEmail(), loginRequest.getPassword());
	        if (usuario != null) {
	            return ResponseEntity.ok(usuario);
	        } else {
	            return ResponseEntity.status(401).body("Email o contraseña incorrectos.");
	        }
	    }
	    
	    @GetMapping("/user/{email}")
	    public ResponseEntity<?> obtenerUsuarioPorEmail(@PathVariable String email) {
	        Object usuario = usuarioService.obtenerUsuarioPorEmail(email);
	        if (usuario != null) {
	            return ResponseEntity.ok(usuario);
	        } else {
	            return ResponseEntity.notFound().build();
	        }
	    }
	    
	    @PostMapping("/cambiar-contrasena")
    public ResponseEntity<?> cambiarPassword(@RequestBody Map<String, String> request) {
        String email = request.get("email");
        String nuevaPassword = request.get("nuevaPassword");
        try {
            usuarioService.cambiarPassword(email, nuevaPassword);
            return ResponseEntity.ok("Contraseña cambiada exitosamente");
        } catch (IllegalArgumentException e) {
            return ResponseEntity.badRequest().body(e.getMessage());
        }
    }
}