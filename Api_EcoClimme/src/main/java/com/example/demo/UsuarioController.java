package com.example.demo;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import java.util.Optional;

@RestController
@RequestMapping("/api/ApiEcoClimeInnovations/usuarios")
public class UsuarioController {

    @Autowired
    private UsuarioService usuarioService;

    // Endpoint para registrar un usuario
    @PostMapping("/register")
    public ResponseEntity<UsuarioDTO> register(@RequestBody UsuarioDTO usuarioDTO) {
        UsuarioDTO nuevoUsuario = usuarioService.registrarUsuario(usuarioDTO);
        return ResponseEntity.ok(nuevoUsuario);
    }

    // Endpoint para hacer login
    @PostMapping("/login")
    public ResponseEntity<?> login(@RequestBody UsuarioDTO usuarioDTO) {
        Optional<UsuarioDTO> usuarioEncontrado = usuarioService.login(usuarioDTO.getEmail(), usuarioDTO.getContraseña());

        if (usuarioEncontrado.isPresent()) {
            return ResponseEntity.ok(usuarioEncontrado.get());
        } else {
            return ResponseEntity.status(401).body("Credenciales incorrectas");
        }
    }

    // Endpoint para buscar usuario por email
    @GetMapping("/buscar")
    public ResponseEntity<?> buscarPorEmail(@RequestParam String email) {
        Optional<UsuarioDTO> usuario = usuarioService.buscarPorEmail(email);
        return usuario
                .map(u -> ResponseEntity.ok(u))
                .orElse(ResponseEntity.notFound().build());
    }
}