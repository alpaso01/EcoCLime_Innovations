package com.example.demo;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import java.util.Optional;

@Service
public class TrabajadorService {

    @Autowired
    private TrabajadorRepository trabajadorRepository;

    public Trabajador registrarTrabajador(Trabajador trabajador) {
        // Validar el formato del email
        if (!trabajador.getEmail().matches("ecotrabajador_\\d+@clime\\.es")) {
            throw new IllegalArgumentException("El email debe tener el formato ecotrabajador_X@clime.es");
        }

        // Validar que el email no esté en uso
        if (trabajadorRepository.existsByEmail(trabajador.getEmail())) {
            throw new IllegalArgumentException("El email ya está en uso.");
        }

        return trabajadorRepository.save(trabajador);
    }

    public Trabajador login(String email, String password) {
        Optional<Trabajador> optionalTrabajador = trabajadorRepository.findByEmail(email);
        if (optionalTrabajador.isPresent()) {
            Trabajador trabajador = optionalTrabajador.get();
            if (trabajador.getPassword().equals(password)) {
                return trabajador;
            }
        }
        return null;
    }

    public Trabajador obtenerTrabajadorPorEmail(String email) {
        Optional<Trabajador> optionalTrabajador = trabajadorRepository.findByEmail(email);
        return optionalTrabajador.orElse(null);
    }
} 