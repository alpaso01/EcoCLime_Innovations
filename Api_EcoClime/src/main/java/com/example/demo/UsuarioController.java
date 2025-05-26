package com.example.demo;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import java.util.List;
import java.util.Map;
import java.util.ArrayList;

@RestController
@RequestMapping("api/usuarios")
public class UsuarioController {

    @Autowired
    private UsuarioService usuarioService;

    @Autowired
    private UsuarioRepository usuarioRepository;
    
    @Autowired
    private AdminRepository adminRepository;
    
    @Autowired
    private TrabajadorRepository trabajadorRepository;

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
    public ResponseEntity<List<UsuarioUnificadoDTO>> obtenerTodos() {
        List<UsuarioUnificadoDTO> todos = new ArrayList<>();

        // Clientes
        for (Usuario cliente : usuarioRepository.findAll()) {
            UsuarioUnificadoDTO dto = new UsuarioUnificadoDTO();
            dto.setId(cliente.getId());
            dto.setNombre(cliente.getNombre());
            dto.setApellidos(cliente.getApellidos());
            dto.setEmail(cliente.getEmail());
            dto.setTelefono(cliente.getTelefono());
            dto.setRol("cliente");
            dto.setTipo(cliente.getTipo()); // "particular" o "empresa"
            todos.add(dto);
        }

        // Admins
        for (Admin admin : adminRepository.findAll()) {
            UsuarioUnificadoDTO dto = new UsuarioUnificadoDTO();
            dto.setId(admin.getId());
            dto.setNombre(admin.getNombre());
            dto.setApellidos(admin.getApellidos());
            dto.setEmail(admin.getEmail());
            dto.setTelefono(admin.getTelefono());
            dto.setRol("admin");
            dto.setTipo(null);
            todos.add(dto);
        }

        // Trabajadores
        for (Trabajador trabajador : trabajadorRepository.findAll()) {
            UsuarioUnificadoDTO dto = new UsuarioUnificadoDTO();
            dto.setId(trabajador.getId());
            dto.setNombre(trabajador.getNombre());
            dto.setApellidos(trabajador.getApellidos());
            dto.setEmail(trabajador.getEmail());
            dto.setTelefono(trabajador.getTelefono());
            dto.setRol("trabajador");
            dto.setTipo(null);
            todos.add(dto);
        }

        return ResponseEntity.ok(todos);
    }

    @GetMapping("/trabajadores")
    public ResponseEntity<List<UsuarioUnificadoDTO>> obtenerTrabajadores() {
        List<UsuarioUnificadoDTO> trabajadores = new ArrayList<>();
        for (Trabajador trabajador : trabajadorRepository.findAll()) {
            UsuarioUnificadoDTO dto = new UsuarioUnificadoDTO();
            dto.setId(trabajador.getId());
            dto.setNombre(trabajador.getNombre());
            dto.setApellidos(trabajador.getApellidos());
            dto.setEmail(trabajador.getEmail());
            dto.setTelefono(trabajador.getTelefono());
            dto.setRol("trabajador");
            dto.setTipo(null);
            trabajadores.add(dto);
        }
        return ResponseEntity.ok(trabajadores);
    }

    @GetMapping("/admins")
    public ResponseEntity<List<UsuarioUnificadoDTO>> obtenerAdmins() {
        List<UsuarioUnificadoDTO> admins = new ArrayList<>();
        for (Admin admin : adminRepository.findAll()) {
            UsuarioUnificadoDTO dto = new UsuarioUnificadoDTO();
            dto.setId(admin.getId());
            dto.setNombre(admin.getNombre());
            dto.setApellidos(admin.getApellidos());
            dto.setEmail(admin.getEmail());
            dto.setTelefono(admin.getTelefono());
            dto.setRol("admin");
            dto.setTipo(null);
            admins.add(dto);
        }
        return ResponseEntity.ok(admins);
    }

    @GetMapping("/clientes")
    public ResponseEntity<List<UsuarioUnificadoDTO>> obtenerClientes() {
        List<UsuarioUnificadoDTO> clientes = new ArrayList<>();
        // Obtener todos los usuarios de la tabla usuarios (todos son clientes)
        for (Usuario usuario : usuarioRepository.findAll()) {
            UsuarioUnificadoDTO dto = new UsuarioUnificadoDTO();
            dto.setId(usuario.getId());
            dto.setNombre(usuario.getNombre());
            dto.setApellidos(usuario.getApellidos());
            dto.setEmail(usuario.getEmail());
            dto.setTelefono(usuario.getTelefono());
            dto.setRol("cliente");
            dto.setTipo(usuario.getTipo());
            clientes.add(dto);
        }
        return ResponseEntity.ok(clientes);
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