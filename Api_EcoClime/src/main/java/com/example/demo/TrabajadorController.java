package com.example.demo;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

@RestController
@RequestMapping("/api/trabajadores")
@CrossOrigin(origins = "*")
public class TrabajadorController {

    @Autowired
    private TrabajadorService trabajadorService;

    @PostMapping("/registro")
    public ResponseEntity<?> registrarTrabajador(@RequestBody Trabajador trabajador) {
        try {
            Trabajador nuevoTrabajador = trabajadorService.registrarTrabajador(trabajador);
            return ResponseEntity.ok(nuevoTrabajador);
        } catch (IllegalArgumentException e) {
            return ResponseEntity.badRequest().body(e.getMessage());
        }
    }

    @PostMapping("/login")
    public ResponseEntity<?> login(@RequestBody LoginRequest loginRequest) {
        Trabajador trabajador = trabajadorService.login(loginRequest.getEmail(), loginRequest.getPassword());
        if (trabajador != null) {
            return ResponseEntity.ok(trabajador);
        } else {
            return ResponseEntity.status(401).body("Email o contraseña incorrectos.");
        }
    }

    @GetMapping("/{email}")
    public ResponseEntity<?> obtenerTrabajadorPorEmail(@PathVariable String email) {
        Trabajador trabajador = trabajadorService.obtenerTrabajadorPorEmail(email);
        if (trabajador != null) {
            return ResponseEntity.ok(trabajador);
        } else {
            return ResponseEntity.notFound().build();
        }
    }
} 