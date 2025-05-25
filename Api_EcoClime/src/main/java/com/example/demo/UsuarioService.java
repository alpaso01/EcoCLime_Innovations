package com.example.demo;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import java.util.Optional;

@Service
public class UsuarioService {

    @Autowired
    private UsuarioRepository usuarioRepository;
    
    @Autowired
    private TrabajadorRepository trabajadorRepository;
    
    @Autowired
    private AdminRepository adminRepository;

    public boolean existePorEmail(String email) {
        return usuarioRepository.existsByEmail(email)
            || trabajadorRepository.existsByEmail(email)
            || adminRepository.existsByEmail(email);
    }

    //REGISTRO
    public Usuario registrarUsuario(Usuario usuario) {
        // No permitir registro con emails de trabajador o admin
        if (usuario.getEmail().matches("ecotrabajador_\\d+@clime\\.es") || 
            usuario.getEmail().matches("ecoadmin_\\d+@clime\\.es")) {
            throw new IllegalArgumentException("No está permitido registrarse con este tipo de correo.");
        }

        // Validación simple: email no duplicado
        if (usuarioRepository.existsByEmail(usuario.getEmail()) ||
            trabajadorRepository.existsByEmail(usuario.getEmail()) ||
            adminRepository.existsByEmail(usuario.getEmail())) {
            throw new IllegalArgumentException("El email ya está en uso.");
        }
        return usuarioRepository.save(usuario);
    }
    
    //LOGIN
    public Object login(String email, String password) {
        // Primero intentamos con usuario normal
        Optional<Usuario> optionalUsuario = usuarioRepository.findByEmail(email);
        if (optionalUsuario.isPresent()) {
            Usuario usuario = optionalUsuario.get();
            if (usuario.getpassword().equals(password)) {
                return usuario;
            }
        }

        // Si no es usuario normal, intentamos con trabajador
        Optional<Trabajador> optionalTrabajador = trabajadorRepository.findByEmail(email);
        if (optionalTrabajador.isPresent()) {
            Trabajador trabajador = optionalTrabajador.get();
            if (trabajador.getPassword().equals(password)) {
                return trabajador;
            }
        }

        // Si no es trabajador, intentamos con admin
        Optional<Admin> optionalAdmin = adminRepository.findByEmail(email);
        if (optionalAdmin.isPresent()) {
            Admin admin = optionalAdmin.get();
            if (admin.getPassword().equals(password)) {
                return admin;
            }
        }

        return null;
    }

    //OBTENER USUARIO POR EMAIL
    public Object obtenerUsuarioPorEmail(String email) {
        // Primero intentamos con usuario normal
        Optional<Usuario> optionalUsuario = usuarioRepository.findByEmail(email);
        if (optionalUsuario.isPresent()) {
            return optionalUsuario.get();
        }

        // Si no es usuario normal, intentamos con trabajador
        Optional<Trabajador> optionalTrabajador = trabajadorRepository.findByEmail(email);
        if (optionalTrabajador.isPresent()) {
            return optionalTrabajador.get();
        }

        // Si no es trabajador, intentamos con admin
        Optional<Admin> optionalAdmin = adminRepository.findByEmail(email);
        return optionalAdmin.orElse(null);
    }

    public void cambiarPassword(String email, String nuevaPassword) {
        // Primero intentamos con usuario normal
        Optional<Usuario> optionalUsuario = usuarioRepository.findByEmail(email);
        if (optionalUsuario.isPresent()) {
            Usuario usuario = optionalUsuario.get();
            usuario.setpassword(nuevaPassword);
            usuarioRepository.save(usuario);
            return;
        }

        // Si no es usuario normal, intentamos con trabajador
        Optional<Trabajador> optionalTrabajador = trabajadorRepository.findByEmail(email);
        if (optionalTrabajador.isPresent()) {
            Trabajador trabajador = optionalTrabajador.get();
            trabajador.setPassword(nuevaPassword);
            trabajadorRepository.save(trabajador);
            return;
        }

        // Si no es trabajador, intentamos con admin
        Optional<Admin> optionalAdmin = adminRepository.findByEmail(email);
        if (optionalAdmin.isPresent()) {
            Admin admin = optionalAdmin.get();
            admin.setPassword(nuevaPassword);
            adminRepository.save(admin);
            return;
        }

        // Si no se encontró ningún usuario
        throw new IllegalArgumentException("Usuario no encontrado");
    }
}