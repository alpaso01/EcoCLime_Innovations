package com.example.demo;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Service;
import java.time.LocalDateTime;
import java.time.LocalDate;
import java.time.LocalTime;
import java.time.format.DateTimeFormatter;
import java.util.List;
import java.util.Optional;

@Service
public class CitaService {

    @Autowired
    private CitaRepository citaRepository;

    @Autowired
    private UsuarioRepository usuarioRepository;

    // Agendar una nueva cita
    public Cita agendarCita(Cita cita, Integer usuarioId) {
        Optional<Usuario> usuarioOpt = usuarioRepository.findById(usuarioId);
        if (usuarioOpt.isPresent()) {
            cita.setUsuario(usuarioOpt.get());
            return citaRepository.save(cita);
        }
        return null; // O lanzar una excepción
    }

    // Obtener historial de citas de un usuario
    public List<Cita> obtenerCitasPorUsuario(Integer usuarioId) {
        return citaRepository.findByUsuarioId(usuarioId);
    }

    // Obtener citas por email
    public List<Cita> obtenerCitasPorEmail(String email) {
        return citaRepository.findByEmail(email);
    }

    // Anular una cita
    public boolean anularCita(Integer citaId) {
        Optional<Cita> citaOpt = citaRepository.findById(citaId);
        if (citaOpt.isPresent()) {
            citaRepository.deleteById(citaId);
            return true;
        }
        return false;
    }
    
    public Cita crearCita(int usuarioId, Cita cita) {
        Optional<Usuario> usuarioOpt = usuarioRepository.findById(usuarioId);
        if (usuarioOpt.isPresent()) {
            Usuario usuario = usuarioOpt.get();
            cita.setUsuario(usuario);
            cita.setId(null); // Aseguramos que se cree una nueva cita
            
            // Convertir fecha y hora a LocalDateTime
            try {
                LocalDate fecha = LocalDate.parse(cita.getFecha());
                LocalTime hora = LocalTime.parse(cita.getHora());
                LocalDateTime fechaHora = LocalDateTime.of(fecha, hora);
                cita.setFechaHora(fechaHora);
            } catch (Exception e) {
                throw new RuntimeException("Error al procesar la fecha y hora: " + e.getMessage());
            }
            
            return citaRepository.save(cita);
        }
        throw new RuntimeException("Usuario no encontrado");
    }
}