package com.example.demo;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

@RestController
@RequestMapping("/api/admins")
@CrossOrigin(origins = "*")
public class AdminController {

    @Autowired
    private AdminService adminService;

    @PostMapping("/registro")
    public ResponseEntity<?> registrarAdmin(@RequestBody Admin admin) {
        try {
            Admin nuevoAdmin = adminService.registrarAdmin(admin);
            return ResponseEntity.ok(nuevoAdmin);
        } catch (IllegalArgumentException e) {
            return ResponseEntity.badRequest().body(e.getMessage());
        }
    }

    @PostMapping("/login")
    public ResponseEntity<?> login(@RequestBody LoginRequest loginRequest) {
        Admin admin = adminService.login(loginRequest.getEmail(), loginRequest.getPassword());
        if (admin != null) {
            return ResponseEntity.ok(admin);
        } else {
            return ResponseEntity.status(401).body("Email o contraseña incorrectos.");
        }
    }

    @GetMapping("/{email}")
    public ResponseEntity<?> obtenerAdminPorEmail(@PathVariable String email) {
        Admin admin = adminService.obtenerAdminPorEmail(email);
        if (admin != null) {
            return ResponseEntity.ok(admin);
        } else {
            return ResponseEntity.notFound().build();
        }
    }
} 