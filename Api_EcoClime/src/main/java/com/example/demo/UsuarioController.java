package com.example.demo;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import java.util.List;
import java.util.Map;

@RestController
@RequestMapping("api/usuarios")
public class UsuarioController {

    @Autowired
    private UsuarioService usuarioService;

    @Autowired
    private UsuarioRepository usuarioRepository;

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

    // --- HISTORIAL DE USUARIOS ---
    
    @GetMapping("/todos")
    public ResponseEntity<?> obtenerTodos() {
        return ResponseEntity.ok(usuarioRepository.findAll());
    }

    @GetMapping("/trabajadores")
    public ResponseEntity<?> obtenerTrabajadores() {
        return ResponseEntity.ok(usuarioRepository.findByTipo("trabajador"));
    }

    @GetMapping("/admins")
    public ResponseEntity<?> obtenerAdmins() {
        return ResponseEntity.ok(usuarioRepository.findByTipo("admin"));
    }

    @GetMapping("/clientes")
    public ResponseEntity<?> obtenerClientes() {
        return ResponseEntity.ok(usuarioRepository.findByTipo("cliente"));
    }

    @PatchMapping("/{id}")
    @org.springframework.transaction.annotation.Transactional
    public ResponseEntity<?> actualizarUsuarioParcial(@PathVariable int id, @RequestBody Map<String, Object> campos) {
        try {
            System.out.println("PATCH recibido para usuario ID: " + id + " con campos: " + campos);
            
            // Buscar el usuario por ID
            java.util.Optional<Usuario> usuarioOpt = usuarioRepository.findById(id);
            if (!usuarioOpt.isPresent()) {
                System.out.println("Usuario no encontrado con ID: " + id);
                return ResponseEntity.notFound().build();
            }
            
            Usuario usuario = usuarioOpt.get();
            System.out.println("Usuario antes de actualizar: " + usuario.getId() + ", " + usuario.getNombre() + ", " + usuario.getTelefono());
            
            // Actualizar solo los campos recibidos
            campos.forEach((key, value) -> {
                switch (key) {
                    case "nombre":
                        usuario.setNombre((String) value);
                        System.out.println("Actualizando nombre a: " + value);
                        break;
                    case "apellidos":
                        usuario.setApellidos((String) value);
                        System.out.println("Actualizando apellidos a: " + value);
                        break;
                    case "email":
                        usuario.setEmail((String) value);
                        System.out.println("Actualizando email a: " + value);
                        break;
                    case "telefono":
                        usuario.setTelefono((String) value);
                        System.out.println("Actualizando telefono a: " + value);
                        break;
                    case "password":
                        usuario.setpassword((String) value); // Nota: 'p' minúscula
                        System.out.println("Actualizando password");
                        break;
                    case "tipo":
                        usuario.setTipo((String) value);
                        System.out.println("Actualizando tipo a: " + value);
                        break;
                }
            });
            
            // Guardar los cambios
            Usuario usuarioActualizado = usuarioRepository.save(usuario);
            System.out.println("Usuario después de actualizar: " + usuarioActualizado.getId() + ", " + usuarioActualizado.getNombre() + ", " + usuarioActualizado.getTelefono());
            
            return ResponseEntity.ok().build();
            
        } catch (Exception e) {
            System.out.println("Error al actualizar usuario: " + e.getMessage());
            e.printStackTrace();
            return ResponseEntity.status(org.springframework.http.HttpStatus.INTERNAL_SERVER_ERROR)
                    .body("Error al actualizar usuario: " + e.getMessage());
        }
    }
}