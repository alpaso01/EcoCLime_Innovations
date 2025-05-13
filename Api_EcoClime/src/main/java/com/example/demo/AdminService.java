package com.example.demo;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import java.util.Optional;

@Service
public class AdminService {

    @Autowired
    private AdminRepository adminRepository;

    public Admin registrarAdmin(Admin admin) {
        // Validar el formato del email
        if (!admin.getEmail().matches("ecoadmin_\\d+@clime\\.es")) {
            throw new IllegalArgumentException("El email debe tener el formato ecoadmin_X@clime.es");
        }

        // Validar que el email no esté en uso
        if (adminRepository.existsByEmail(admin.getEmail())) {
            throw new IllegalArgumentException("El email ya está en uso.");
        }

        return adminRepository.save(admin);
    }

    public Admin login(String email, String password) {
        Optional<Admin> optionalAdmin = adminRepository.findByEmail(email);
        if (optionalAdmin.isPresent()) {
            Admin admin = optionalAdmin.get();
            if (admin.getPassword().equals(password)) {
                return admin;
            }
        }
        return null;
    }

    public Admin obtenerAdminPorEmail(String email) {
        Optional<Admin> optionalAdmin = adminRepository.findByEmail(email);
        return optionalAdmin.orElse(null);
    }
} 