package com.example.demo;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import java.util.List;
import java.util.Optional;

@Service
public class CitaService {

    @Autowired
    private CitaRepository citaRepository;

    @Autowired
    private UsuarioRepository usuarioRepository;

    // Agendar una nueva cita
    public Cita agendarCita(Cita cita, Long usuarioId) {
        Optional<Usuario> usuarioOpt = usuarioRepository.findById(usuarioId);
        if (usuarioOpt.isPresent()) {
            cita.setUsuario(usuarioOpt.get());
            return citaRepository.save(cita);
        }
        return null; // O lanzar una excepción
    }

    // Obtener historial de citas de un usuario
    public List<Cita> obtenerCitasPorUsuario(Long usuarioId) {
        return citaRepository.findByUsuarioId(usuarioId);
    }

    // Anular una cita
    public boolean anularCita(Long citaId) {
        if (citaRepository.existsById(citaId)) {
            citaRepository.deleteById(citaId);
            return true;
        }
        return false;
    }
}
